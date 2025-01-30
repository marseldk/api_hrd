<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_token';
    protected $fillable = ['token', 'expires_at', 'ip_address', 'nik_func', 'created_by', 'updated_by'];
}
