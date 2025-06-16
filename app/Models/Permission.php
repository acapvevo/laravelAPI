<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as PermissionModel;

class Permission extends PermissionModel implements Auditable
{
    use AuditableTrait, HasFactory, SoftDeletes;
}
