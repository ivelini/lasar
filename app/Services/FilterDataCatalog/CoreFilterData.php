<?php


namespace App\Services\FilterDataCatalog;


use Illuminate\Support\Collection;

abstract class CoreFilterData
{

    private $next;


    public function nextFilter(CoreFilterData $nextFilter): CoreFilterData
    {
        $this->next = $nextFilter;

        return $nextFilter;
    }

    abstract function getData();
}
