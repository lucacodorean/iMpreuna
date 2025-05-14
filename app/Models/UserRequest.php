<?php

namespace App\Models;

use App\Models\Traits\HasKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserRequest extends Model {

    use HasKey;

    protected $table = "requests";
    protected $fillable = ["requester_id", "description", "status", "key"];
    public function getRouteKeyName() { return "key"; }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "requester_id");
    }

    public function volunteers(): BelongsToMany {
        return $this->belongsToMany(User::class, "request_volunteer", "request_id", "volunteer_id");
    }

    public function tags(): belongsToMany {
        return $this->belongsToMany(Tag::class, "request_tag", "request_id", "tag_id");
    }
}
