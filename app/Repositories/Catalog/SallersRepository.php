<?php


namespace App\Repositories\Catalog;

use App\Models\Saller as Model;
use App\Repositories\CoreRepository;

class SallersRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getIdExccelColumnName($excelColName)
    {
        $model = $this->startConditions()
            ->select('id', 'key_excel_col')
            ->where('key_excel_col', $excelColName)
            ->first();

        if (!empty($model)) {
            $id = $model->id;
        } else {
            $id = null;
        }

        return $id;
    }
}
