<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Itemable extends Pivot
{
    protected $table = 'itemables';
    protected $guarded = [];
}
