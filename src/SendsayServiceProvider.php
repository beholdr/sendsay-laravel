<?php

namespace Beholdr\Sendsay;

use Illuminate\Support\Facades\Mail;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SendsayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('sendsay')
            ->hasConfigFile();
    }

    public function packageBooted()
    {
        Mail::extend('sendsay', fn () => new SendsayTransport);
    }
}
