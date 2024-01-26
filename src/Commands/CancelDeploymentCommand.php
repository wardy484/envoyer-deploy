<?php

namespace Tutorful\EnvoyerDeploy\Commands;

use Illuminate\Console\Command;
use Tutorful\EnvoyerDeploy\Envoyer\Envoyer;

use function Laravel\Prompts\info;

class CancelDeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envoyer:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel most recent running deployment for envoyer site';

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

        $deployment = $this->envoyer->getLatestDeployment($projectId);

        if ($deployment->status !== 'running') {
            $this->error('No running deployments found');

            return 1;
        }

        $this->envoyer->cancelDeployment($deployment);

        info("Deployment {$deployment->id} cancelled: {$deployment->getUrl()}");

        return 1;
    }
}
