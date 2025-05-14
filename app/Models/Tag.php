<?php

namespace App\Models;

use App\Models\Traits\HasKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{

    use HasKey;
    protected $fillable = ["name", "key"];

    public function requests(): BelongsToMany {
        return $this->belongsToMany(UserRequest::class, "request_tag", "id");
    }

    public function organizations(): BelongsToMany {
        return $this->belongsToMany(Organization::class);
    }
}
