<?php

namespace App\Lists\Actions;

use App\Liste;

class DeleteList
{
    public function perform(Liste $list)
    {
        $list->delete();
    }
}
