<?php

namespace Tutorful\EnvoyerDeploy;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tutorful\EnvoyerDeploy\Commands\CancelDeploymentCommand;
use Tutorful\EnvoyerDeploy\Commands\CreateDeploymentCommand;
use Tutorful\EnvoyerDeploy\Commands\ListDeploymentsCommand;

class EnvoyerDeployServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('envoyer_deploy')
            ->hasConfigFile()
            ->hasCommand(CancelDeploymentCommand::class)
            ->hasCommand(ListDeploymentsCommand::class)
            ->hasCommand(CreateDeploymentCommand::class);
    }
}
