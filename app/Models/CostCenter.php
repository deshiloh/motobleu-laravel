<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @mixin IdeHelperCostCenter
 */
class CostCenter extends Model
{
    use HasFactory;

    protected $guarded = [];
}
