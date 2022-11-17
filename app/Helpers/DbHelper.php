<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Schema;

class DbHelper
{
    private $model;

    public function __construct($modelVsModelName)
    {
        $this->model = $this->checkModelVsModelName($modelVsModelName);
    }

    private function checkModelVsModelName($modelVsModelName)
    {
        $model = $modelVsModelName;

        if (is_object($model) == false) {

            $modelClass = 'App\\Models\\' .$model;
            $model = new $modelClass();
        }

        return $model;
    }

    public function save(array $data)
    {
        return $this->model->create($data);
    }

    public function saveAll(array $dataAll)
    {
        $dataAll = collect($dataAll);
        $dataAll->each(function ($item) {
            $this->save($item);
        });

        return true;
    }

    public function update($model, array $data)
    {
        foreach($data as $key => $value) {
            $model->$key = $value;
        }

        $model->save();

        return true;
    }

}
