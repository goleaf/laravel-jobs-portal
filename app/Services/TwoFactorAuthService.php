<?php

namespace App\Services;

use App\Mail\LowBackupCodesNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class TwoFactorAuthService
{
    protected const BACKUP_CODES_COUNT = 8;
    protected const BACKUP_CODE_LENGTH = 8;
    protected const RATE_LIMIT_ATTEMPTS = 5;
    protected const RATE_LIMIT_DECAY = 15; // minutes

    /**
     * Generate a new 2FA secret for user.
     *
     * @param mixed $user
     */
    public function generateSecret($user): string
    {
        $secret = random_bytes(32);
        $encodedSecret = Base32::encodeUpper($secret);

        // Store encrypted secret
        $user->update([
            'two_factor_secret' => Crypt::encrypt($encodedSecret),
            'two_factor_enabled' => false, // Not enabled until confirmed
        ]);

        $this->logSecurityEvent('2fa_secret_generated', $user);

        return $encodedSecret;
    }

    /**
     * Generate QR code data for 2FA setup.
     *
     * @param mixed $user
     */
    public function generateQrCodeData($user, string $secret): string
    {
        $totp = TOTP::create($secret);
        $totp->setLabel($user->email);
        $totp->setIssuer(config('app.name', 'Laravel App'));

        return $totp->getProvisioningUri();
    }

    /**
     * Verify TOTP code and enable 2FA.
     *
     * @param mixed $user
     */
    public function enableTwoFactor($user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        // Check rate limiting
        if ($this->isRateLimited($user, '2fa_verify')) {
            $this->logSecurityEvent('2fa_enable_rate_limited', $user, ['code' => substr($code, 0, 2).'****']);

            return false;
        }

        $secret = Crypt::decrypt($user->two_factor_secret);

        if ($this->verifyCode($secret, $code)) {
            // Generate backup codes
            $backupCodes = $this->generateBackupCodes();

            $user->update([
                'two_factor_enabled' => true,
                'two_factor_backup_codes' => Crypt::encrypt(json_encode($backupCodes)),
                'two_factor_enabled_at' => now(),
            ]);

            $this->clearRateLimit($user, '2fa_verify');
            $this->logSecurityEvent('2fa_enabled', $user);

            return true;
        }

        $this->recordFailedAttempt($user, '2fa_verify');
        $this->logSecurityEvent('2fa_enable_failed', $user, ['code' => substr($code, 0, 2).'****']);

        return false;
    }

    /**
     * Disable 2FA for user.
     *
     * @param mixed $user
     */
    public function disableTwoFactor($user): bool
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_enabled_at' => null,
        ]);

        $this->logSecurityEvent('2fa_disabled', $user);

        return true;
    }

    /**
     * Verify 2FA code during login.
     *
     * @param mixed $user
     */
    public function verifyLogin($user, string $code): bool
    {
        if (!$user->two_factor_enabled) {
            return true; // 2FA not enabled
        }

        // Check rate limiting
        if ($this->isRateLimited($user, '2fa_login')) {
            $this->logSecurityEvent('2fa_login_rate_limited', $user, ['code' => substr($code, 0, 2).'****']);

            return false;
        }

        // Try TOTP first
        if ($this->verifyTOTP($user, $code)) {
            $this->clearRateLimit($user, '2fa_login');
            $this->logSecurityEvent('2fa_login_success', $user, ['method' => 'totp']);

            return true;
        }

        // Try backup code
        if ($this->verifyBackupCode($user, $code)) {
            $this->clearRateLimit($user, '2fa_login');
            $this->logSecurityEvent('2fa_login_success', $user, ['method' => 'backup_code']);

            return true;
        }

        $this->recordFailedAttempt($user, '2fa_login');
        $this->logSecurityEvent('2fa_login_failed', $user, ['code' => substr($code, 0, 2).'****']);

        return false;
    }

    /**
     * Generate new backup codes.
     *
     * @param mixed $user
     */
    public function generateNewBackupCodes($user): array
    {
        if (!$user->two_factor_enabled) {
            throw new \Exception('2FA must be enabled to generate backup codes');
        }

        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_backup_codes' => Crypt::encrypt(json_encode($backupCodes)),
        ]);

        $this->logSecurityEvent('2fa_backup_codes_regenerated', $user);

        // Return plain codes for display (they're already hashed in storage)
        return array_map(function ($code) {
            return Str::random(self::BACKUP_CODE_LENGTH);
        }, $backupCodes);
    }

    /**
     * Get remaining backup codes count.
     *
     * @param mixed $user
     */
    public function getRemainingBackupCodesCount($user): int
    {
        if (!$user->two_factor_backup_codes) {
            return 0;
        }

        try {
            $backupCodes = json_decode(Crypt::decrypt($user->two_factor_backup_codes), true);

            return is_array($backupCodes) ? count($backupCodes) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check if 2FA is required for user.
     *
     * @param mixed $user
     */
    public function isRequired($user): bool
    {
        // Check if globally enabled
        if (!config('security.authentication.enable_2fa', false)) {
            return false;
        }

        // Force for admin users if configured
        if (config('security.authentication.force_2fa_for_admin', true)) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        }

        return $user->two_factor_enabled ?? false;
    }

    /**
     * Generate recovery codes for account recovery.
     *
     * @param mixed $user
     */
    public function generateRecoveryCodes($user): array
    {
        $codes = [];

        for ($i = 0; $i < 5; ++$i) {
            $codes[] = Str::random(16);
        }

        // Store hashed recovery codes
        $hashedCodes = array_map(fn ($code) => Hash::make($code), $codes);

        $user->update([
            'two_factor_recovery_codes' => Crypt::encrypt(json_encode($hashedCodes)),
            'two_factor_recovery_codes_generated_at' => now(),
        ]);

        $this->logSecurityEvent('2fa_recovery_codes_generated', $user);

        return $codes; // Return plain codes for user to save
    }

    /**
     * Verify recovery code and disable 2FA.
     *
     * @param mixed $user
     */
    public function verifyRecoveryCode($user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        // Check rate limiting
        if ($this->isRateLimited($user, '2fa_recovery')) {
            $this->logSecurityEvent('2fa_recovery_rate_limited', $user);

            return false;
        }

        try {
            $recoveryCodes = json_decode(Crypt::decrypt($user->two_factor_recovery_codes), true);

            if (!is_array($recoveryCodes)) {
                return false;
            }

            foreach ($recoveryCodes as $hashedCode) {
                if (Hash::check($code, $hashedCode)) {
                    // Disable 2FA and clear all related data
                    $user->update([
                        'two_factor_enabled' => false,
                        'two_factor_secret' => null,
                        'two_factor_backup_codes' => null,
                        'two_factor_recovery_codes' => null,
                        'two_factor_enabled_at' => null,
                        'two_factor_recovery_codes_generated_at' => null,
                    ]);

                    $this->clearRateLimit($user, '2fa_recovery');
                    $this->logSecurityEvent('2fa_recovered', $user);

                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('2FA recovery code verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->recordFailedAttempt($user, '2fa_recovery');
        $this->logSecurityEvent('2fa_recovery_failed', $user);

        return false;
    }

    /**
     * Get rate limit remaining time.
     *
     * @param mixed $user
     */
    public function getRateLimitRemainingTime($user, string $action): int
    {
        $key = "2fa_rate_limit:{$action}:{$user->id}";

        if (!Cache::has($key)) {
            return 0;
        }

        $ttl = Cache::getStore()->getRedis()->ttl(
            Cache::getStore()->getPrefix().$key
        );

        return max(0, $ttl);
    }

    /**
     * Get 2FA statistics for user.
     *
     * @param mixed $user
     */
    public function getStatistics($user): array
    {
        return [
            'enabled' => (bool) $user->two_factor_enabled,
            'enabled_at' => $user->two_factor_enabled_at,
            'backup_codes_count' => $this->getRemainingBackupCodesCount($user),
            'recovery_codes_available' => !empty($user->two_factor_recovery_codes),
            'recovery_codes_generated_at' => $user->two_factor_recovery_codes_generated_at,
            'is_required' => $this->isRequired($user),
        ];
    }

    /**
     * Validate 2FA setup requirements.
     *
     * @param mixed $user
     */
    public function validateSetupRequirements($user): array
    {
        $errors = [];

        // Check if user has verified email
        if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            $errors[] = 'Email address must be verified before enabling 2FA';
        }

        // Check if user has strong password
        if (strlen($user->password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        return $errors;
    }

    /**
     * Test 2FA configuration.
     */
    public function testConfiguration(): array
    {
        $results = [
            'status' => 'ok',
            'checks' => [],
        ];

        // Test secret generation
        try {
            $secret = random_bytes(32);
            $encoded = Base32::encodeUpper($secret);
            $results['checks']['secret_generation'] = 'ok';
        } catch (\Exception $e) {
            $results['checks']['secret_generation'] = 'failed: '.$e->getMessage();
            $results['status'] = 'error';
        }

        // Test TOTP generation
        try {
            $totp = TOTP::create($encoded ?? 'TESTSECRET');
            $code = $totp->now();
            $results['checks']['totp_generation'] = 'ok';
        } catch (\Exception $e) {
            $results['checks']['totp_generation'] = 'failed: '.$e->getMessage();
            $results['status'] = 'error';
        }

        // Test encryption
        try {
            $encrypted = Crypt::encrypt('test');
            $decrypted = Crypt::decrypt($encrypted);
            $results['checks']['encryption'] = 'test' === $decrypted ? 'ok' : 'failed';
        } catch (\Exception $e) {
            $results['checks']['encryption'] = 'failed: '.$e->getMessage();
            $results['status'] = 'error';
        }

        return $results;
    }

    /**
     * Verify TOTP code.
     *
     * @param mixed $user
     */
    protected function verifyTOTP($user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        try {
            $secret = Crypt::decrypt($user->two_factor_secret);

            return $this->verifyCode($secret, $code);
        } catch (\Exception $e) {
            Log::error('2FA TOTP verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Verify backup code.
     *
     * @param mixed $user
     */
    protected function verifyBackupCode($user, string $code): bool
    {
        if (!$user->two_factor_backup_codes) {
            return false;
        }

        try {
            $backupCodes = json_decode(Crypt::decrypt($user->two_factor_backup_codes), true);

            if (!is_array($backupCodes)) {
                return false;
            }

            // Convert to collection for enhanced manipulation
            $backupCodesCollection = collect($backupCodes);

            foreach ($backupCodesCollection as $index => $hashedCode) {
                if (Hash::check($code, $hashedCode)) {
                    // Enhanced removal with forget() - maintains collection integrity
                    $backupCodesCollection->forget($index);

                    $user->update([
                        'two_factor_backup_codes' => Crypt::encrypt($backupCodesCollection->values()->toJson()),
                    ]);

                    // Enhanced low backup codes alert with better notification
                    if ($backupCodesCollection->count() <= 2) {
                        $this->sendLowBackupCodesAlert($user, $backupCodesCollection->count());
                    }

                    // Log successful backup code usage
                    $this->logSecurityEvent('2fa_backup_code_used', $user, [
                        'remaining' => $backupCodesCollection->count(),
                        'code_index' => $index,
                    ]);

                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::error('2FA backup code verification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Verify TOTP code against secret.
     */
    protected function verifyCode(string $secret, string $code): bool
    {
        try {
            $totp = TOTP::create($secret);

            // Allow some time drift (previous, current, next window)
            $timestamp = time();

            for ($i = -1; $i <= 1; ++$i) {
                $timeSlice = $timestamp + ($i * 30); // 30-second windows
                if ($totp->verify($code, $timeSlice)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('TOTP code verification failed', [
                'error' => $e->getMessage(),
                'code_length' => strlen($code),
            ]);

            return false;
        }
    }

    /**
     * Generate backup codes.
     */
    protected function generateBackupCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < self::BACKUP_CODES_COUNT; ++$i) {
            $code = Str::random(self::BACKUP_CODE_LENGTH);
            $codes[] = Hash::make($code);
        }

        return $codes;
    }

    /**
     * Check if user is rate limited for specific action.
     *
     * @param mixed $user
     */
    protected function isRateLimited($user, string $action): bool
    {
        $key = "2fa_rate_limit:{$action}:{$user->id}";
        $attempts = Cache::get($key, 0);

        return $attempts >= self::RATE_LIMIT_ATTEMPTS;
    }

    /**
     * Record failed attempt for rate limiting.
     *
     * @param mixed $user
     */
    protected function recordFailedAttempt($user, string $action): void
    {
        $key = "2fa_rate_limit:{$action}:{$user->id}";
        $attempts = Cache::get($key, 0) + 1;

        Cache::put($key, $attempts, now()->addMinutes(self::RATE_LIMIT_DECAY));
    }

    /**
     * Clear rate limit for user and action.
     *
     * @param mixed $user
     */
    protected function clearRateLimit($user, string $action): void
    {
        $key = "2fa_rate_limit:{$action}:{$user->id}";
        Cache::forget($key);
    }

    /**
     * Log security events related to 2FA.
     *
     * @param mixed $user
     */
    protected function logSecurityEvent(string $event, $user, array $context = []): void
    {
        Log::channel('security')->info("2FA Event: {$event}", array_merge([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()->toISOString(),
        ], $context));
    }

    /**
     * Send enhanced alert when backup codes are running low.
     *
     * @param mixed $user
     */
    protected function sendLowBackupCodesAlert($user, int $remainingCount): void
    {
        // Log the security event
        $this->logSecurityEvent('2fa_backup_codes_low', $user, ['remaining' => $remainingCount]);

        // Send email notification if user has email
        if ($user->email) {
            try {
                \Mail::to($user->email)->send(new LowBackupCodesNotification($user, $remainingCount));
            } catch (\Exception $e) {
                \Log::warning('Failed to send low backup codes email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Create in-app notification
        if (method_exists($user, 'notifications')) {
            $user->notifications()->create([
                'type' => 'low_backup_codes',
                'data' => [
                    'message' => "You have only {$remainingCount} backup codes remaining. Consider generating new ones.",
                    'remaining_codes' => $remainingCount,
                    'action_url' => route('profile.2fa.backup-codes'),
                ],
                'read_at' => null,
            ]);
        }
    }
}
