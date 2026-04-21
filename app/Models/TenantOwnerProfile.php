<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantOwnerProfile extends Model
{
    protected $fillable = [
        'tenant_id',
        'owner_name',
        'owner_email',
        'entity_type',
        'company_name',
        'tax_id',
        'phone',
        'address',
        'city',
        'postal_code',
    ];

    /**
     * Get the tenant that owns this profile.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
