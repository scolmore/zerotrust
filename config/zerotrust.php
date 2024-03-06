<?php

return [

    /**
     * Should zero trust be enabled?
     */
    'enabled' => env('ZEROTRUST_ENABLED', false),

    /**
     * The title that will show on the zero trust screen(s).
     */
    'title' => env('ZEROTRUST_AZURE_TITLE', ''),

    /**
     * The application name that will show on the zero trust screen(s).
     */
    'application_name' => env('ZEROTRUST_APP_NAME', env('APP_NAME')),

    /*
     * When set to true, a check that the user exists in the database will be performed
     * and if found, will automatically log the user in to the application.
     */
    'auto_login' => env('ZEROTRUST_AUTO_LOGIN', false),

    /**
     * The key that the session data will be stored in.
     * Do not change this unless you have a good reason to.
     */
    'session_key' => env('ZEROTRUST_SESSION_KEY', 'zero-trust'),

    /*
     * Add all the Azure active directories that you want to use in this application.
     * If you only have one, you will not see the AD selection screen and will be sent straight to the login screen.
     */
    'directories' => [
        [
            'name' => env('ZEROTRUST_AZURE_NAME', 'Name not provided'),
            'tenant_id' => env('ZEROTRUST_AZURE_TENANT_ID'),
            'client_id' => env('ZEROTRUST_AZURE_CLIENT_ID'),
            'secret' => env('ZEROTRUST_AZURE_SECRET'),

            /*
             * If you have multiple domains in the AD, you can restrict access to only certain domains.
             * For example if you have example@example.com and example@foo.com, you can restrict access to only example.com
             * by adding example.com to your env file.
             *
             * For example:
             * ZEROTRUST_AZURE_ALLOWED_DOMAINS="example.com,foo.com"
             *
             * Leaving it blank will allow all domains.
             */
            'allowed_domains' => explode(',', env('ZEROTRUST_AZURE_ALLOWED_DOMAINS', '')),
        ],
    ],

    /**
     * The user model and email column to use for the auto-login feature.
     */
    'model' => \App\Models\User::class,
    'email_column' => 'email',
];
