<?php

namespace App\Entity;

interface HasNestedRelations
{
    public function refreshNestedRelations(): void;
}
