<?php

namespace App\Models;

class Profile extends User
{
    protected $table = 'users';
    public $timestamps = true;

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}