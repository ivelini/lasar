<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Repositories\Catalog\PricesRepository;
use Illuminate\Http\Request;

class testApiController extends Controller
{
    public function test(Request $request) {
        $paginatePage = (new PricesRepository)->getPaginationByFilterData([]);
        dd($paginatePage);
    }
}
