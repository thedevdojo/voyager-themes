<?php

// if (!isset($theme)) {
//     $theme = Hooks\Models\VoyagerThemes\Theme::where('active', '=', 1)->first()->folder;
//     View::share('theme', $theme);
// }
// 
if (!function_exists(theme_field)){

	function theme_field($type, $field, $content = '', $details = '', $placeholder = '', $required = 1){
		$row = (object)['required' => $required, 'field' => $field, 'type' => $type, 'details' => $details, 'display_name' => $placeholder];
		$dataTypeContent = (object)[$field => $content];
		return app('voyager')->formField($row, '', $dataTypeContent);
	}

}