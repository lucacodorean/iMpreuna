<?php

namespace App\Models;

use App\Models\Traits\HasKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasKey;
    protected $fillable = ["name", "description", "address", "key"];

    public function getRouteKeyName() { return "key"; }

    public function users() : BelongsToMany {
        return $this->belongsToMany(User::class)->withPivot("role_id");
    }

    public function tags() : BelongsToMany {
        return $this->belongsToMany(Tag::class);
    }

    public function events() : BelongsToMany {
        return $this->belongsToMany(Event::class);
    }
}
