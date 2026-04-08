<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Use the tenant connection when in tenant context
     */
    public function getConnectionName()
    {
        return config('tenancy.database.connection_name') ?: parent::getConnectionName();
    }
}
