<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Use the tenant connection when tenancy is initialized
     */
    public function getConnectionName()
    {
        if (function_exists('tenancy') && tenancy()->initialized) {
            return 'tenant';
        }
        
        return parent::getConnectionName();
    }
}
