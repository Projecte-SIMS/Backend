<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralDevice extends Model
{
    protected $fillable = [
        'hardware_id',
        'display_name',
        'ip_address',
        'ssh_user',
        'tenant_id',
        'api_key',
        'use_docker',
        'last_status',
        'last_sync_at'
    ];

    protected $casts = [
        'use_docker' => 'boolean',
        'last_sync_at' => 'datetime'
    ];
}
