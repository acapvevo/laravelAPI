<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as RoleModel;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Models\Audit;

class Role extends RoleModel implements Auditable
{
    use HasFactory, SoftDeletes, AuditableTrait;

    /**
     * Get all of the Role's audit.
     */
    public function audit(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
