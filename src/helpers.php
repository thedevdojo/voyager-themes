<?php


if (!function_exists(theme_field)){

	function theme_field($type, $key, $title, $content = '', $details = '', $placeholder = '', $required = 0){

		$theme = \VoyagerThemes\Models\Theme::where('folder', '=', ACTIVE_THEME_FOLDER)->first();

		$option_exists = $theme->options->where('key', '=', $key)->first();

		if(isset($option_exists->value)){
			$content = $option_exists->value;
		}

		$row = (object)['required' => $required, 'field' => $key, 'type' => $type, 'details' => $details, 'display_name' => $placeholder];
		$dataTypeContent = (object)[$key => $content];
		$label = '<label for="'. $key . '">' . $title . '<span class="how_to">You can reference this value with <code>theme(\'' . $key . '\')</code></span></label>';
		$details = '<input type="hidden" value="' . $details . '" name="' . $key . '_details__theme_field">';
		$type = '<input type="hidden" value="' . $type . '" name="' . $key . '_type__theme_field">';
		return $label . app('voyager')->formField($row, '', $dataTypeContent) . $details . $type . '<hr>';
	}

}

if (!function_exists(theme)){

	function theme($key, $default = ''){
		$theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

		if(Cookie::get('voyager_theme')){
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if(isset($theme_cookied->id)){
                $theme = $theme_cookied;
            }
        }

		$value = $theme->options->where('key', '=', $key)->first();

		if(isset($value)) {
			return $value->value;
		}

		return $default;
	}

}

if(!function_exists(theme_folder)){
	function theme_folder($folder_file = ''){

		if(defined('VOYAGER_THEME_FOLDER') && VOYAGER_THEME_FOLDER){
			return 'themes/' . VOYAGER_THEME_FOLDER . $folder_file;
		}

		$theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

		if(Cookie::get('voyager_theme')){
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if(isset($theme_cookied->id)){
                $theme = $theme_cookied;
            }
        }

		define('VOYAGER_THEME_FOLDER', $theme->folder);
		return 'themes/' . $theme->folder . $folder_file;
	}
}

if(!function_exists(theme_folder_url)){
	function theme_folder_url($folder_file = ''){

		if(defined('VOYAGER_THEME_FOLDER') && VOYAGER_THEME_FOLDER){
			return url('themes/' . VOYAGER_THEME_FOLDER . $folder_file);
		}

		$theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

		if(Cookie::get('voyager_theme')){
            $theme_cookied = \VoyagerThemes\Models\Theme::where('folder', '=', Cookie::get('voyager_theme'))->first();
            if(isset($theme_cookied->id)){
                $theme = $theme_cookied;
            }
        }

		define('VOYAGER_THEME_FOLDER', $theme->folder);
		return url('themes/' . $theme->folder . $folder_file);
	}
}
