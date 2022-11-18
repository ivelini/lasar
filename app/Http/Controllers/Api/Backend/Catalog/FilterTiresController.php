<?php

namespace App\Http\Controllers\Api\Backend\Catalog;

use App\Http\Controllers\Controller;
use App\Repositories\Catalog\PricesRepository;
use App\Repositories\Catalog\SeasonRepository;
use App\Repositories\Catalog\TiresRepository;
use App\Repositories\Catalog\VendorRepository;
use Illuminate\Http\Request;

class FilterTiresController extends Controller
{
    public function getFilteredData()
    {
        $tiresRepository = new TiresRepository();

        $data = [
            'width'  => $tiresRepository->getModelsTableAttributesToArray('width', 'width', 'width')->all(),
            'height'  => $tiresRepository->getModelsTableAttributesToArray('height', 'height','height')->all(),
            'diameter'  => $tiresRepository->getModelsTableAttributesToArray('diameter', 'diameter','diameter')->all(),
            'seasons'   => (new SeasonRepository)->getSeasonsToFilter(),
            'isSpikes'  => [0, 1],
            'vendors'   => (new VendorRepository)->getVendorsToFilter(),
            'prices'    => (new PricesRepository())->getMinAndMaxPrice(),
        ];

        return $data;
    }
}
