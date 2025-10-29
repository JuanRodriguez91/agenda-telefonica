<?php

namespace App\Repository;

class HomeRepository extends AbstractRepository
{

    public function checkConnection(): void
    {
        $this->getConnection();
    }
}