<?php

namespace App\Components;

class Button extends BaseButton
{
    public function outlineColors(): array
    {
        return [];
    }

    public function flatColors(): array
    {
        return [];
    }

    public function defaultColors(): array
    {
        return [
            self::DEFAULT => 'btn',

            'primary' => 'btn btn-primary',

            'secondary' => <<<EOT
                btn-secondary
            EOT,

            'accent' => <<<EOT
                btn btn-accent
            EOT,

            'ghost' => <<<EOT
                btn btn-ghost
            EOT,
        ];
    }

    public function sizes(): array
    {
        return [
            'xs'          => 'btn-xs',
            'sm'          => 'btn-sm',
            self::DEFAULT => '',
            'lg'          => 'btn-lg',
        ];
    }

    public function iconSizes(): array
    {
        return [
            '2xs'         => 'w-2 h-2',
            'xs'          => 'w-3 h-3',
            'sm'          => 'w-3.5 h-3.5',
            self::DEFAULT => 'w-4 h-4',
            'md'          => 'w-4 h-4',
            'lg'          => 'w-5 h-5',
            'xl'          => 'w-6 h-6',
        ];
    }
}
