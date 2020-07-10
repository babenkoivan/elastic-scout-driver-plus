<?php

namespace ElasticScoutDriverPlus\Tests\App;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider  extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Mixed::bootSearchable();
    }
}
