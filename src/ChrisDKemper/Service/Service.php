<?php namespace ChrisDKemper\Service;

use
    ChrisDKemper\Repository\Repository
;

class Service
{
    protected
        $repository
    ;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
}