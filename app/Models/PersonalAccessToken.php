<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The connection name for the model.
     * 
     * @var string|null
     */
    protected $connection = 'tenant';
    
    /**
     * Use the tenant connection when tenancy is initialized
     */
    public function getConnectionName()
    {
        // If tenancy is initialized, use tenant connection
        if (function_exists('tenancy') && tenancy()->initialized) {
            return 'tenant';
        }
        
        // Otherwise try to use the default connection
        return config('database.default');
    }
}
