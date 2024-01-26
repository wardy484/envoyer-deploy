<?php

namespace Tutorful\EnvoyerDeploy\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Tutorful\EnvoyerDeploy\EnvoyerDeployServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Factory::guessFactoryNamesUsing(
        //     fn (string $modelName) => 'VendorName\\Skeleton\\Database\\Factories\\'.class_basename($modelName).'Factory'
        // );
    }

    protected function getPackageProviders($app)
    {
        return [
            EnvoyerDeployServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
