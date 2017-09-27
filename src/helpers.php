<?php


if (!function_exists(theme_field)){

	function theme_field($type, $key, $title, $content = '', $details = '', $placeholder = '', $required = 1){
		
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

	function theme($key){
		$theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();
		return $theme->options->where('key', '=', $key)->first()->value;
	}

}