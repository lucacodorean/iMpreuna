<?php

namespace App\Models;

use App\Models\Traits\HasKey;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    use HasKey;
    protected $fillable = ['name', 'key'];

    public function getRouteKeyName() { return "key"; }

    public function users() {
        return $this->belongsToMany(User::class, 'organization_user', 'role_id', 'user_id')
            ->withPivot('organization_id')
            ->withTimestamps();
    }
}
