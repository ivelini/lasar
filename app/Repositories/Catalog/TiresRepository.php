<?php


namespace App\Repositories\Catalog;

use App\Models\Tire;
use App\Models\Tire as Model;
use App\Repositories\CoreRepository;
use Barryvdh\Debugbar\Facades\Debugbar;

class TiresRepository extends CoreRepository
{
    const RELATION_TYPE = 'tire';

    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getIdFromNum($num)
    {
        $model = $this->startConditions()
            ->select('id', 'num')
            ->where('num', $num)
            ->first();

        if (!empty($model)) {
            $id = $model->id;
        } else {
            $id = null;
        }

        return $id;
    }

    public function getModelsTableAttributesToArray($unique = '', $order = '', ...$attributes)
    {
        $models = $this->startConditions()
            ->select($attributes);

            if (!empty($order)) $models = $models->orderBy($order);

            $models = $models->get();

            if (!empty($unique)) $models = $models->unique($unique)->values();

        $models = $this->modelsAttributesToArray($models);

        return $models;
    }
}
