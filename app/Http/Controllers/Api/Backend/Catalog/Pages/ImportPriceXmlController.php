<?php

namespace App\Http\Controllers\Api\Backend\Catalog\Pages;

use App\Helpers\UploadFileHelper;
use App\Http\Controllers\Controller;
use App\Models\ApiUrlSaller;
use App\Repositories\Catalog\ApiUrlSallersRepository;
use App\Repositories\Catalog\LabelImportServiceRepository;
use App\Repositories\Catalog\SallersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $storage = $url->storage()->create([
                'is_manual' => true,
                'name' => $request->input('storage')
            ]);
        }

        return ['saller' => $saller, 'url' => $url, 'storage' => $storage];
    }

    private function updatePrice($request)
    {

        $apiUrl = ApiUrlSaller::all()->find($request->input('urlId'));

        $uploadFileHelper = new UploadFileHelper();

        $path = $uploadFileHelper
            ->setRelationModel($apiUrl)
            ->setLink($apiUrl->url)
            ->setPath('tmp\xml')
            ->upload();

        $importMethodName = 'App\Services\ImportCatalogService\Sallers\\' . $apiUrl->label->name;
        $storage = !empty($apiUrl->storage) ? $apiUrl->storage->name : null ;

        $importPriceMethod = new $importMethodName($path, $storage, $apiUrl->id);
        $importPriceMethod->importPrice();

        return ['update' => $request->input('urlId')];
    }

    public function download(Request $request)
    {
        return Storage::get('tmp/' . $request->query('fileName'));

    }

    private function delUrl($request)
    {
        $apiUrlSallersRepository = new ApiUrlSallersRepository();
        $url = $apiUrlSallersRepository->getModel($request->input('urlId'));
        $url->delete();

        return ['del' => 'Ok'];
    }
}
