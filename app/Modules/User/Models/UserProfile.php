<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        //need to handle from request later
        static::creating(function (UserProfile $profile) {
            $profile->country = 'SG';
            $profile->language = 'en';
            $profile->timezone = 'Asia/Singapore';
        });
    }

    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'avatar_url',
        'country',
        'language',
        'timezone',
        'phone',
        'phone_verified_at'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(\Modules\Auth\Models\User::class);
    }
}
