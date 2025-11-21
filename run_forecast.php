<?php
// run_forecast.php

function try_run_forecast_once() {
    // Confirmed path for the Python script
    $script_path = 'C:\xampp\htdocs\healthbackup\health\forecast.py'; 
    
    // Commands to try: specific paths first (based on your log), then generic commands
    $commands = [
        // Specific paths from your environment (highest priority)
        '"C:\Users\Laptop Supplier PH\AppData\Local\Programs\Python\Python312\python.exe"',
        '"C:\Program Files\Python312\python.exe"',
        '"C:\Program Files\Python311\python.exe"',
        '"C:\Program Files\Python310\python.exe"',
        '"C:\Python312\python.exe"',
        // Generic commands (rely on PATH variable)
        'python', 
        'py', 
        'python3'
    ];

    foreach ($commands as $cmd) {
        // Execute command + redirect output (2>&1)
        $full_cmd = "$cmd \"$script_path\" 2>&1"; 
        @exec($full_cmd, $out, $ret);
        
        if ($ret === 0) return true; // Success, stop trying
    }
    return false; // All attempts failed
}

// Note: This file defines the function but does not call it automatically.
// It will be called explicitly in other dispense/admin files.
?>