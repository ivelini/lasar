<?php


namespace App\Repositories\Catalog;

use App\Models\Storage as Model;
use App\Repositories\CoreRepository;

class StorageRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getIdAndName()
    {
        $idAndName = $this->startConditions()->select('id', 'name')->get();

        $idAndName = $idAndName->map(function ($item) {
            return [ 'id' => $item->id, 'name' => $item->name];
            });

        return $idAndName;
    }

    public function getAttributeFromAttributeValue($name, $value, $getAttribute = 'id')
    {
        $model =  $this->startConditions()
            ->where($name, $value)
            ->first();

        if (!empty($model)) {
            $attr = $model->$getAttribute;
        } else {
            $attr = null;
        }

        return $attr;
    }

    public function findModelByAttributes($attributes)
    {
        $model =  $this->startConditions();
        foreach ($attributes as $key => $value ) {
            $model = $model->where($key, $value);
        }

        $model = $model->first();

        return $model;
    }

    public function getIndex()
    {
        $models = $this->startConditions()
            ->with('apiUrlSaller.saller')
            ->get();

        $models->map(function ($item) {
            $item->sallerName = $item->apiUrlSaller->saller->name;
            $item->sallerId = $item->apiUrlSaller->saller->id;
            return $item;
        });

        $items = $this->modelsAttributesToArray($models);

        return $items;
    }
}
