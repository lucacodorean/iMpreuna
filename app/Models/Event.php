<?php

namespace App\Models;

use App\Models\Traits\HasKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasKey;
    protected $fillable = ["name", "description", "banner", "location", "key", "starting_at", "ending_at"];

    public function getRouteKeyName() { return "key"; }

    public function organizations() : BelongsToMany {
        return $this->belongsToMany(Organization::class);
    }

    public function scopeLocation($query, $location): Builder {
        return $query->where('location', 'like', "%$location%");
    }

    public function scopeStartsBefore($query, $starts_before): Builder {
        return $query->where('starting_at', '<', $starts_before);
    }
}
