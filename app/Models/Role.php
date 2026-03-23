<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 1;

    const STAFF = 2;

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
