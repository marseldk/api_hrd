<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuLevel extends Model
{
    protected $table = 'menu_level';
    protected $fillable = [
        'id_menu',
        'kode_jabatan',
        'created_by',
    ];
}
