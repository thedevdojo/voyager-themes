<?php

namespace VoyagerThemes;

use Illuminate\Http\Request;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Models\MenuItem;
use Illuminate\Events\Dispatcher;
use TCG\Voyager\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class VoyagerThemesServiceProvider extends ServiceProvider
{
    private $models = [
            'Theme',
            'ThemeOptions',
        ];

    /**
     * Register is loaded every time the voyager themes hook is used.
     *
     * @return none
     */
    public function register()
    {
        if (request()->is(config('voyager.prefix')) || request()->is(config('voyager.prefix').'/*')) {
            $this->addThemesTable();

            app(Dispatcher::class)->listen('voyager.menu.display', function ($menu) {
                $this->addThemeMenuItem($menu);
            });

            app(Dispatcher::class)->listen('voyager.admin.routing', function ($router) {
                $this->addThemeRoutes($router);
            });
        }

        // load helpers
        @include __DIR__.'/helpers.php';
    }

    /**
     * Register the menu options and selected theme.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadModels();
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'themes');

        $theme = \VoyagerThemes\Models\Theme::where('active', '=', 1)->first();

        view()->share('theme', $theme);

        // Make sure we have an active theme
        if (isset($theme)) {
            $this->loadViewsFrom(public_path('themes/'.$theme->folder), 'theme');
        }
        $this->loadViewsFrom(public_path('themes'), 'themes_folder');
    }

    /**
     * Admin theme routes.
     *
     * @param $router
     */
    public function addThemeRoutes($router)
    {
        $namespacePrefix = '\\VoyagerThemes\\Http\\Controllers\\';
        $router->get('themes', ['uses' => $namespacePrefix.'ThemesController@index', 'as' => 'theme.index']);
        $router->get('themes/activate/{theme}', ['uses' => $namespacePrefix.'ThemesController@activate', 'as' => 'theme.activate']);
        $router->get('themes/options/{theme}', ['uses' => $namespacePrefix.'ThemesController@options', 'as' => 'theme.options']);
        $router->post('themes/options/{theme}', ['uses' => $namespacePrefix.'ThemesController@options_save', 'as' => 'theme.options.post']);
        $router->get('themes/options', function () {
            return redirect(route('voyager.theme.index'));
        });
        $router->delete('themes/delete', ['uses' => $namespacePrefix.'ThemesController@delete', 'as' => 'theme.delete']);
    }

    /**
     * Adds the Theme icon to the admin menu.
     *
     * @param TCG\Voyager\Models\Menu $menu
     */
    public function addThemeMenuItem(Menu $menu)
    {
        if ($menu->name == 'admin') {
            $url = route('voyager.theme.index', [], false);
            $menuItem = $menu->items->where('url', $url)->first();
            if (is_null($menuItem)) {
                $menu->items->add(MenuItem::create([
                    'menu_id' => $menu->id,
                    'url' => $url,
                    'title' => 'Themes',
                    'target' => '_self',
                    'icon_class' => 'voyager-paint-bucket',
                    'color' => null,
                    'parent_id' => null,
                    'order' => 98,
                ]));
                $this->ensurePermissionExist();

                return redirect()->back();
            }
        }
    }

    /**
     * Loads all models in the src/Models folder.
     *
     * @return none
     */
    private function loadModels()
    {
        foreach ($this->models as $model) {
            @include __DIR__.'/Models/'.$model.'.php';
        }
    }

    /**
     * Add Permissions for themes if they do not exist yet.
     *
     * @return none
     */
    protected function ensurePermissionExist()
    {
        $permission = Permission::firstOrNew([
            'key' => 'browse_themes',
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

    /**
     * Add the necessary Themes tables if they do not exist.
     */
    private function addThemesTable()
    {
        if (!Schema::hasTable('voyager_themes')) {
            Schema::create('voyager_themes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('folder')->unique();
                $table->boolean('active')->default(false);
                $table->string('version')->default('');
                $table->timestamps();
            });

            Schema::create('voyager_theme_options', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('voyager_theme_id')->unsigned()->index();
                $table->foreign('voyager_theme_id')->references('id')->on('voyager_themes')->onDelete('cascade');
                $table->string('key');
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }
}
