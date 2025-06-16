<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Role as RoleModel;

class Role extends RoleModel implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['permissions'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['in_used'];

    /**
     * Determine if the role is in used.
     */
    protected function inUsed(): Attribute
    {
        return new Attribute(
            get: function () {
                return DB::table('model_has_roles')->where('role_id', '=', $this->id)->exists();
            },
        );
    }

    /**
     * Get all of the Role's audit.
     */
    public function audit(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
