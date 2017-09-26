<?php

namespace VoyagerThemes\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    //
    protected $table = 'voyager_themes';
    protected $fillable = ['name', 'folder'];
}
