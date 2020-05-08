<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ListItem extends Pivot
{
    protected $table = 'item_list';
    protected $guarded = [];
}
