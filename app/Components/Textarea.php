<?php

namespace App\Components;

class Textarea extends Input
{
    protected function getView(): string
    {
        return 'wireui::components.textarea';
    }
}
