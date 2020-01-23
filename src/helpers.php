<?php

if (!class_exists(ThemeOptionConvertible)) {
    class ThemeOptionConvertible
    {
        public function toObject() {
            $array = (array)$this;

            return (object)$array;
        }        
    }
}

if (!class_exists(ThemeOptionHelper)) {
    class ThemeOptionHelper extends ThemeOptionConvertible
    {
        public $required = false;
        public $field;
        public $type;
        public $details;
        public $display_name;
        public $options = [];

        public static function create($type, $field, $details, $display_name, $required = 0, $options = []) {
            $result = new ThemeOptionHelper();
            $result->type = $type;
            $result->field = $field;
            $result->details = $details;
            $result->display_name = $display_name;
            $result->required = $required;
            $result->options = $options;

            return $result;
        }

        public function getTranslatedAttribute($attribute) {
            return $attribute;
        }
    }
}

if (!class_exists(ThemeOptionTypeHelper)) {
    class ThemeOptionTypeHelper extends ThemeOptionConvertible
    {
        protected $id = 0;
        protected $key = null;

        public function setKey($key, $content) { 
            $this->key = $key;
            $this->{$key} = $content; 
        }
        
        public static function create($key, $content) {

            $result = new ThemeOptionTypeHelper();
            $result->setKey($key, $content);

            return $result;
        }

        public function getKey() { return $this->key; }
    }
}

if (!function_exists(theme_field)) {

    function theme_field($type, $key, $title, $content = '', $details = '', $placeholder = '', $required = 0)
    {

        $theme = \VoyagerThemes\Models\Theme::where('folder', '=', ACTIVE_THEME_FOLDER)->first();

        $option_exists = $theme->options->where('key', '=', $key)->first();

        if (isset($option_exists->value)) {
            $content = $option_exists->value;
        }

        $row = ThemeOptionHelper::create($type, $key, $details, $placeholder, $required);
        $dataTypeContent = ThemeOptionTypeHelper::create($key, $content);   
        
        $label = '<label for="' . $key . '">' . $title . '<span class="how_to">You can reference this value with <code>theme(\'' . $key . '\')</code></span></label>';
        $details = '<input type="hidden" value="' . $details . '" name="' . $key . '_details__theme_field">';
        $type = '<input type="hidden" value="' . $type . '" name="' . $key . '_type__theme_field">';
        
        if (version_compare(substr(app('voyager')->getVersion(),1), '1.2.6', '<=')) {
            return $label . app('voyager')->formField($row->toObject(), '', $dataTypeContent->toObject()) . $details . $type . '<hr>';              
        }

        return $label . app('voyager')->formField($row, '', $dataTypeContent) . $details . $type . '<hr>';
    }

}

if (!function_exists(theme)) {

    function theme($key, $default = '')
    {
        $theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

        if (Cookie::get('voyager_theme')) {
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if (isset($theme_cookied->id)) {
                $theme = $theme_cookied;
            }
        }

        $value = $theme->options->where('key', '=', $key)->first();

        if (isset($value)) {
            return $value->value;
        }

        return $default;
    }

}

if (!function_exists(theme_folder)) {
    function theme_folder($folder_file = '')
    {

        if (defined('VOYAGER_THEME_FOLDER') && VOYAGER_THEME_FOLDER) {
            return 'themes/' . VOYAGER_THEME_FOLDER . $folder_file;
        }

        $theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

        if (Cookie::get('voyager_theme')) {
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if (isset($theme_cookied->id)) {
                $theme = $theme_cookied;
            }
        }

        define('VOYAGER_THEME_FOLDER', $theme->folder);
        return 'themes/' . $theme->folder . $folder_file;
    }
}

if (!function_exists(theme_folder_url)) {
    function theme_folder_url($folder_file = '')
    {

        if (defined('VOYAGER_THEME_FOLDER') && VOYAGER_THEME_FOLDER) {
            return url('themes/' . VOYAGER_THEME_FOLDER . $folder_file);
        }

        $theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

        if (Cookie::get('voyager_theme')) {
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if (isset($theme_cookied->id)) {
                $theme = $theme_cookied;
            }
        }

        define('VOYAGER_THEME_FOLDER', $theme->folder);
        return url('themes/' . $theme->folder . $folder_file);
    }
}

