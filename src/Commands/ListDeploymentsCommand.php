<?php

namespace Tutorful\EnvoyerDeploy\Commands;

use Illuminate\Console\Command;
use Tutorful\EnvoyerDeploy\Envoyer\Deployment;
use Tutorful\EnvoyerDeploy\Envoyer\Envoyer;

use function Laravel\Prompts\table;

class ListDeploymentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envoyer:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List deployments for envoyer site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private Envoyer $envoyer,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $project = config('envoyer_deploy.default_project');
        $projectId = $this->envoyer->getProjectId($project);

        $deployments = $this->envoyer->getDeployments($projectId);

        table(
            ['Started', 'Committer', 'Branch', 'Commit', 'Duration', 'Status'],
            $deployments->map(function (Deployment $deployment) {
                return [
                    $deployment->createdAt->format('Y-m-d H:i:s'),
                    $deployment->commitAuthor,
                    $deployment->commitBranch,
                    $deployment->commitHash,
                    $deployment->getDuration() . 's',
                    $deployment->status,
                ];
            })->toArray()
        );

        return 1;
    }
}
