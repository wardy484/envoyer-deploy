<?php

namespace Tutorful\EnvoyerDeploy;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Git
{
    public function getCurrentBranch(): string
    {
        $result = Process::run('git rev-parse --abbrev-ref HEAD');

        return trim($result->output());
    }

    /**
     * @return array<string>
     */
    public function getAvailableBranches(): array
    {
        $result = Process::run('git branch -r --no-merged')->output();

        return collect(explode(' ', $result))
            ->unique()
            ->where(fn($value) => ! in_array($value, ['->', 'origin/HEAD', 'origin/master', '']))
            ->map(
                fn($value) => Str::of($value)
                    ->replace('origin/', '')
                    ->trim()
                    ->toString()
            )
            ->toArray();
    }
}
