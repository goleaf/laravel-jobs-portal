<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DataScienceService
{
    /**
     * Advanced data cleaning with intelligent forget() patterns.
     */
    public function advancedDataCleaning(array $rawData, array $cleaningRules = []): array
    {
        $data = collect($rawData);
        $cleaningStats = [
            'original_count' => $data->count(),
            'operations_performed' => [],
            'performance_metrics' => [],
        ];

        $startTime = microtime(true);

        // 1. Statistical outlier removal using forget()
        $outlierIndices = $this->detectStatisticalOutliers($data);
        $data->forget($outlierIndices);
        $cleaningStats['operations_performed'][] = 'outlier_removal';

        // 2. Intelligent null value handling
        $nullIndices = $this->intelligentNullDetection($data);
        $data->forget($nullIndices);
        $cleaningStats['operations_performed'][] = 'null_handling';

        // 3. Duplicate detection with similarity scoring
        $duplicateIndices = $this->advancedDuplicateDetection($data);
        $data->forget($duplicateIndices);
        $cleaningStats['operations_performed'][] = 'duplicate_removal';

        // 4. Data quality scoring and low-quality removal
        $lowQualityIndices = $this->calculateDataQualityScores($data);
        $data->forget($lowQualityIndices);
        $cleaningStats['operations_performed'][] = 'quality_filtering';

        // 5. Business rule validation
        $ruleViolationIndices = $this->validateBusinessRules($data, $cleaningRules);
        $data->forget($ruleViolationIndices);
        $cleaningStats['operations_performed'][] = 'business_rule_validation';

        $cleaningStats['final_count'] = $data->count();
        $cleaningStats['processing_time'] = microtime(true) - $startTime;
        $cleaningStats['data_retention_rate'] = $cleaningStats['final_count'] / $cleaningStats['original_count'];

        // Cache cleaning statistics for analytics
        $this->cacheCleaningStatistics($cleaningStats);

        return [
            'cleaned_data' => $data->values()->toArray(),
            'statistics' => $cleaningStats,
        ];
    }

    /**
     * Performance analytics with Collection forget() optimization.
     */
    public function performanceAnalytics(array $performanceData): array
    {
        $data = collect($performanceData);

        // Remove performance data older than analysis window
        $analysisWindow = Carbon::now()->subDays(30);
        $outdatedIndices = $data->filter(function ($record, $index) use ($analysisWindow) {
            $recordDate = Carbon::parse($record['timestamp'] ?? $record['created_at'] ?? now());

            return $recordDate->isBefore($analysisWindow);
        })->keys();

        $data->forget($outdatedIndices->toArray());

        // Remove anomalous performance readings
        $anomalyIndices = $this->detectPerformanceAnomalies($data);
        $data->forget($anomalyIndices);

        // Remove incomplete performance records
        $incompleteIndices = $data->filter(function ($record) {
            return ! isset($record['response_time'])
                   || ! isset($record['memory_usage'])
                   || ! isset($record['cpu_usage']);
        })->keys();

        $data->forget($incompleteIndices->toArray());

        return $this->calculatePerformanceMetrics($data);
    }

    /**
     * User behavior analytics with intelligent data segmentation.
     */
    public function userBehaviorAnalytics(array $userActions): array
    {
        $actions = collect($userActions);

        // Remove bot/spam actions using forget()
        $botIndices = $this->detectBotActions($actions);
        $actions->forget($botIndices);

        // Remove privacy-sensitive actions for GDPR compliance
        $sensitiveIndices = $this->identifySensitiveActions($actions);
        $actions->forget($sensitiveIndices);

        // Remove actions from test/admin users
        $testUserIndices = $actions->filter(function ($action) {
            return ($action['user_type'] ?? '') === 'test'
                   || ($action['is_admin'] ?? false) === true
                   || str_contains($action['email'] ?? '', 'test');
        })->keys();

        $actions->forget($testUserIndices->toArray());

        // Advanced segmentation with forget()
        return $this->performBehaviorSegmentation($actions);
    }

    /**
     * Machine Learning data preparation with forget() optimization.
     */
    public function prepareMLDataset(array $rawFeatures, array $config = []): array
    {
        $features = collect($rawFeatures);

        // Remove features with low variance (not useful for ML)
        $lowVarianceIndices = $this->calculateFeatureVariance($features);
        $features->forget($lowVarianceIndices);

        // Remove highly correlated features to prevent multicollinearity
        $correlatedIndices = $this->findHighlyCorrelatedFeatures($features);
        $features->forget($correlatedIndices);

        // Remove features with excessive missing values
        $missingDataIndices = $this->findExcessiveMissingData($features, $config['missing_threshold'] ?? 0.3);
        $features->forget($missingDataIndices);

        // Remove categorical features with too many unique values
        $highCardinalityIndices = $this->findHighCardinalityFeatures($features, $config['cardinality_threshold'] ?? 50);
        $features->forget($highCardinalityIndices);

        return [
            'prepared_features' => $features->values()->toArray(),
            'feature_engineering_stats' => $this->generateFeatureStats($features),
            'recommended_algorithms' => $this->suggestMLAlgorithms($features),
        ];
    }

    /**
     * Real-time data stream processing with forget() buffering.
     */
    public function processDataStream(Collection $streamData, array $processingRules): Collection
    {
        // Implement sliding window with forget() for memory efficiency
        $windowSize = $processingRules['window_size'] ?? 1000;

        if ($streamData->count() > $windowSize) {
            $excessIndices = range(0, $streamData->count() - $windowSize - 1);
            $streamData->forget($excessIndices);
        }

        // Real-time anomaly detection and removal
        $anomalyIndices = $this->realTimeAnomalyDetection($streamData);
        $streamData->forget($anomalyIndices);

        // Apply real-time filters
        $filteredIndices = $this->applyStreamFilters($streamData, $processingRules);
        $streamData->forget($filteredIndices);

        return $streamData->values();
    }

    /**
     * Clean data for analysis with intelligent forget() patterns.
     */
    public static function cleanDataForAnalysis(array $rawData, array $options = []): array
    {
        $data = collect($rawData);
        $originalCount = $data->count();
        $startTime = microtime(true);

        // Remove outliers using statistical methods
        $outlierIndices = self::findOutliers($data, $options['outlier_threshold'] ?? 3);
        $data->forget($outlierIndices);

        // Remove null/empty values
        $emptyIndices = $data->filter(function ($record, $index) {
            return empty($record)
                   || (is_array($record) && count(array_filter($record)) === 0);
        })->keys();
        $data->forget($emptyIndices->toArray());

        // Remove duplicate records
        $seen = [];
        $duplicateIndices = [];
        foreach ($data as $index => $record) {
            $signature = md5(serialize($record));
            if (isset($seen[$signature])) {
                $duplicateIndices[] = $index;
            } else {
                $seen[$signature] = true;
            }
        }
        $data->forget($duplicateIndices);

        $processingTime = (microtime(true) - $startTime) * 1000;

        return [
            'cleaned_data' => $data->values()->toArray(),
            'original_count' => $originalCount,
            'final_count' => $data->count(),
            'processing_time_ms' => round($processingTime, 2),
            'outliers_removed' => count($outlierIndices),
            'empty_records_removed' => count($emptyIndices),
            'duplicates_removed' => count($duplicateIndices),
        ];
    }

    /**
     * Find statistical outliers in dataset.
     */
    protected static function findOutliers(Collection $data, float $threshold = 3): array
    {
        $outlierIndices = [];

        foreach ($data as $index => $record) {
            if (is_array($record)) {
                $numericValues = array_filter($record, 'is_numeric');
                if (count($numericValues) > 0) {
                    $mean = array_sum($numericValues) / count($numericValues);
                    $variance = array_sum(array_map(function ($x) use ($mean) {
                        return pow($x - $mean, 2);
                    }, $numericValues)) / count($numericValues);
                    $stdDev = sqrt($variance);

                    foreach ($numericValues as $value) {
                        if ($stdDev > 0 && abs($value - $mean) > ($threshold * $stdDev)) {
                            $outlierIndices[] = $index;

                            break;
                        }
                    }
                }
            }
        }

        return array_unique($outlierIndices);
    }

    /**
     * Advanced helper methods for data science operations.
     */
    protected function detectStatisticalOutliers(Collection $data): array
    {
        // Implement IQR method for outlier detection
        $numericFields = $this->identifyNumericFields($data);
        $outlierIndices = [];

        foreach ($numericFields as $field) {
            $values = $data->pluck($field)->filter()->sort()->values();
            if ($values->count() < 4) {
                continue;
            }

            $q1 = $values->percentile(25);
            $q3 = $values->percentile(75);
            $iqr = $q3 - $q1;
            $lowerBound = $q1 - (1.5 * $iqr);
            $upperBound = $q3 + (1.5 * $iqr);

            $fieldOutliers = $data->filter(function ($record, $index) use ($field, $lowerBound, $upperBound) {
                $value = $record[$field] ?? null;

                return is_numeric($value) && ($value < $lowerBound || $value > $upperBound);
            })->keys();

            $outlierIndices = array_merge($outlierIndices, $fieldOutliers->toArray());
        }

        return array_unique($outlierIndices);
    }

    protected function advancedDuplicateDetection(Collection $data): array
    {
        $duplicateIndices = [];
        $seenHashes = [];

        foreach ($data as $index => $record) {
            // Create smart hash excluding timestamp fields
            $recordForHash = collect($record)->except(['created_at', 'updated_at', 'timestamp', 'id'])->toArray();
            $hash = md5(serialize($recordForHash));

            if (in_array($hash, $seenHashes)) {
                $duplicateIndices[] = $index;
            } else {
                $seenHashes[] = $hash;
            }
        }

        return $duplicateIndices;
    }

    protected function calculateDataQualityScores(Collection $data): array
    {
        $lowQualityIndices = [];

        foreach ($data as $index => $record) {
            $qualityScore = $this->calculateRecordQualityScore($record);

            if ($qualityScore < 0.6) { // Threshold for data quality
                $lowQualityIndices[] = $index;
            }
        }

        return $lowQualityIndices;
    }

    protected function calculateRecordQualityScore(array $record): float
    {
        $score = 1.0;
        $fieldCount = count($record);
        $nullCount = count(array_filter($record, fn ($value) => is_null($value)));
        $emptyCount = count(array_filter($record, fn ($value) => $value === ''));

        // Penalize for missing data
        $score -= ($nullCount / $fieldCount) * 0.3;
        $score -= ($emptyCount / $fieldCount) * 0.2;

        // Bonus for rich data (longer text fields, more filled fields)
        $textFieldLength = array_sum(array_map(function ($value) {
            return is_string($value) ? strlen($value) : 0;
        }, $record));

        if ($textFieldLength > 100) {
            $score += 0.1;
        }

        return max(0, min(1, $score));
    }

    protected function detectPerformanceAnomalies(Collection $data): array
    {
        $anomalyIndices = [];

        // Detect response time anomalies
        $responseTimes = $data->pluck('response_time')->filter();
        $avgResponseTime = $responseTimes->avg();
        $stdDev = $this->calculateStandardDeviation($responseTimes);

        foreach ($data as $index => $record) {
            $responseTime = $record['response_time'] ?? 0;

            if (abs($responseTime - $avgResponseTime) > (3 * $stdDev)) {
                $anomalyIndices[] = $index;
            }
        }

        return $anomalyIndices;
    }

    protected function calculateStandardDeviation(Collection $values): float
    {
        $mean = $values->avg();
        $variance = $values->map(fn ($value) => pow($value - $mean, 2))->avg();

        return sqrt($variance);
    }

    protected function cacheCleaningStatistics(array $stats): void
    {
        $cacheKey = 'data_cleaning_stats_'.date('Y-m-d');
        $existingStats = Cache::get($cacheKey, []);
        $existingStats[] = $stats;
        Cache::put($cacheKey, $existingStats, 86400); // 24 hours

        Log::info('Data cleaning completed', $stats);
    }

    protected function identifyNumericFields(Collection $data): array
    {
        if ($data->isEmpty()) {
            return [];
        }

        $firstRecord = $data->first();
        $numericFields = [];

        foreach ($firstRecord as $field => $value) {
            if (is_numeric($value)) {
                $numericFields[] = $field;
            }
        }

        return $numericFields;
    }
}
