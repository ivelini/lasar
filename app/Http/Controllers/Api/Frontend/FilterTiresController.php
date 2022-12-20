<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Catalog\PricesRepository;
use App\Repositories\Catalog\SeasonRepository;
use App\Repositories\Catalog\TiresRepository;
use App\Repositories\Catalog\VendorRepository;
use Illuminate\Http\Request;

class FilterTiresController extends Controller
{
    public function getFilteredParams(): string
    {
        $tiresRepository = new TiresRepository();

        $data = [
            'width'  => $tiresRepository->getModelsTableAttributesToArray('width', 'width', 'width')->all(),
            'height'  => $tiresRepository->getModelsTableAttributesToArray('height', 'height','height')->all(),
            'diameter'  => $tiresRepository->getModelsTableAttributesToArray('diameter', 'diameter','diameter')->all(),
            'seasonId'   => (new SeasonRepository)->getSeasonsToFilter(),
            'isSpikes'  => [0, 1],
            'vendorId'   => (new VendorRepository)->getVendorsToFilter(),
            'prices'    => (new PricesRepository())->getMinAndMaxPrice(),
        ];

        return json_encode($data);
    }

    public function getFilteredTires(Request $request)
    {
        $data = $request->query();

        logger($data);

        $paginatePage = (new PricesRepository)->getPaginationByFilterData($data);

        return json_encode($paginatePage);
    }
}
