<?php

namespace Beholdr\Sendsay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Beholdr\Sendsay\SendsayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SendsayServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
