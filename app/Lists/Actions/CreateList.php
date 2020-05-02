<?php

namespace App\Lists\Actions;

use App\Liste;

class CreateList
{
    public function perform(string $name): Liste
    {
        return Liste::create(['name' => $name]);
    }
}
