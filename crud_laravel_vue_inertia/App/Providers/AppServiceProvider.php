<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider{

  /**
   * Register any application services.
   */
  public function register(): void{
    Inertia::setRootView('inertia');
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void{
    Inertia::setRootView('inertia');
  }

}
