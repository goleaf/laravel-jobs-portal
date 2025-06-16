<?php

namespace App\Services\Universal;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UniversalAISecurityService
{
    /**
     * AI-powered threat detection system
     */
    public function analyzeThreatLevel(Request $request)
    {
        try {
            $threatScore = 0;
            $threatFactors = [];
            
            // Behavioral analysis
            $behaviorScore = $this->analyzeBehavioralPatterns($request);
            $threatScore += $behaviorScore['score'];
            $threatFactors['behavioral'] = $behaviorScore;
            
            // IP reputation analysis
            $ipScore = $this->analyzeIPReputation($request->ip());
            $threatScore += $ipScore['score'];
            $threatFactors['ip_reputation'] = $ipScore;
            
            // Request pattern analysis
            $patternScore = $this->analyzeRequestPatterns($request);
            $threatScore += $patternScore['score'];
            $threatFactors['request_patterns'] = $patternScore;
            
            // User agent analysis
            $uaScore = $this->analyzeUserAgent($request->userAgent());
            $threatScore += $uaScore['score'];
            $threatFactors['user_agent'] = $uaScore;
            
            // Temporal analysis
            $temporalScore = $this->analyzeTemporalPatterns($request);
            $threatScore += $temporalScore['score'];
            $threatFactors['temporal'] = $temporalScore;
            
            // Machine learning model prediction
            $mlScore = $this->machineLearningPrediction($request, $threatFactors);
            $threatScore += $mlScore['score'];
            $threatFactors['ml_prediction'] = $mlScore;
            
            $riskLevel = $this->calculateRiskLevel($threatScore);
            
            return [
                'threat_score' => $threatScore,
                'risk_level' => $riskLevel,
                'factors' => $threatFactors,
                'recommendations' => $this->generateSecurityRecommendations($riskLevel, $threatFactors),
                'timestamp' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('AI threat analysis error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'threat_score' => 50, // Default medium risk
                'risk_level' => 'medium',
                'error' => 'Analysis failed, using default risk level'
            ];
        }
    }

    /**
     * Advanced behavioral pattern analysis
     */
    private function analyzeBehavioralPatterns(Request $request)
    {
        $ip = $request->ip();
        $userId = auth()->id();
        $score = 0;
        $factors = [];
        
        // Analyze request frequency
        $requestHistory = $this->getRequestHistory($ip, $userId);
        
        // Unusual request volume
        if ($requestHistory['requests_per_minute'] > 30) {
            $score += 20;
            $factors[] = 'high_request_frequency';
        }
        
        // Request pattern diversity
        $patternDiversity = $this->calculatePatternDiversity($requestHistory['patterns']);
        if ($patternDiversity < 0.3) { // Low diversity indicates bot behavior
            $score += 15;
            $factors[] = 'low_pattern_diversity';
        }
        
        // Time-based anomalies
        $timeAnomalies = $this->detectTimeAnomalies($requestHistory['timestamps']);
        if ($timeAnomalies['has_anomalies']) {
            $score += 10;
            $factors[] = 'temporal_anomalies';
        }
        
        // Session behavior analysis
        if ($userId) {
            $sessionBehavior = $this->analyzeSessionBehavior($userId);
            if ($sessionBehavior['is_suspicious']) {
                $score += 25;
                $factors = array_merge($factors, $sessionBehavior['suspicious_factors']);
            }
        }
        
        // Geographic inconsistencies
        $geoInconsistency = $this->analyzeGeographicInconsistencies($ip, $userId);
        if ($geoInconsistency['is_inconsistent']) {
            $score += 20;
            $factors[] = 'geographic_inconsistency';
        }
        
        return [
            'score' => min($score, 100), // Cap at 100
            'factors' => $factors,
            'details' => [
                'requests_per_minute' => $requestHistory['requests_per_minute'],
                'pattern_diversity' => $patternDiversity,
                'time_anomalies' => $timeAnomalies,
                'geo_inconsistency' => $geoInconsistency
            ]
        ];
    }

    /**
     * IP reputation analysis with threat intelligence
     */
    private function analyzeIPReputation($ip)
    {
        $score = 0;
        $factors = [];
        
        // Check against known threat databases
        $threatIntelligence = $this->checkThreatIntelligence($ip);
        if ($threatIntelligence['is_malicious']) {
            $score += 50;
            $factors[] = 'known_malicious_ip';
        }
        
        // Check for VPN/Proxy usage
        $proxyDetection = $this->detectProxy($ip);
        if ($proxyDetection['is_proxy']) {
            $score += 15;
            $factors[] = 'proxy_usage';
        }
        
        // Check for TOR exit nodes
        if ($this->isTorExitNode($ip)) {
            $score += 30;
            $factors[] = 'tor_exit_node';
        }
        
        // Historical security events from this IP
        $historicalEvents = $this->getHistoricalSecurityEvents($ip);
        if ($historicalEvents['count'] > 0) {
            $score += min($historicalEvents['count'] * 5, 25);
            $factors[] = 'historical_security_events';
        }
        
        // ASN and hosting provider analysis
        $asnAnalysis = $this->analyzeASN($ip);
        if ($asnAnalysis['is_suspicious']) {
            $score += 10;
            $factors[] = 'suspicious_hosting_provider';
        }
        
        return [
            'score' => min($score, 100),
            'factors' => $factors,
            'details' => [
                'threat_intelligence' => $threatIntelligence,
                'proxy_detection' => $proxyDetection,
                'historical_events' => $historicalEvents,
                'asn_analysis' => $asnAnalysis
            ]
        ];
    }

    /**
     * Advanced request pattern analysis
     */
    private function analyzeRequestPatterns(Request $request)
    {
        $score = 0;
        $factors = [];
        
        // SQL injection pattern detection
        $sqlInjectionScore = $this->detectSQLInjectionPatterns($request);
        if ($sqlInjectionScore > 0) {
            $score += $sqlInjectionScore;
            $factors[] = 'sql_injection_patterns';
        }
        
        // XSS pattern detection
        $xssScore = $this->detectXSSPatterns($request);
        if ($xssScore > 0) {
            $score += $xssScore;
            $factors[] = 'xss_patterns';
        }
        
        // Command injection detection
        $commandInjectionScore = $this->detectCommandInjection($request);
        if ($commandInjectionScore > 0) {
            $score += $commandInjectionScore;
            $factors[] = 'command_injection_patterns';
        }
        
        // Directory traversal detection
        $traversalScore = $this->detectDirectoryTraversal($request);
        if ($traversalScore > 0) {
            $score += $traversalScore;
            $factors[] = 'directory_traversal_patterns';
        }
        
        // Unusual parameter patterns
        $parameterScore = $this->analyzeParameterPatterns($request);
        if ($parameterScore > 0) {
            $score += $parameterScore;
            $factors[] = 'unusual_parameter_patterns';
        }
        
        // Request size anomalies
        $sizeScore = $this->analyzeRequestSize($request);
        if ($sizeScore > 0) {
            $score += $sizeScore;
            $factors[] = 'request_size_anomaly';
        }
        
        return [
            'score' => min($score, 100),
            'factors' => $factors,
            'pattern_details' => [
                'sql_injection' => $sqlInjectionScore,
                'xss' => $xssScore,
                'command_injection' => $commandInjectionScore,
                'directory_traversal' => $traversalScore,
                'parameter_anomalies' => $parameterScore,
                'size_anomalies' => $sizeScore
            ]
        ];
    }

    /**
     * Machine learning threat prediction
     */
    private function machineLearningPrediction(Request $request, $threatFactors)
    {
        try {
            // Feature extraction for ML model
            $features = $this->extractMLFeatures($request, $threatFactors);
            
            // Simple decision tree model (replace with trained ML model)
            $prediction = $this->simpleDecisionTree($features);
            
            // Ensemble voting with multiple models
            $ensemblePrediction = $this->ensembleVoting($features);
            
            // Combine predictions
            $finalScore = ($prediction['score'] + $ensemblePrediction['score']) / 2;
            
            return [
                'score' => $finalScore,
                'confidence' => $prediction['confidence'],
                'model_predictions' => [
                    'decision_tree' => $prediction,
                    'ensemble' => $ensemblePrediction
                ],
                'features_used' => array_keys($features)
            ];
            
        } catch (\Exception $e) {
            Log::error('ML prediction error', [
                'error' => $e->getMessage(),
                'features' => $features ?? []
            ]);
            
            return [
                'score' => 0,
                'confidence' => 0,
                'error' => 'ML prediction failed'
            ];
        }
    }

    /**
     * Extract features for machine learning model
     */
    private function extractMLFeatures(Request $request, $threatFactors)
    {
        return [
            // Request characteristics
            'request_size' => strlen($request->getContent()),
            'parameter_count' => count($request->all()),
            'header_count' => count($request->headers->all()),
            'method_is_post' => $request->isMethod('POST') ? 1 : 0,
            'has_user_agent' => $request->hasHeader('User-Agent') ? 1 : 0,
            
            // Threat factor scores
            'behavioral_score' => $threatFactors['behavioral']['score'] ?? 0,
            'ip_reputation_score' => $threatFactors['ip_reputation']['score'] ?? 0,
            'pattern_score' => $threatFactors['request_patterns']['score'] ?? 0,
            
            // Time-based features
            'hour_of_day' => now()->hour,
            'day_of_week' => now()->dayOfWeek,
            'is_weekend' => now()->isWeekend() ? 1 : 0,
            
            // User context
            'is_authenticated' => auth()->check() ? 1 : 0,
            'user_age_days' => auth()->check() ? 
                now()->diffInDays(auth()->user()->created_at) : 0,
            
            // Network features
            'ip_is_private' => $this->isPrivateIP($request->ip()) ? 1 : 0,
            'has_proxy_headers' => $this->hasProxyHeaders($request) ? 1 : 0,
        ];
    }

    /**
     * Simple decision tree for threat classification
     */
    private function simpleDecisionTree($features)
    {
        $score = 0;
        $confidence = 0.7; // Base confidence
        
        // High-risk patterns
        if ($features['pattern_score'] > 50) {
            $score += 40;
            $confidence += 0.2;
        }
        
        // IP reputation issues
        if ($features['ip_reputation_score'] > 30) {
            $score += 30;
            $confidence += 0.1;
        }
        
        // Behavioral anomalies
        if ($features['behavioral_score'] > 40) {
            $score += 25;
        }
        
        // Suspicious timing
        if ($features['hour_of_day'] >= 2 && $features['hour_of_day'] <= 5) {
            $score += 5; // Late night activity
        }
        
        // New user with suspicious activity
        if ($features['is_authenticated'] && $features['user_age_days'] < 1 && $score > 20) {
            $score += 15;
            $confidence += 0.1;
        }
        
        return [
            'score' => min($score, 100),
            'confidence' => min($confidence, 1.0)
        ];
    }

    /**
     * Ensemble voting with multiple simple models
     */
    private function ensembleVoting($features)
    {
        $models = [
            'suspicious_patterns' => $this->suspiciousPatternsModel($features),
            'behavioral_anomaly' => $this->behavioralAnomalyModel($features),
            'network_threat' => $this->networkThreatModel($features)
        ];
        
        $scores = array_column($models, 'score');
        $confidences = array_column($models, 'confidence');
        
        return [
            'score' => array_sum($scores) / count($scores),
            'confidence' => array_sum($confidences) / count($confidences),
            'model_results' => $models
        ];
    }

    /**
     * Calculate final risk level based on threat score
     */
    private function calculateRiskLevel($threatScore)
    {
        if ($threatScore >= 80) return 'critical';
        if ($threatScore >= 60) return 'high';
        if ($threatScore >= 40) return 'medium';
        if ($threatScore >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Generate security recommendations based on risk analysis
     */
    private function generateSecurityRecommendations($riskLevel, $threatFactors)
    {
        $recommendations = [];
        
        switch ($riskLevel) {
            case 'critical':
                $recommendations[] = 'Immediately block this request';
                $recommendations[] = 'Consider IP ban for 24 hours';
                $recommendations[] = 'Trigger security alert';
                break;
                
            case 'high':
                $recommendations[] = 'Require additional authentication';
                $recommendations[] = 'Rate limit this IP aggressively';
                $recommendations[] = 'Log detailed security event';
                break;
                
            case 'medium':
                $recommendations[] = 'Monitor closely for patterns';
                $recommendations[] = 'Apply standard rate limiting';
                $recommendations[] = 'Consider CAPTCHA challenge';
                break;
                
            case 'low':
                $recommendations[] = 'Continue normal monitoring';
                $recommendations[] = 'Log basic security metrics';
                break;
        }
        
        // Add specific recommendations based on threat factors
        foreach ($threatFactors as $category => $data) {
            if (!empty($data['factors'])) {
                $recommendations = array_merge(
                    $recommendations, 
                    $this->getSpecificRecommendations($category, $data['factors'])
                );
            }
        }
        
        return array_unique($recommendations);
    }

    /**
     * Get specific recommendations for threat categories
     */
    private function getSpecificRecommendations($category, $factors)
    {
        $recommendations = [];
        
        foreach ($factors as $factor) {
            switch ($factor) {
                case 'sql_injection_patterns':
                    $recommendations[] = 'Enable SQL injection protection';
                    $recommendations[] = 'Review database access patterns';
                    break;
                    
                case 'proxy_usage':
                    $recommendations[] = 'Consider blocking proxy traffic';
                    $recommendations[] = 'Require email verification';
                    break;
                    
                case 'brute_force_attack':
                    $recommendations[] = 'Implement progressive delays';
                    $recommendations[] = 'Require CAPTCHA after failures';
                    break;
                    
                case 'geographic_inconsistency':
                    $recommendations[] = 'Send location change notification';
                    $recommendations[] = 'Require device verification';
                    break;
            }
        }
        
        return $recommendations;
    }

    /**
     * Additional helper methods for comprehensive analysis
     */
    private function getRequestHistory($ip, $userId)
    {
        // Simplified implementation - in production, use efficient data structures
        $key = $userId ? "user_requests:{$userId}" : "ip_requests:{$ip}";
        
        $history = Cache::get($key, [
            'requests_per_minute' => 0,
            'patterns' => [],
            'timestamps' => []
        ]);
        
        return $history;
    }

    private function detectSQLInjectionPatterns(Request $request)
    {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b.*\bWHERE\b)/i',
            '/(\'.*OR.*\'.*=.*\')/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i'
        ];
        
        $content = json_encode($request->all());
        $score = 0;
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $score += 20;
            }
        }
        
        return min($score, 100);
    }

    private function isPrivateIP($ip)
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function hasProxyHeaders(Request $request)
    {
        $proxyHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_CLUSTER_CLIENT_IP'
        ];
        
        foreach ($proxyHeaders as $header) {
            if ($request->server($header)) {
                return true;
            }
        }
        
        return false;
    }

    // Additional model implementations
    private function suspiciousPatternsModel($features)
    {
        $score = $features['pattern_score'] * 0.8;
        return ['score' => $score, 'confidence' => 0.9];
    }

    private function behavioralAnomalyModel($features)
    {
        $score = $features['behavioral_score'] * 0.7;
        return ['score' => $score, 'confidence' => 0.8];
    }

    private function networkThreatModel($features)
    {
        $score = $features['ip_reputation_score'] * 0.9;
        return ['score' => $score, 'confidence' => 0.85];
    }

    // Placeholder methods for comprehensive threat analysis
    private function checkThreatIntelligence($ip) { return ['is_malicious' => false]; }
    private function detectProxy($ip) { return ['is_proxy' => false]; }
    private function isTorExitNode($ip) { return false; }
    private function getHistoricalSecurityEvents($ip) { return ['count' => 0]; }
    private function analyzeASN($ip) { return ['is_suspicious' => false]; }
    private function detectXSSPatterns(Request $request) { return 0; }
    private function detectCommandInjection(Request $request) { return 0; }
    private function detectDirectoryTraversal(Request $request) { return 0; }
    private function analyzeParameterPatterns(Request $request) { return 0; }
    private function analyzeRequestSize(Request $request) { return 0; }
    private function calculatePatternDiversity($patterns) { return 0.5; }
    private function detectTimeAnomalies($timestamps) { return ['has_anomalies' => false]; }
    private function analyzeSessionBehavior($userId) { return ['is_suspicious' => false]; }
    private function analyzeGeographicInconsistencies($ip, $userId) { return ['is_inconsistent' => false]; }
    private function analyzeTemporalPatterns(Request $request) { return ['score' => 0]; }
    private function analyzeUserAgent($userAgent) { return ['score' => 0]; }
} 