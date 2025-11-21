<?php
/**
 * Enhanced Forecast API
 * =====================
 * Serves monthly, quarterly, and seasonal medicine demand forecasts
 *
 * Endpoints:
 * - ?type=monthly   : Get monthly predictions
 * - ?type=quarterly : Get quarterly predictions
 * - ?type=seasonal  : Get seasonal predictions
 * - ?type=all       : Get all predictions (default)
 * - ?medicine=NAME  : Filter by specific medicine
 *
 * Output: JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// File paths
$enhanced_file = 'forecast_results/enhanced_forecast_results.json';
$gbr_file = 'forecast_results.json';
$prophet_file = 'seasonal_forecast.json';
$lr_file = 'forecast_demand.json';

// Get request parameters
$forecast_type = isset($_GET['type']) ? $_GET['type'] : 'all';
$medicine_filter = isset($_GET['medicine']) ? $_GET['medicine'] : null;

/**
 * Load forecast data from JSON files
 */
function load_forecast_data() {
    global $enhanced_file, $gbr_file, $prophet_file, $lr_file;

    // Try to load enhanced forecast first
    if (file_exists($enhanced_file)) {
        $data = json_decode(file_get_contents($enhanced_file), true);
        return [
            'source' => 'enhanced',
            'data' => $data
        ];
    }

    // Fallback: Load from legacy files
    $gbr_data = file_exists($gbr_file) ? json_decode(file_get_contents($gbr_file), true) : [];
    $prophet_data = file_exists($prophet_file) ? json_decode(file_get_contents($prophet_file), true) : [];
    $lr_data = file_exists($lr_file) ? json_decode(file_get_contents($lr_file), true) : [];

    // Merge legacy data
    $merged_data = [];
    $all_medicines = array_unique(array_merge(
        array_keys($gbr_data),
        array_keys($prophet_data),
        array_keys($lr_data)
    ));

    foreach ($all_medicines as $med) {
        // Priority: GBR > Prophet > LR
        $monthly_pred = 0;
        $quarterly_pred = 0;

        if (isset($gbr_data[$med]) && $gbr_data[$med] > 0) {
            $monthly_pred = $gbr_data[$med];
        } elseif (isset($prophet_data[$med]['next_month_pred'])) {
            $monthly_pred = $prophet_data[$med]['next_month_pred'];
        } elseif (isset($lr_data[$med]) && $lr_data[$med] > 0) {
            $monthly_pred = $lr_data[$med];
        }

        if (isset($prophet_data[$med]['quarter_avg_pred'])) {
            $quarterly_pred = $prophet_data[$med]['quarter_avg_pred'];
        } else {
            $quarterly_pred = $monthly_pred;
        }

        $merged_data[$med] = [
            'monthly' => [
                'next_1_month' => round($monthly_pred, 2),
                'next_2_months' => 0,
                'next_3_months' => 0,
                'all_months' => []
            ],
            'quarterly' => [
                'next_quarter' => round($quarterly_pred, 2),
                'all_quarters' => []
            ],
            'seasonal' => [],
            'category' => 'Unknown'
        ];
    }

    return [
        'source' => 'legacy',
        'data' => $merged_data
    ];
}

/**
 * Filter forecasts by type
 */
function filter_by_type($data, $type) {
    if ($type === 'all') {
        return $data;
    }

    $filtered = [];
    foreach ($data as $medicine => $forecast) {
        if ($type === 'monthly' && isset($forecast['monthly'])) {
            $filtered[$medicine] = $forecast['monthly'];
        } elseif ($type === 'quarterly' && isset($forecast['quarterly'])) {
            $filtered[$medicine] = $forecast['quarterly'];
        } elseif ($type === 'seasonal' && isset($forecast['seasonal'])) {
            $filtered[$medicine] = $forecast['seasonal'];
        } elseif ($type === 'summary') {
            // Summary: just next month and next quarter
            $filtered[$medicine] = [
                'next_month' => $forecast['monthly']['next_1_month'] ?? 0,
                'next_quarter' => $forecast['quarterly']['next_quarter'] ?? 0,
                'category' => $forecast['category'] ?? 'Unknown'
            ];
        }
    }

    return $filtered;
}

/**
 * Filter by specific medicine
 */
function filter_by_medicine($data, $medicine_name) {
    if (isset($data[$medicine_name])) {
        return [$medicine_name => $data[$medicine_name]];
    }

    // Try case-insensitive search
    foreach ($data as $med => $forecast) {
        if (strcasecmp($med, $medicine_name) === 0) {
            return [$med => $forecast];
        }
    }

    return [];
}

/**
 * Calculate statistics
 */
function calculate_statistics($data) {
    $total_medicines = count($data);
    $monthly_predictions = [];
    $categories = [];

    foreach ($data as $medicine => $forecast) {
        if (isset($forecast['monthly']['next_1_month'])) {
            $monthly_predictions[] = $forecast['monthly']['next_1_month'];
        }

        if (isset($forecast['category'])) {
            $category = $forecast['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }
    }

    return [
        'total_medicines' => $total_medicines,
        'total_predicted_demand' => array_sum($monthly_predictions),
        'average_demand' => count($monthly_predictions) > 0 ? round(array_sum($monthly_predictions) / count($monthly_predictions), 2) : 0,
        'max_demand' => count($monthly_predictions) > 0 ? max($monthly_predictions) : 0,
        'min_demand' => count($monthly_predictions) > 0 ? min($monthly_predictions) : 0,
        'categories' => $categories
    ];
}

/**
 * Get top N medicines by predicted demand
 */
function get_top_medicines($data, $n = 10) {
    $sorted = [];

    foreach ($data as $medicine => $forecast) {
        $demand = $forecast['monthly']['next_1_month'] ?? 0;
        $sorted[$medicine] = [
            'predicted_demand' => $demand,
            'category' => $forecast['category'] ?? 'Unknown',
            'next_quarter' => $forecast['quarterly']['next_quarter'] ?? 0
        ];
    }

    // Sort by demand descending
    uasort($sorted, function($a, $b) {
        return $b['predicted_demand'] <=> $a['predicted_demand'];
    });

    return array_slice($sorted, 0, $n, true);
}

// Main execution
try {
    // Load forecast data
    $forecast_result = load_forecast_data();
    $forecast_data = $forecast_result['data'];
    $data_source = $forecast_result['source'];

    if (empty($forecast_data)) {
        echo json_encode([
            'success' => false,
            'error' => 'No forecast data available',
            'message' => 'Please run the forecasting script first: python3 forecast_enhanced_gbr.py'
        ]);
        exit;
    }

    // Apply medicine filter if specified
    if ($medicine_filter) {
        $forecast_data = filter_by_medicine($forecast_data, $medicine_filter);

        if (empty($forecast_data)) {
            echo json_encode([
                'success' => false,
                'error' => 'Medicine not found',
                'message' => "No forecast data available for: $medicine_filter"
            ]);
            exit;
        }
    }

    // Filter by type
    $filtered_data = filter_by_type($forecast_data, $forecast_type);

    // Prepare response
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'data_source' => $data_source,
        'forecast_type' => $forecast_type,
        'forecasts' => $filtered_data
    ];

    // Add statistics if returning all data
    if ($forecast_type === 'all' && !$medicine_filter) {
        $response['statistics'] = calculate_statistics($forecast_data);
        $response['top_10_demand'] = get_top_medicines($forecast_data, 10);
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>
