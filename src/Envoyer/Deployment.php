<?php

namespace Tutorful\EnvoyerDeploy\Envoyer;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Deployment
{
    public function __construct(
        public readonly int $id,
        public readonly int $projectId,
        public readonly string $status,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly string $commitHash,
        public readonly string $commitAuthor,
        public readonly string $commitBranch,
        /**
         * @var Collection<int, Process> $processes
         */
        public readonly Collection $processes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): Deployment
    {
        return new Deployment(
            id: (int) $data['id'],
            status: (string) $data['status'],
            projectId: (int) $data['project_id'],
            createdAt: Carbon::parse($data['created_at']),
            updatedAt: Carbon::parse($data['updated_at']),
            commitHash: (string) $data['commit_hash'],
            commitAuthor: (string) $data['commit_author'],
            commitBranch: (string) $data['commit_branch'],
            processes: self::createProcessCollection($data['processes'] ?? []),
        );
    }

    /**
     * @param  array<array<string, mixed>>  $processesData
     * @return Collection<int, Process>
     */
    private static function createProcessCollection(array $processesData): Collection
    {
        return new Collection(array_map(fn($process) => Process::fromArray($process), $processesData));
    }

    public function getUrl(): string
    {
        return "https://envoyer.io/projects/{$this->projectId}/deployments/{$this->id}";
    }

    public function getRunningProcess(): ?Process
    {
        return $this->processes->firstWhere(function (Process $process) {
            return $process->status === 'running';
        });
    }

    public function getDuration(): int
    {
        if ($this->status === 'finished') {
            return $this->createdAt->diffInSeconds($this->updatedAt);
        }

        return $this->createdAt->diffInSeconds(Carbon::now());
    }
}
