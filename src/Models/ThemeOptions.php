<?php

namespace VoyagerThemes\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeOptions extends Model
{
	protected $table = 'voyager_theme_options';
    protected $fillable = ['voyager_theme_id', 'key', 'value'];
}
