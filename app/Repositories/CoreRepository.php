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

    protected function modelsAttributesToArray($models)
    {
        $models =  $models->map(function ($item) {
            $arr = $item->getAttributes();

            if (count($arr) == 1) {
                $firstKey = array_key_first($arr);
                return $arr[$firstKey];
            }

            return $arr;
        });

        return $models;
    }

    public function getAttributeById($attribute, $id)
    {
        $attribute = $this->startConditions()
            ->where('id', $id)
            ->pluck($attribute)
            ->first();

        return $attribute;

    }

    public function getModelsByAttributesBuilder($attributes)
    {
        $builder = $this->startConditions();
        foreach ($attributes as $key => $value) {
            $builder = $builder->where($key, $value);
        }


        return $builder;
    }
}
