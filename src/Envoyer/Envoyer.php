<?php

namespace Tutorful\EnvoyerDeploy\Envoyer;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Envoyer
{
    private PendingRequest $client;

    private string $baseUrl;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $token = config('envoyer_deploy.api_token');

        if ($token === null || $token === '') {
            throw new \Exception('Envoyer API token not set');
        }

        $this->client = Http::withToken($token);
        $this->baseUrl = config('envoyer_deploy.base_url');
    }

    public function getProjectId(string $projectName): int
    {
        /** @var array<string, mixed> $projects */
        $projects = $this->client->get("{$this->baseUrl}/projects")->json('projects');

        $project = collect($projects)->firstWhere(function ($project) use ($projectName) {
            return $project['name'] === $projectName;
        });

        if ($project === null) {
            throw new \Exception("Project '{$projectName}' does not exist");
        }

        return $project['id'];
    }

    public function triggerDeployment(int $projectId, string $branch): Deployment
    {
        $this->client->post("{$this->baseUrl}/projects/{$projectId}/deployments", [
            'from' => 'branch',
            'branch' => $branch,
        ]);

        return $this->getLatestDeployment($projectId);
    }

    /**
     * @return Collection<int, Deployment>
     */
    public function getDeployments(int $projectId): Collection
    {
        /** @var array<int, mixed> $deployments */
        $deployments = $this->client
            ->get("{$this->baseUrl}/projects/{$projectId}/deployments")
            ->json('deployments');

        return collect($deployments)
            ->map(fn($deployment) => Deployment::fromArray($deployment))
            ->sortByDesc(fn(Deployment $deployment) => $deployment->createdAt);
    }

    public function getLatestDeployment(int $projectId): Deployment
    {
        $deployments = $this->client
            ->get("{$this->baseUrl}/projects/{$projectId}/deployments")
            ->json('deployments');

        return Deployment::fromArray($deployments[0]);
    }

    public function getDeployment(int $projectId, int $deploymentId): Deployment
    {
        $deployment = $this->client
            ->get("{$this->baseUrl}/projects/{$projectId}/deployments/{$deploymentId}")
            ->json('deployment');

        return Deployment::fromArray($deployment);
    }

    public function cancelDeployment(Deployment $deployment): void
    {
        $this->client
            ->delete("{$this->baseUrl}/projects/{$deployment->projectId}/deployments/{$deployment->id}")
            ->json('deployment');
    }

    public function buildDeploymentUrl(int $projectId, int $deploymentId): string
    {
        return "https://envoyer.io/projects/{$projectId}/deployments/{$deploymentId}";
    }
}
