<?php


use Illuminate\Events\Dispatcher;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class VoyagerThemesServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function boot(\Illuminate\Routing\Router $router, Dispatcher $events)
	{
		$events->listen('voyager.admin.routing', [$this, 'addThemeRoutes']);
		$events->listen('voyager.menu.display', [$this, 'addThemeMenuItem']);

		$this->loadViewsFrom(public_path('themes'), 'theme');

		$router->get('/admin/themes', function () {
	  		return 'Themes section';
		});
	}

	public function addThemeroutes($router)
    {
        $namespacePrefix = '\\Hooks\\VoyagerThemes\\Http\\Controllers\\';
        $router->get('themes', ['uses' => $namespacePrefix.'ThemesController@index', 'as' => 'themes']);
    }

	public function addThemeMenuItem(Menu $menu)
	{
	    if ($menu->name == 'admin') {
	        $url = route('voyager.themes', [], false);
	        $menuItem = $menu->items->where('url', $url)->first();
	        if (is_null($menuItem)) {
	            $menu->items->add(MenuItem::create([
	                'menu_id'    => $menu->id,
	                'url'        => $url,
	                'title'      => 'Themes',
	                'target'     => '_self',
	                'icon_class' => 'voyager-paint-bucket',
	                'color'      => null,
	                'parent_id'  => null,
	                'order'      => 98,
	            ]));
	            $this->ensurePermissionExist();
	            $this->addThemeTable();
	        }
	    }
	}

	protected function ensurePermissionExist()
    {
        $permission = Permission::firstOrNew([
            'key'        => 'browse_themes',
            'table_name' => 'admin',
        ]);
        if (!$permission->exists) {
            $permission->save();
            $role = Role::where('name', 'admin')->first();
            if (!is_null($role)) {
                $role->permissions()->attach($permission);
            }
        }
    }

    private function addThemeTable(){
    	if(Schema::hasTable('voyager_theme')){
	    	Schema::create('voyager_theme', function (Blueprint $table) {
	            $table->increments('id');
				$table->string('name');
				$table->string('folder')->unique();
				$table->boolean('active')->default(false);
				$table->timestamps();
	        });

	    	Schema::create('voyager_theme_options', function (Blueprint $table) {
	            $table->increments('id');
	            $table->string('key');
	            $table->text('value');
	            $table->timestamp('created_at')->nullable();
	        });

	    }
    }
}
