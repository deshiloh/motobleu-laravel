<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperPage
 */
class Page extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];
    public array $translatable = ['title', 'content', 'slug'];
}
