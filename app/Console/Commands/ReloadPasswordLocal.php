<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ReloadPasswordLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reload-password-local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!\App::environment(['local'])) {
            $this->error('Commande non autorisée dans cet environment');
            return false;
        }

        // Mettre le mot de passe de test pour tout le monde pour faciliter les tests en local.
        foreach (User::all() as $user) {
            $user->password = Hash::make('test');
            $user->updateQuietly();
        }

        $this->info('Mots de passe changés.');
    }
}
