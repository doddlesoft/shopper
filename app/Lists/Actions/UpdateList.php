<?php

namespace App\Lists\Actions;

use App\Liste;

class UpdateList
{
    public function perform(Liste $list, string $name): Liste
    {
        return tap($list)->update(['name' => $name]);
    }
}
