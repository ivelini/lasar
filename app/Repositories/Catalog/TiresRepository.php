<?php


namespace App\Repositories\Catalog;

use App\Models\Tire as Model;
use App\Repositories\CoreRepository;

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

    public function getByVendor($vendorId)
    {

    }
}
