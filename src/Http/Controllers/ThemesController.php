<?php

namespace VoyagerThemes\Http\Controllers;

use Illuminate\Http\Request;
use \VoyagerThemes\Models\Theme;
use Voyager;

class ThemesController extends \App\Http\Controllers\Controller
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
        $theme_folder = public_path('themes');
        
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
    				Theme::create(['name' => $theme->name, 'folder' => $theme->folder]);
    			} else {
    				// If it does exist, let's make sure it's been updated
    				$theme_exists->name = $theme->name;
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

    public function options_save(Request $request){
        dd((array)$request);
    }

    private function deactivateThemes(){
        Theme::query()->update(['active' => 0]);
    }
}
