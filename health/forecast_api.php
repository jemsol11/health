<?php
// forecast_api.php

header('Content-Type: application/json');

function load_tiered_forecast_data_api() {
    $gbr_file = 'forecast_results.json';
    $prophet_file = 'seasonal_forecast.json';
    $lr_file = 'forecast_demand.json';

    $final_data = [];
    
    $gbr_data = file_exists($gbr_file) ? json_decode(file_get_contents($gbr_file), true) : [];
    $prophet_data = file_exists($prophet_file) ? json_decode(file_get_contents($prophet_file), true) : [];
    $lr_data = file_exists($lr_file) ? json_decode(file_get_contents($lr_file), true) : [];

    // Combine all keys from all sources
    $all_meds = array_keys(array_merge($gbr_data, $lr_data));
    $all_meds = array_unique($all_meds);

    foreach ($all_meds as $med_name) {
        $prediction = 0;
        
        // Tier 1: GBR (Value > 0)
        if (isset($gbr_data[$med_name]) && $gbr_data[$med_name] > 0) {
            $prediction = $gbr_data[$med_name];
        } 
        
        // Tier 2: Prophet (Value > 0)
        else if (isset($prophet_data[$med_name])) {
            $prophet_pred = $prophet_data[$med_name]['next_month_pred'] ?? 0;
            if ($prophet_pred > 0) {
                 $prediction = $prophet_pred;
            }
        }

        // Tier 3: Linear Regression (Value > 0)
        else if (isset($lr_data[$med_name]) && $lr_data[$med_name] > 0) {
            $prediction = $lr_data[$med_name];
        }

        // If all are zero, the forecast remains zero.
        $final_data[$med_name] = round($prediction, 2);
    }
    
    return $final_data;
}

echo json_encode(load_tiered_forecast_data_api());
?>