<?php


namespace App\Repositories\Catalog;

use App\Models\Season as Model;
use App\Repositories\CoreRepository;

class SeasonRepository extends CoreRepository
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

    public function getSeasonsToFilter()
    {
        $models = $this->startConditions()
            ->select('id', 'name')
            ->get();

        return $this->modelsAttributesToArray($models)->all();
    }
}
