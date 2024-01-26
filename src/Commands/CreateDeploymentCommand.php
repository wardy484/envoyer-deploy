<?php

namespace Tutorful\EnvoyerDeploy\Commands;

use Illuminate\Console\Command;
use Laravel\Prompts\Progress;
use Tutorful\EnvoyerDeploy\Envoyer\Deployment;
use Tutorful\EnvoyerDeploy\Envoyer\Envoyer;
use Tutorful\EnvoyerDeploy\Git;

use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\suggest;

class CreateDeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envoyer:deploy {--m|main : Deploy to main branch} {--f|force : Skip branch selection prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy application to envoyer site';

    /**
     * Flag used to indicate a deployment in progress
     */
    private bool $deploying = false;

    /**
     * @var \Laravel\Prompts\Progress<int>
     */
    private Progress $progress;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private Git $git,
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
        $branch = $this->option('force')
            ? $this->getDefaultBranch()
            : suggest(
                "Select a branch to deploy to {$project}",
                options: $this->git->getAvailableBranches(),
                default: $this->getDefaultBranch(),
            );

        $deployment = $this->envoyer->triggerDeployment($projectId, $branch);
        $this->deploying = true;

        info("Deployment started: {$deployment->getUrl()}");

        $deployment = $this->envoyer->getDeployment($projectId, $deployment->id);
        $previousProcess = $deployment->processes->first();

        if ($previousProcess === null) {
            $this->error("No processes found for deployment {$deployment->id}");
            exit(1);
        }

        $this->progress = progress(
            label: "Deploying {$branch} to {$project}",
            steps: $deployment->processes->count(),
            hint: $previousProcess->name,
        );

        $this->progress->start();

        while ($this->deploying) {
            $deployment = $this->envoyer->getDeployment($projectId, $deployment->id);

            match ($deployment->status) {
                'finished' => $this->deploymentFinished($deployment),
                'error' => $this->deploymentErrored($deployment),
                default => $this->processDeployment($deployment),
            };

            sleep(2);
        }

        return 1;
    }

    private function processDeployment(Deployment $deployment): void
    {
        $currentProcess = $deployment->getRunningProcess();

        if ($currentProcess) {
            $steps = $currentProcess->sequence - $this->progress->progress;
            $this->progress->advance($steps);
            $this->progress->hint($currentProcess->name);
        }
    }

    private function deploymentFinished(Deployment $deployment): void
    {
        $this->deploying = false;
        $this->progress->finish();
        info("Deployment {$deployment->id} finished");
    }

    private function deploymentErrored(Deployment $deployment): void
    {
        $this->deploying = false;
        $this->progress->finish();
        $this->error("Deployment {$deployment->id}, see errors: {$deployment->getUrl()}");
    }

    private function getDefaultBranch(): string
    {
        return $this->option('main')
            ? config('envoyer_deploy.default_branch')
            : $this->git->getCurrentBranch();
    }
}
