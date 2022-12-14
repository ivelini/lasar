<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlShininvest extends ImportXml
{
    public function __construct($path, $skladName, $apiUrlSallerId)
    {
        $this->skladName = $skladName;
        $this->apiUrlSallerId = $apiUrlSallerId;
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        foreach ($xml as $item) {

            $item = collect($item)->map(function ($volume) {
                return $volume->__toString();
            });

            $items->push([
                'title' => $item->get('descr'),
                'num' => $item->get('code'),
                'code' => $item->get('producer_code'),
                'count' => $item->get('quantity'),
                'price' => $item->get('price_mic'),
                'storage' => !empty($this->skladName) ? $this->skladName : 'Склад не установлен',
                'label' => $this->label
            ]);
        }

        return $items;
    }
}
