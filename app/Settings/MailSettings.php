<?php

namespace app\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public string $from_name;
    public string $from_address;

    public static function group(): string
    {
        return 'mail';
    }
}
