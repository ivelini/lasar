<?php

namespace App\Http\Controllers\Api\Backend\Catalog\Pages;

use App\Http\Controllers\Controller;
use App\Repositories\Catalog\ApiUrlSallersRepository;
use App\Repositories\Catalog\LabelImportServiceRepository;
use App\Repositories\Catalog\SallersRepository;
use Illuminate\Http\Request;

class ImportPriceXmlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $sallersRepository = new SallersRepository();
        $sallersForSelectUrl = $sallersRepository->getSallersForSelectUrl();

        $apiUrlSallersRepository = new ApiUrlSallersRepository();
        $urls = $apiUrlSallersRepository->getAllForTableUpdatePrice();

        $labelRepository = new LabelImportServiceRepository();
        $labels = $labelRepository->getAll();

        $index =  [
            'sallersForSelectUrl' => $sallersForSelectUrl,
            'labels' => $labels,
            'selectedUrls' => $urls,
            ];

        return  $index;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return
     */
    public function update(Request $request)
    {
        logger($request->input());
        $method = $request->input('method');

        $result = match ($method) {
            'add' => $this->addUrl($request),
            'del' => $this->delUrl($request),
            'updPrice' => $this->updatePrice($request),
        };

        return $result;
    }

    private function addUrl($request)
    {

        $sellerId = $request->input('sallerId');
        $saller = (new SallersRepository)->getModel($sellerId);

        $url = $saller->apiUrlsSaller()->create(['url' => $request->input('url'), 'label_id' => $request->input('labelId')]);

        $storage = null;
        if(!empty($request->input('storage'))) {
            $storage = $url->storage()->create(['name' => $request->input('storage')]);
        }

        return ['saller' => $saller, 'url' => $url, 'storage' => $storage];
    }

    private function updatePrice($request)
    {
        return ['update' => $request->input()];
    }

    private function delUrl($request)
    {
        $apiUrlSallersRepository = new ApiUrlSallersRepository();
        $url = $apiUrlSallersRepository->getModel($request->input('urlId'));
        $url->delete();

        return ['del' => 'Ok'];
    }
}
