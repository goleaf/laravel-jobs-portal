<?php

namespace Tests\Unit\Universal;

use Tests\TestCase;
use App\Services\Universal\UniversalAISecurityService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;

class UniversalAISecurityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $aiSecurityService;
    protected $mockRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aiSecurityService = new UniversalAISecurityService();
        $this->mockRequest = Mockery::mock(Request::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_analyze_threat_level_returns_proper_structure()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0 Test Browser');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('threat_score', $result);
        $this->assertArrayHasKey('risk_level', $result);
        $this->assertArrayHasKey('factors', $result);
        $this->assertArrayHasKey('recommendations', $result);
        $this->assertArrayHasKey('timestamp', $result);
    }

    /** @test */
    public function test_threat_score_is_numeric_and_within_bounds()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0 Test Browser');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertIsNumeric($result['threat_score']);
        $this->assertGreaterThanOrEqual(0, $result['threat_score']);
        $this->assertLessThanOrEqual(100, $result['threat_score']);
    }

    /** @test */
    public function test_risk_level_calculation_for_different_scores()
    {
        // Mock a minimal request to test risk level calculation
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $service = new class extends UniversalAISecurityService {
            public function testCalculateRiskLevel($score) {
                return $this->calculateRiskLevel($score);
            }
        };

        $this->assertEquals('minimal', $service->testCalculateRiskLevel(10));
        $this->assertEquals('low', $service->testCalculateRiskLevel(25));
        $this->assertEquals('medium', $service->testCalculateRiskLevel(45));
        $this->assertEquals('high', $service->testCalculateRiskLevel(65));
        $this->assertEquals('critical', $service->testCalculateRiskLevel(85));
    }

    /** @test */
    public function test_behavioral_analysis_detects_high_request_frequency()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        // Mock high request frequency
        Cache::put('user_requests:' . auth()->id(), [
            'requests_per_minute' => 50, // High frequency
            'patterns' => ['GET /api/test'],
            'timestamps' => [now(), now()->subSeconds(30)]
        ], 300);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertGreaterThan(0, $result['threat_score']);
        $this->assertContains('high_request_frequency', $result['factors']['behavioral']['factors'] ?? []);
    }

    /** @test */
    public function test_sql_injection_pattern_detection()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([
            'search' => "'; DROP TABLE users; --"
        ]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertGreaterThan(50, $result['threat_score']); // Should be high threat
        $this->assertEquals('critical', $result['risk_level']);
    }

    /** @test */
    public function test_xss_pattern_detection()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([
            'comment' => '<script>alert("xss")</script>'
        ]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertGreaterThan(0, $result['threat_score']);
        $this->assertContains('xss_patterns', $result['factors']['request_patterns']['factors'] ?? []);
    }

    /** @test */
    public function test_machine_learning_prediction_integration()
    {
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('test content');
        $this->mockRequest->shouldReceive('headers->all')->andReturn(['User-Agent' => 'Mozilla/5.0']);
        $this->mockRequest->shouldReceive('isMethod')->with('POST')->andReturn(true);
        $this->mockRequest->shouldReceive('hasHeader')->with('User-Agent')->andReturn(true);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(false);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertArrayHasKey('ml_prediction', $result['factors']);
        $this->assertArrayHasKey('score', $result['factors']['ml_prediction']);
        $this->assertArrayHasKey('confidence', $result['factors']['ml_prediction']);
    }

    /** @test */
    public function test_feature_extraction_for_ml_model()
    {
        $this->mockRequest->shouldReceive('getContent')->andReturn('test content');
        $this->mockRequest->shouldReceive('all')->andReturn(['param1' => 'value1']);
        $this->mockRequest->shouldReceive('headers->all')->andReturn(['User-Agent' => 'Mozilla/5.0']);
        $this->mockRequest->shouldReceive('isMethod')->with('POST')->andReturn(true);
        $this->mockRequest->shouldReceive('hasHeader')->with('User-Agent')->andReturn(true);
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');

        $threatFactors = [
            'behavioral' => ['score' => 10],
            'ip_reputation' => ['score' => 5],
            'request_patterns' => ['score' => 0]
        ];

        $service = new class extends UniversalAISecurityService {
            public function testExtractMLFeatures($request, $factors) {
                return $this->extractMLFeatures($request, $factors);
            }
        };

        $features = $service->testExtractMLFeatures($this->mockRequest, $threatFactors);

        $this->assertIsArray($features);
        $this->assertArrayHasKey('request_size', $features);
        $this->assertArrayHasKey('parameter_count', $features);
        $this->assertArrayHasKey('behavioral_score', $features);
        $this->assertArrayHasKey('ip_reputation_score', $features);
        $this->assertArrayHasKey('is_authenticated', $features);
    }

    /** @test */
    public function test_decision_tree_model_prediction()
    {
        $features = [
            'pattern_score' => 60,
            'ip_reputation_score' => 40,
            'behavioral_score' => 30,
            'hour_of_day' => 3, // Late night
            'is_authenticated' => 1,
            'user_age_days' => 0 // New user
        ];

        $service = new class extends UniversalAISecurityService {
            public function testSimpleDecisionTree($features) {
                return $this->simpleDecisionTree($features);
            }
        };

        $prediction = $service->testSimpleDecisionTree($features);

        $this->assertIsArray($prediction);
        $this->assertArrayHasKey('score', $prediction);
        $this->assertArrayHasKey('confidence', $prediction);
        $this->assertGreaterThan(0, $prediction['score']);
        $this->assertLessThanOrEqual(1.0, $prediction['confidence']);
    }

    /** @test */
    public function test_ensemble_voting_combines_multiple_models()
    {
        $features = [
            'pattern_score' => 30,
            'ip_reputation_score' => 20,
            'behavioral_score' => 15
        ];

        $service = new class extends UniversalAISecurityService {
            public function testEnsembleVoting($features) {
                return $this->ensembleVoting($features);
            }
        };

        $result = $service->testEnsembleVoting($features);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('model_results', $result);
        $this->assertCount(3, $result['model_results']); // Three models
    }

    /** @test */
    public function test_security_recommendations_generation()
    {
        $threatFactors = [
            'behavioral' => [
                'factors' => ['high_request_frequency', 'suspicious_patterns']
            ],
            'ip_reputation' => [
                'factors' => ['proxy_usage']
            ],
            'request_patterns' => [
                'factors' => ['sql_injection_patterns']
            ]
        ];

        $service = new class extends UniversalAISecurityService {
            public function testGenerateSecurityRecommendations($riskLevel, $factors) {
                return $this->generateSecurityRecommendations($riskLevel, $factors);
            }
        };

        $recommendations = $service->testGenerateSecurityRecommendations('high', $threatFactors);

        $this->assertIsArray($recommendations);
        $this->assertNotEmpty($recommendations);
        $this->assertContains('Require additional authentication', $recommendations);
        $this->assertContains('Enable SQL injection protection', $recommendations);
    }

    /** @test */
    public function test_handles_analysis_errors_gracefully()
    {
        // Create a mock request that will cause an exception
        $badRequest = Mockery::mock(Request::class);
        $badRequest->shouldReceive('ip')->andThrow(new \Exception('Network error'));

        $result = $this->aiSecurityService->analyzeThreatLevel($badRequest);

        $this->assertIsArray($result);
        $this->assertEquals(50, $result['threat_score']); // Default medium risk
        $this->assertEquals('medium', $result['risk_level']);
        $this->assertArrayHasKey('error', $result);
    }

    /** @test */
    public function test_private_ip_detection()
    {
        $service = new class extends UniversalAISecurityService {
            public function testIsPrivateIP($ip) {
                return $this->isPrivateIP($ip);
            }
        };

        $this->assertTrue($service->testIsPrivateIP('192.168.1.1'));
        $this->assertTrue($service->testIsPrivateIP('10.0.0.1'));
        $this->assertTrue($service->testIsPrivateIP('172.16.0.1'));
        $this->assertTrue($service->testIsPrivateIP('127.0.0.1'));
        $this->assertFalse($service->testIsPrivateIP('8.8.8.8'));
        $this->assertFalse($service->testIsPrivateIP('1.1.1.1'));
    }

    /** @test */
    public function test_proxy_header_detection()
    {
        $this->mockRequest->shouldReceive('server')
            ->with('HTTP_X_FORWARDED_FOR')
            ->andReturn('203.0.113.1');

        $service = new class extends UniversalAISecurityService {
            public function testHasProxyHeaders($request) {
                return $this->hasProxyHeaders($request);
            }
        };

        $this->assertTrue($service->testHasProxyHeaders($this->mockRequest));
    }

    /** @test */
    public function test_threat_analysis_with_authenticated_user()
    {
        $user = User::factory()->create(['created_at' => now()->subDays(10)]);
        $this->actingAs($user);

        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Mozilla/5.0');
        $this->mockRequest->shouldReceive('all')->andReturn([]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('threat_score', $result);
    }

    /** @test */
    public function test_sql_injection_pattern_scoring()
    {
        $service = new class extends UniversalAISecurityService {
            public function testDetectSQLInjectionPatterns($request) {
                return $this->detectSQLInjectionPatterns($request);
            }
        };

        // Test with malicious SQL injection attempt
        $maliciousRequest = Mockery::mock(Request::class);
        $maliciousRequest->shouldReceive('all')->andReturn([
            'query' => "SELECT * FROM users WHERE id = 1 UNION SELECT password FROM admin"
        ]);

        $score = $service->testDetectSQLInjectionPatterns($maliciousRequest);
        $this->assertGreaterThan(0, $score);

        // Test with normal request
        $normalRequest = Mockery::mock(Request::class);
        $normalRequest->shouldReceive('all')->andReturn([
            'query' => "normal search term"
        ]);

        $score = $service->testDetectSQLInjectionPatterns($normalRequest);
        $this->assertEquals(0, $score);
    }

    /** @test */
    public function test_threat_score_capping()
    {
        // Create a scenario that would generate a very high threat score
        $this->mockRequest->shouldReceive('ip')->andReturn('192.168.1.1');
        $this->mockRequest->shouldReceive('userAgent')->andReturn('Malicious Bot');
        $this->mockRequest->shouldReceive('all')->andReturn([
            'injection' => "'; DROP TABLE users; --",
            'xss' => '<script>alert("hack")</script>'
        ]);
        $this->mockRequest->shouldReceive('getContent')->andReturn('');
        $this->mockRequest->shouldReceive('headers->all')->andReturn([]);
        $this->mockRequest->shouldReceive('isMethod')->andReturn(false);
        $this->mockRequest->shouldReceive('hasHeader')->andReturn(true);

        $result = $this->aiSecurityService->analyzeThreatLevel($this->mockRequest);

        // Threat score should be capped at 100
        $this->assertLessThanOrEqual(100, $result['threat_score']);
    }
} 