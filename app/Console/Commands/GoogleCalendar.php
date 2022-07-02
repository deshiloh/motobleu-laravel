<?php

namespace App\Console\Commands;

use Google\Exception;
use Illuminate\Console\Command;
use Google\Client;
use Illuminate\Support\Facades\Storage;

class GoogleCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create credentials for Google Calendar with OAuth';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $client = new Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes('https://www.googleapis.com/auth/calendar.events');
        $client->setAuthConfig(config('google-calendar.auth_profiles.oauth.credentials_json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Request authorization from the user.
        $this->newLine();
        $this->info("Open the following link in your browser: ");
        $this->newLine();
        $this->line($client->createAuthUrl());

        $authCode = $this->ask('Enter verification code: ');

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }

        // Save the token to a file.
        if (!Storage::exists(config('google-calendar.auth_profiles.oauth.token_json'))) {
            Storage::disk('local')->put(
                config('google-calendar.auth_profiles.oauth.token_json'),
                json_encode($client->getAccessToken())
            );
        }

        return 0;
    }
}
