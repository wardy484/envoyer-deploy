<?php

return [
    /**
     * API Token for Laravel Envoyer
     */
    'api_token' => env('ENVOYER_API_TOKEN', 'add-token-here'),

    /**
     * The base URL for the Envoyer API
     */
    'base_url' => env('ENVOYER_BASE_URL', 'https://envoyer.io/api'),

    /**
     * The name of your default project you wish to deploy to
     */
    'default_project' => env('ENVOYER_DEFAULT_PROJECT', 'add-project-here'),

    /**
     * The branch used with the --main flag
     */
    'default_branch' => env('ENVOYER_DEFAULT_BRANCH', 'master'),
];
