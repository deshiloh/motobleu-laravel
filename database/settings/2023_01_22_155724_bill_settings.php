<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class BillSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bill.entreprises_xls_file', []);
        $this->migrator->add('bill.entreprises_cost_center_facturation', []);
        $this->migrator->add('bill.entreprises_command_field', []);
    }
}
