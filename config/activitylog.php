<?php

return [
    'enabled' => env('ACTIVITY_LOG_ENABLED', true),
    
    'delete_records_older_than_days' => 365, // Auto-delete old logs
    
    'default_log_name' => 'default',
    
    'default_auth_driver' => null,
    
    'subject_returns_soft_deleted_models' => false,

    'database_connection' => env('DB_CONNECTION', 'mysql'), // or your default connection

    'table_name' => 'activity_log',
];