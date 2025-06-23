    /**
     * Display security dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get security metrics
            $metrics = [
                'failed_login_attempts' => DB::table('failed_login_attempts')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
                'active_sessions' => DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->count(),
                'security_alerts' => DB::table('security_logs')
                    ->where('user_id', $user->id)
                    ->where('level', 'warning')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'password_last_changed' => $user->password_changed_at ?? $user->created_at,
                'two_factor_enabled' => $user->two_factor_secret ? true : false,
                'last_login' => $user->last_login_at,
                'login_ip' => $user->last_login_ip
            ];
            
            // Get recent security events
            $securityEvents = DB::table('security_logs')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Get active sessions
            $activeSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->select([
                    'id',
                    'ip_address',
                    'user_agent',
                    'last_activity',
                    'created_at'
                ])
                ->orderBy('last_activity', 'desc')
                ->get();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'metrics' => $metrics,
                        'security_events' => $securityEvents,
                        'active_sessions' => $activeSessions
                    ]
                ]);
            }
            
            return view('security.dashboard', compact('metrics', 'securityEvents', 'activeSessions'));
            
        } catch (\Exception $e) {
            Log::error('Security dashboard error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load security dashboard')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to load security dashboard'));
        }
    }

    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user->two_factor_secret) {
                return response()->json([
                    'success' => false,
                    'message' => __('Two-factor authentication is already enabled')
                ], 422);
            }
            
            // Generate secret
            $google2fa = app('pragmarx.google2fa');
            $secret = $google2fa->generateSecretKey();
            
            // Generate QR code
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
            
            // Store secret temporarily (not activated until verified)
            $user->update([
                'two_factor_secret' => encrypt($secret),
                'two_factor_confirmed_at' => null
            ]);
            
            // Generate recovery codes
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $recoveryCodes[] = Str::random(10);
            }
            
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes))
            ]);
            
            Log::info('Two-factor authentication setup initiated', [
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'secret' => $secret,
                    'qr_code_url' => $qrCodeUrl,
                    'recovery_codes' => $recoveryCodes
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Enable two-factor authentication error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to enable two-factor authentication')
            ], 500);
        }
    }

    /**
     * Confirm two-factor authentication
     */
    public function confirmTwoFactor(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|size:6'
            ]);
            
            $user = auth()->user();
            
            if (!$user->two_factor_secret) {
                return response()->json([
                    'success' => false,
                    'message' => __('Two-factor authentication is not set up')
                ], 422);
            }
            
            $google2fa = app('pragmarx.google2fa');
            $secret = decrypt($user->two_factor_secret);
            
            $valid = $google2fa->verifyKey($secret, $validated['code']);
            
            if (!$valid) {
                Log::warning('Invalid two-factor authentication code', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid authentication code')
                ], 422);
            }
            
            // Confirm two-factor authentication
            $user->update([
                'two_factor_confirmed_at' => now()
            ]);
            
            Log::info('Two-factor authentication confirmed', [
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Two-factor authentication enabled successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Confirm two-factor authentication error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to confirm two-factor authentication')
            ], 500);
        }
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|size:6',
                'password' => 'required|string'
            ]);
            
            $user = auth()->user();
            
            // Verify password
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => __('Invalid password')
                ], 422);
            }
            
            // Verify 2FA code
            if ($user->two_factor_secret) {
                $google2fa = app('pragmarx.google2fa');
                $secret = decrypt($user->two_factor_secret);
                
                $valid = $google2fa->verifyKey($secret, $validated['code']);
                
                if (!$valid) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Invalid authentication code')
                    ], 422);
                }
            }
            
            // Remove two-factor authentication
            $user->update([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null
            ]);
            
            Log::info('Two-factor authentication disabled', [
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Two-factor authentication disabled successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Disable two-factor authentication error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to disable two-factor authentication')
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed|different:current_password',
                'new_password_confirmation' => 'required|string'
            ]);
            
            $user = auth()->user();
            
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => __('Current password is incorrect')
                ], 422);
            }
            
            // Check password strength
            $passwordStrength = $this->checkPasswordStrength($validated['new_password']);
            if ($passwordStrength['score'] < 3) {
                return response()->json([
                    'success' => false,
                    'message' => __('Password is too weak. Please choose a stronger password.'),
                    'strength' => $passwordStrength
                ], 422);
            }
            
            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
                'password_changed_at' => now()
            ]);
            
            // Invalidate all other sessions
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', session()->getId())
                ->delete();
            
            Log::info('Password changed successfully', [
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Password changed successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Change password error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to change password')
            ], 500);
        }
    }

    /**
     * Revoke session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        try {
            $user = auth()->user();
            
            $session = DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => __('Session not found')
                ], 404);
            }
            
            // Don't allow revoking current session
            if ($sessionId === session()->getId()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot revoke current session')
                ], 422);
            }
            
            // Delete session
            DB::table('sessions')->where('id', $sessionId)->delete();
            
            Log::info('Session revoked', [
                'session_id' => $sessionId,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Session revoked successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Revoke session error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to revoke session')
            ], 500);
        }
    }

    /**
     * Get security audit log
     */
    public function auditLog(Request $request)
    {
        try {
            $user = auth()->user();
            
            $query = DB::table('security_logs')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($request->has('level')) {
                $query->where('level', $request->get('level'));
            }
            
            if ($request->has('action')) {
                $query->where('action', $request->get('action'));
            }
            
            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->get('date_from'));
            }
            
            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->get('date_to'));
            }
            
            $logs = $query->paginate(50);
            
            return response()->json([
                'success' => true,
                'data' => $logs
            ]);
            
        } catch (\Exception $e) {
            Log::error('Security audit log error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to load audit log')
            ], 500);
        }
    }

    /**
     * Check password strength
     */
    private function checkPasswordStrength($password)
    {
        $score = 0;
        $feedback = [];
        
        // Length check
        if (strlen($password) >= 8) {
            $score++;
        } else {
            $feedback[] = __('Password should be at least 8 characters long');
        }
        
        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        } else {
            $feedback[] = __('Password should contain uppercase letters');
        }
        
        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        } else {
            $feedback[] = __('Password should contain lowercase letters');
        }
        
        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score++;
        } else {
            $feedback[] = __('Password should contain numbers');
        }
        
        // Special character check
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score++;
        } else {
            $feedback[] = __('Password should contain special characters');
        }
        
        $strength = 'very_weak';
        if ($score >= 4) $strength = 'strong';
        elseif ($score >= 3) $strength = 'medium';
        elseif ($score >= 2) $strength = 'weak';
        
        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback
        ];
    }

    /**
     * Report security incident
     */
    public function reportIncident(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:suspicious_activity,phishing,malware,data_breach,other',
                'description' => 'required|string|max:5000',
                'severity' => 'required|in:low,medium,high,critical',
                'evidence' => 'nullable|file|max:10240'
            ]);
            
            $user = auth()->user();
            
            // Store incident report
            $incidentId = DB::table('security_incidents')->insertGetId([
                'user_id' => $user->id,
                'type' => $validated['type'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Handle evidence file
            if ($request->hasFile('evidence')) {
                $file = $request->file('evidence');
                $filename = time() . '_incident_' . $file->getClientOriginalName();
                $path = $file->storeAs('security/incidents', $filename, 'private');
                
                DB::table('security_incidents')
                    ->where('id', $incidentId)
                    ->update(['evidence_path' => $path]);
            }
            
            // Notify security team
            // You can implement email notification here
            
            Log::critical('Security incident reported', [
                'incident_id' => $incidentId,
                'user_id' => $user->id,
                'type' => $validated['type'],
                'severity' => $validated['severity']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Security incident reported successfully'),
                'incident_id' => $incidentId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Report security incident error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to report security incident')
            ], 500);
        }
    }

    /**
     * Get security recommendations
     */
    public function getRecommendations(Request $request)
    {
        try {
            $user = auth()->user();
            $recommendations = [];
            
            // Check two-factor authentication
            if (!$user->two_factor_secret) {
                $recommendations[] = [
                    'type' => 'two_factor',
                    'priority' => 'high',
                    'title' => __('Enable Two-Factor Authentication'),
                    'description' => __('Add an extra layer of security to your account'),
                    'action' => 'enable_2fa'
                ];
            }
            
            // Check password age
            $passwordAge = $user->password_changed_at ? 
                now()->diffInDays($user->password_changed_at) : 365;
            
            if ($passwordAge > 90) {
                $recommendations[] = [
                    'type' => 'password_age',
                    'priority' => 'medium',
                    'title' => __('Update Your Password'),
                    'description' => __('Your password is older than 90 days'),
                    'action' => 'change_password'
                ];
            }
            
            // Check for suspicious activity
            $suspiciousActivity = DB::table('security_logs')
                ->where('user_id', $user->id)
                ->where('level', 'warning')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            
            if ($suspiciousActivity > 0) {
                $recommendations[] = [
                    'type' => 'suspicious_activity',
                    'priority' => 'high',
                    'title' => __('Review Recent Activity'),
                    'description' => __('Suspicious activity detected in your account'),
                    'action' => 'review_activity'
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get security recommendations error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to load recommendations')
            ], 500);
        }
    } 