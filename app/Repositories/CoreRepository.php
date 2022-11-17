<?php

namespace App\Repositories;


use App\Repositories\Traits\ValidationTrait;

abstract class CoreRepository
{
    use ValidationTrait;

    protected $model;

    abstract public function getModelClass();

    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    public function startConditions()
    {
        return clone $this->model;
    }
}
