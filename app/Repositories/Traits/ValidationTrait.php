<?php


namespace App\Repositories\Traits;


trait ValidationTrait
{
    public function checkExist($model, $returnAttribute = null)
    {
        if (!empty($model) && $returnAttribute != null) {
            $returnedElement = $model->$returnAttribute;
        }
        elseif (!empty($model)) {
            $returnedElement = $model;
        }
        else {
            $returnedElement = null;
        }

        return $returnedElement;
    }
}
