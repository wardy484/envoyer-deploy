<?php

namespace Tutorful\EnvoyerDeploy\Envoyer;

class Process
{
    public function __construct(
        public readonly int $id,
        public readonly int $sequence,
        public readonly string $status,
        public readonly string $name,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): Process
    {
        return new Process(
            id: (int) $data['id'],
            sequence: (int) $data['sequence'],
            status: (string) $data['status'],
            name: (string) $data['name'],
        );
    }
}
