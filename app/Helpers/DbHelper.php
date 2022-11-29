<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Schema;

class DbHelper
{
    private $model;


    public function __construct($modelVsModelName = 'User')
    {
        $this->setModel($modelVsModelName);
    }

    public function setModel($modelVsModelName)
    {
        $model = $modelVsModelName;

        if (is_object($model) == false) {

            $modelClass = 'App\\Models\\' .$model;
            $model = new $modelClass();
        }

        $this->model =  $model;
    }

    public function updateRelation(string $relationName, array $data)
    {

        dd($this->model->$relationName(), $this->model);
    }

    public function save(array $data)
    {
        return $this->model->create($data);
    }

    public function saveAll(array $dataAll)
    {
        $dataAll = collect($dataAll);
        $dataAll->each(function ($item) {
            $this->model->save($item);
        });

        return true;
    }

    public function update(array $data)
    {
        foreach($data as $key => $value) {
            $this->model->$key = $value;
        }

        return $this->model->save();
    }


}
