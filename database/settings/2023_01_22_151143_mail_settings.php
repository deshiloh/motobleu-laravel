<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class MailSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mail.from_name', 'Motobleu Paris');
        $this->migrator->add('mail.from_address', 'contact@motobleu-paris.com');
    }
}
