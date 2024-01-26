<?php

namespace Tutorful\EnvoyerDeploy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tutorful\EnvoyerDeploy\EnvoyerDeployServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
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
