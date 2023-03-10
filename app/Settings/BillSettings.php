<?php

namespace app\Settings;

use Spatie\LaravelSettings\Settings;

class BillSettings extends Settings
{
    public array $entreprises_xls_file = [];
    public array $entreprises_cost_center_facturation = [];
    public array $entreprise_without_command_field = [];

    public static function group(): string
    {
        return 'bill';
    }
}
