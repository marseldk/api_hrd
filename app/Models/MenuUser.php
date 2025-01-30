<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuUser extends Model
{
    protected $table = 'menu_user';
    protected $fillable = [
        'id_menu',
        'nik_func',
        'created_by',
    ];
}
