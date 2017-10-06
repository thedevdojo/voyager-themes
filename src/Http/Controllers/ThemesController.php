<?php

namespace VoyagerThemes\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use \VoyagerThemes\Models\Theme;
use \VoyagerThemes\Models\ThemeOptions;
use Voyager;
use TCG\Voyager\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;

class ThemesController extends Controller
{
    public function index(){

        // Anytime the admin visits the theme page we will check if we
        // need to add any more themes to the database
        $this->addThemesToDB();
        $themes = Theme::all();

        return view('themes::index', compact('themes'));
    }

    private function getThemesFromFolder(){
    	$themes = array();
        $theme_folder = resource_path('views/themes');

        if(!file_exists($theme_folder)){
            mkdir(resource_path('views/themes'));
        }

        $scandirectory = scandir($theme_folder);

        if(isset($scandirectory)){

            foreach($scandirectory as $folder){
            	//dd($theme_folder . '/' . $folder . '/' . $folder . '.json');
            	$json_file = $theme_folder . '/' . $folder . '/' . $folder . '.json';
                if(file_exists($json_file)){
                    $themes[$folder] = json_decode(file_get_contents($json_file), true);
                    $themes[$folder]['folder'] = $folder;
                    $themes[$folder] = (object)$themes[$folder];
                }
            }

        }

        return (object)$themes;
    }

    private function addThemesToDB(){

        $themes = $this->getThemesFromFolder();

    	foreach($themes as $theme){
    		if(isset($theme->folder)){
    			$theme_exists = Theme::where('folder', '=', $theme->folder)->first();
    			// If the theme does not exist in the database, then update it.
    			if(!isset($theme_exists->id)){
                    $version = isset($theme->version) ? $theme->version : '';
                    Theme::create(['name' => $theme->name, 'folder' => $theme->folder, 'version' => $version]);
                    $this->publishAssets($theme->folder);
    			} else {
    				// If it does exist, let's make sure it's been updated
    				$theme_exists->name = $theme->name;
                    $theme_exists->version = isset($theme->version) ? $theme->version : '';
                    $theme_exists->save();
                    $this->publishAssets($theme->folder);
    			}
    		}
    	}
    }

    public function activate($theme_folder){

        $theme = Theme::where('folder', '=', $theme_folder)->first();

        if(isset($theme->id)){
            $this->deactivateThemes();
            $theme->active = 1;
            $theme->save();
            return redirect()
                ->route("voyager.theme.index")
                ->with([
                        'message'    => "Successfully activated " . $theme->name . " theme.",
                        'alert-type' => 'success',
                    ]);
        } else {
            return redirect()
                ->route("voyager.theme.index")
                ->with([
                        'message'    => "Could not find theme " . $theme_folder . ".",
                        'alert-type' => 'error',
                    ]);
        }

    }

    public function delete(Request $request){
        $theme = Theme::find($request->id);
        if(!isset($theme)){
            return redirect()
                ->route("voyager.theme.index")
                ->with([
                        'message'    => "Could not find theme to delete",
                        'alert-type' => 'error',
                    ]);
        }

        $theme_name = $theme->name;

        // if the folder exists delete it
        if(file_exists(resource_path('views/themes/'.$theme->folder))){
            File::deleteDirectory(resource_path('views/themes/'.$theme->folder), false);
        }

        $theme->delete();

        if(file_exists(public_path('themes/'.$theme->folder))){
            File::deleteDirectory(public_path('themes/'.$theme->folder), false);
        }

        return redirect()
                ->back()
                ->with([
                        'message'    => "Successfully deleted theme " . $theme_name,
                        'alert-type' => 'success',
                    ]);

    }

    public function options($theme_folder){

        $theme = Theme::where('folder', '=', $theme_folder)->first();

        if(isset($theme->id)){

            $options = [];

            return view('themes::options', compact('options', 'theme'));

        } else {
            return redirect()
                ->route("voyager.theme.index")
                ->with([
                        'message'    => "Could not find theme " . $theme_folder . ".",
                        'alert-type' => 'error',
                    ]);
        }
    }

    public function options_save(Request $request, $theme_folder){
        $theme = Theme::where('folder', '=', $theme_folder)->first();

        if(!isset($theme->id)){
            return redirect()
                ->route("voyager.theme.index")
                ->with([
                        'message'    => "Could not find theme " . $theme_folder . ".",
                        'alert-type' => 'error',
                    ]);
        }

        foreach($request->all() as $key => $content){
            if(!$this->stringEndsWith($key, '_details__theme_field') && !$this->stringEndsWith($key, '_type__theme_field') && $key != '_token'){
                $type = $request->{$key.'_type__theme_field'};
                $details = $request->{$key.'_details__theme_field'};
                $row = (object)['field' => $key, 'type' => $type, 'details' => $details];

                $value = $this->getContentBasedOnType($request, 'themes', $row);

                $option = ThemeOptions::where('voyager_theme_id', '=', $theme->id)->where('key', '=', $key)->first();


                // If we already have this key with the Theme ID we can update the value
                if(isset($option->id)){
                    $option->value = $value;
                    $option->save();
                } else {
                    ThemeOptions::create(['voyager_theme_id' => $theme->id, 'key' => $key, 'value' => $value]);
                }
            }
        }

        return redirect()
                ->back()
                ->with([
                        'message'    => "Successfully Saved Theme Options",
                        'alert-type' => 'success',
                    ]);


    }

    function stringEndsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
        (substr($haystack, -$length) === $needle);
    }

    private function deactivateThemes(){
        Theme::query()->update(['active' => 0]);
    }

    private function publishAssets($theme) {
        $theme_path = public_path('themes/'.$theme);
        if(!file_exists($theme_path)){
            mkdir($theme_path);
        }
        File::copyDirectory(resource_path('views/themes/'.$theme.'/assets'), public_path('themes/'.$theme));
        File::copy(resource_path('views/themes/'.$theme.'/'.$theme.'.jpg'), public_path('themes/'.$theme.'/'.$theme.'.jpg'));
    }
}
