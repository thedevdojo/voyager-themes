<?php

class ThemeServiceProvider extends \Illuminate\Support\ServiceProvider
{
  public function boot(\Illuminate\Routing\Router $router)
  {
    $router->get('/admin/themes', function () {
      return 'Themes section';
    });
  }
}
