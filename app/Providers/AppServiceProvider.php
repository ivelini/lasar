<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'tire' => 'App\Models\Tire',
            'modelPosition' => 'App\Models\ModelPosition',
            'vendor' => 'App\Models\Vendor',
            'vendor_code' => 'App\Models\VendorCode',
            'saller_code' => 'App\Models\SallerCode',
        ]);
    }
}
