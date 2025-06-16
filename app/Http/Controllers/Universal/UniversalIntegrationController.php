    /**
     * Display integrations dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get available integrations
            $availableIntegrations = [
                'linkedin' => [
                    'name' => 'LinkedIn',
                    'description' => 'Import jobs and candidate profiles from LinkedIn',
                    'status' => 'available',
                    'features' => ['job_import', 'profile_sync', 'auto_posting'],
                    'pricing' => 'Premium'
                ],
                'indeed' => [
                    'name' => 'Indeed',
                    'description' => 'Post jobs automatically to Indeed',
                    'status' => 'available',
                    'features' => ['job_posting', 'application_sync'],
                    'pricing' => 'Free'
                ],
                'glassdoor' => [
                    'name' => 'Glassdoor',
                    'description' => 'Sync company reviews and salary data',
                    'status' => 'available',
                    'features' => ['review_sync', 'salary_data'],
                    'pricing' => 'Professional'
                ],
                'slack' => [
                    'name' => 'Slack',
                    'description' => 'Get notifications in your Slack workspace',
                    'status' => 'available',
                    'features' => ['notifications', 'bot_commands'],
                    'pricing' => 'Free'
                ],
                'zapier' => [
                    'name' => 'Zapier',
                    'description' => 'Connect with 3000+ apps via Zapier',
                    'status' => 'available',
                    'features' => ['workflow_automation', 'data_sync'],
                    'pricing' => 'Varies'
                ]
            ];
            
            // Get user's active integrations
            $activeIntegrations = DB::table('user_integrations')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->get();
            
            // Get integration logs
            $integrationLogs = DB::table('integration_logs')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            // Get webhook endpoints
            $webhooks = DB::table('webhooks')
                ->where('user_id', $user->id)
                ->get();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'available_integrations' => $availableIntegrations,
                        'active_integrations' => $activeIntegrations,
                        'integration_logs' => $integrationLogs,
                        'webhooks' => $webhooks
                    ]
                ]);
            }
            
            return view('integrations.dashboard', compact(
                'availableIntegrations', 
                'activeIntegrations', 
                'integrationLogs', 
                'webhooks'
            ));
            
        } catch (\Exception $e) {
            Log::error('Integrations dashboard error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load integrations dashboard')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to load integrations dashboard'));
        }
    }

    /**
     * Connect to LinkedIn integration
     */
    public function connectLinkedIn(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Check if already connected
            $existingIntegration = DB::table('user_integrations')
                ->where('user_id', $user->id)
                ->where('provider', 'linkedin')
                ->where('status', 'active')
                ->first();
            
            if ($existingIntegration) {
                return response()->json([
                    'success' => false,
                    'message' => __('LinkedIn integration is already active')
                ], 422);
            }
            
            // Generate LinkedIn OAuth URL
            $clientId = config('services.linkedin.client_id');
            $redirectUri = route('integrations.linkedin.callback');
            $state = Str::random(40);
            
            // Store state for verification
            session(['linkedin_oauth_state' => $state]);
            
            $scope = 'r_liteprofile r_emailaddress w_member_social';
            $authUrl = "https://www.linkedin.com/oauth/v2/authorization?" . http_build_query([
                'response_type' => 'code',
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'state' => $state,
                'scope' => $scope
            ]);
            
            Log::info('LinkedIn OAuth initiated', [
                'user_id' => $user->id,
                'state' => $state
            ]);
            
            return response()->json([
                'success' => true,
                'auth_url' => $authUrl
            ]);
            
        } catch (\Exception $e) {
            Log::error('LinkedIn connect error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to connect to LinkedIn')
            ], 500);
        }
    }

    /**
     * Handle LinkedIn OAuth callback
     */
    public function linkedInCallback(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Verify state parameter
            if ($request->get('state') !== session('linkedin_oauth_state')) {
                throw new \Exception('Invalid OAuth state');
            }
            
            // Exchange code for access token
            $code = $request->get('code');
            $tokenResponse = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => route('integrations.linkedin.callback'),
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret')
            ]);
            
            if (!$tokenResponse->successful()) {
                throw new \Exception('Failed to get access token');
            }
            
            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];
            
            // Get LinkedIn profile
            $profileResponse = Http::withToken($accessToken)
                ->get('https://api.linkedin.com/v2/people/~', [
                    'projection' => '(id,localizedFirstName,localizedLastName,profilePicture(displayImage~:playableStreams))'
                ]);
            
            if (!$profileResponse->successful()) {
                throw new \Exception('Failed to get LinkedIn profile');
            }
            
            $profileData = $profileResponse->json();
            
            // Store integration
            DB::table('user_integrations')->insert([
                'user_id' => $user->id,
                'provider' => 'linkedin',
                'provider_id' => $profileData['id'],
                'access_token' => encrypt($accessToken),
                'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
                'expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'profile_data' => json_encode($profileData),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Clear OAuth state
            session()->forget('linkedin_oauth_state');
            
            Log::info('LinkedIn integration connected', [
                'user_id' => $user->id,
                'linkedin_id' => $profileData['id']
            ]);
            
            return redirect()->route('integrations.dashboard')
                          ->with('success', __('LinkedIn integration connected successfully'));
            
        } catch (\Exception $e) {
            Log::error('LinkedIn callback error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'code' => $request->get('code')
            ]);
            
            return redirect()->route('integrations.dashboard')
                          ->with('error', __('Failed to connect LinkedIn integration'));
        }
    }

    /**
     * Create webhook endpoint
     */
    public function createWebhook(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|url',
                'events' => 'required|array',
                'events.*' => 'in:job.created,job.updated,application.received,user.registered',
                'secret' => 'nullable|string|max:255',
                'active' => 'boolean'
            ]);
            
            $user = auth()->user();
            
            // Generate webhook ID
            $webhookId = Str::uuid();
            
            // Insert webhook
            DB::table('webhooks')->insert([
                'id' => $webhookId,
                'user_id' => $user->id,
                'name' => $validated['name'],
                'url' => $validated['url'],
                'events' => json_encode($validated['events']),
                'secret' => $validated['secret'] ? encrypt($validated['secret']) : null,
                'active' => $validated['active'] ?? true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('Webhook created', [
                'webhook_id' => $webhookId,
                'user_id' => $user->id,
                'url' => $validated['url']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Webhook created successfully'),
                'webhook_id' => $webhookId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Create webhook error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to create webhook')
            ], 500);
        }
    }

    /**
     * Test webhook endpoint
     */
    public function testWebhook(Request $request, $webhookId)
    {
        try {
            $user = auth()->user();
            
            $webhook = DB::table('webhooks')
                ->where('id', $webhookId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$webhook) {
                return response()->json([
                    'success' => false,
                    'message' => __('Webhook not found')
                ], 404);
            }
            
            // Prepare test payload
            $testPayload = [
                'event' => 'test',
                'webhook_id' => $webhookId,
                'timestamp' => now()->toISOString(),
                'data' => [
                    'message' => 'This is a test webhook delivery',
                    'test' => true
                ]
            ];
            
            // Send webhook
            $response = $this->sendWebhook($webhook, $testPayload);
            
            // Log webhook delivery
            DB::table('webhook_deliveries')->insert([
                'webhook_id' => $webhookId,
                'event' => 'test',
                'payload' => json_encode($testPayload),
                'response_status' => $response['status'],
                'response_body' => $response['body'],
                'delivered_at' => now(),
                'created_at' => now()
            ]);
            
            Log::info('Webhook tested', [
                'webhook_id' => $webhookId,
                'user_id' => $user->id,
                'status' => $response['status']
            ]);
            
            return response()->json([
                'success' => $response['status'] >= 200 && $response['status'] < 300,
                'message' => $response['status'] >= 200 && $response['status'] < 300 
                    ? __('Webhook test successful') 
                    : __('Webhook test failed'),
                'response' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test webhook error', [
                'error' => $e->getMessage(),
                'webhook_id' => $webhookId,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to test webhook')
            ], 500);
        }
    }

    /**
     * Sync data from integration
     */
    public function syncData(Request $request, $provider)
    {
        try {
            $user = auth()->user();
            
            $integration = DB::table('user_integrations')
                ->where('user_id', $user->id)
                ->where('provider', $provider)
                ->where('status', 'active')
                ->first();
            
            if (!$integration) {
                return response()->json([
                    'success' => false,
                    'message' => __('Integration not found or inactive')
                ], 404);
            }
            
            $syncResults = [];
            
            switch ($provider) {
                case 'linkedin':
                    $syncResults = $this->syncLinkedInData($integration);
                    break;
                case 'indeed':
                    $syncResults = $this->syncIndeedData($integration);
                    break;
                default:
                    throw new \Exception('Unsupported provider');
            }
            
            // Log sync operation
            DB::table('integration_logs')->insert([
                'user_id' => $user->id,
                'provider' => $provider,
                'operation' => 'sync_data',
                'status' => 'success',
                'details' => json_encode($syncResults),
                'created_at' => now()
            ]);
            
            Log::info('Data sync completed', [
                'user_id' => $user->id,
                'provider' => $provider,
                'results' => $syncResults
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Data sync completed successfully'),
                'results' => $syncResults
            ]);
            
        } catch (\Exception $e) {
            // Log failed sync
            DB::table('integration_logs')->insert([
                'user_id' => auth()->id(),
                'provider' => $provider,
                'operation' => 'sync_data',
                'status' => 'failed',
                'details' => json_encode(['error' => $e->getMessage()]),
                'created_at' => now()
            ]);
            
            Log::error('Data sync error', [
                'error' => $e->getMessage(),
                'provider' => $provider,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Data sync failed')
            ], 500);
        }
    }

    /**
     * Disconnect integration
     */
    public function disconnect(Request $request, $provider)
    {
        try {
            $user = auth()->user();
            
            $updated = DB::table('user_integrations')
                ->where('user_id', $user->id)
                ->where('provider', $provider)
                ->update([
                    'status' => 'disconnected',
                    'disconnected_at' => now(),
                    'updated_at' => now()
                ]);
            
            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => __('Integration not found')
                ], 404);
            }
            
            Log::info('Integration disconnected', [
                'user_id' => $user->id,
                'provider' => $provider
            ]);
            
            return response()->json([
                'success' => true,
                'message' => __('Integration disconnected successfully')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Disconnect integration error', [
                'error' => $e->getMessage(),
                'provider' => $provider,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to disconnect integration')
            ], 500);
        }
    }

    /**
     * Send webhook payload
     */
    private function sendWebhook($webhook, $payload)
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'User-Agent' => 'JobPortal-Webhook/1.0'
            ];
            
            // Add signature if secret is provided
            if ($webhook->secret) {
                $secret = decrypt($webhook->secret);
                $signature = hash_hmac('sha256', json_encode($payload), $secret);
                $headers['X-Webhook-Signature'] = 'sha256=' . $signature;
            }
            
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($webhook->url, $payload);
            
            return [
                'status' => $response->status(),
                'body' => $response->body()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'body' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync LinkedIn data
     */
    private function syncLinkedInData($integration)
    {
        // Implement LinkedIn-specific sync logic
        return [
            'profiles_synced' => 0,
            'jobs_imported' => 0,
            'last_sync' => now()->toISOString()
        ];
    }

    /**
     * Sync Indeed data
     */
    private function syncIndeedData($integration)
    {
        // Implement Indeed-specific sync logic
        return [
            'jobs_posted' => 0,
            'applications_synced' => 0,
            'last_sync' => now()->toISOString()
        ];
    }

    /**
     * Get API documentation
     */
    public function apiDocs(Request $request)
    {
        try {
            $endpoints = [
                'GET /api/jobs' => [
                    'description' => 'Get list of jobs',
                    'parameters' => ['page', 'limit', 'category', 'location'],
                    'authentication' => 'Optional'
                ],
                'POST /api/jobs' => [
                    'description' => 'Create new job',
                    'parameters' => ['title', 'description', 'category_id', 'company_id'],
                    'authentication' => 'Required'
                ],
                'GET /api/applications' => [
                    'description' => 'Get job applications',
                    'parameters' => ['page', 'limit', 'status'],
                    'authentication' => 'Required'
                ],
                'POST /api/applications' => [
                    'description' => 'Submit job application',
                    'parameters' => ['job_id', 'resume_id', 'cover_letter'],
                    'authentication' => 'Required'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'version' => '1.0',
                    'base_url' => config('app.url') . '/api',
                    'authentication' => 'Bearer Token',
                    'endpoints' => $endpoints
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('API docs error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('Unable to load API documentation')
            ], 500);
        }
    } 