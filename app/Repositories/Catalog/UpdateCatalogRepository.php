<?php


namespace App\Repositories\Catalog;


use App\Repositories\CoreRepository;
use App\Models\UpdateCatalog as Model;

class UpdateCatalogRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getPathLastModel($type)
    {
        $path = $this->getLastModel($type)->file->path;

        return $path;
    }


    public function getLastModel($type)
    {
        return $this->startConditions()
            ->where('type', $type)
            ->get()
            ->last();
    }

    public function getUploadStatus($type)
    {
        $model = $this->getLastModel($type);

        if (!empty($model)) {
            if($model->is_job_created == true && $model->is_catalog_updated == false && empty($model->error)) {
                return 'loading';
            } elseif ($model->is_job_created == true && $model->is_catalog_updated == true) {
                return 'uploaded';
            } elseif (!empty($model->error)) {
                return 'error';
            }
        }

        return false;
    }
}
