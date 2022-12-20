<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlTochkamarket extends ImportXml
{
    public function __construct($path, $skladName, $apiUrlSallerId)
    {
        $this->skladName = $skladName;
        $this->apiUrlSallerId = $apiUrlSallerId;
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        foreach ($xml as $itemsXml) {
            $itemsXml = collect($itemsXml);

            $type = $itemsXml->get('type')->__toString();
            if ($type == 'tires') {

                $arr = [
                    'title' => $itemsXml->get('name')->__toString(),
                    'count' => $itemsXml->get('quantity')->__toString(),
                    'price' => $itemsXml->get('mrc_price')->__toString(),
                    'storage' => !empty($this->skladName) ? $this->skladName : 'Склад не установлен',
                    'label' => $this->label,
                ];


                $itemsXmlProperties = collect($itemsXml->get('properties'));


                $arr['num'] = $itemsXmlProperties->get('RIMEX_TRADES_CODE')->__toString();
                $arr['code'] = !empty($itemsXmlProperties->get('PRODUCER_TRADES_CODE')) ?
                    $itemsXmlProperties->get('PRODUCER_TRADES_CODE')->__toString() : null;

                $items->push($arr);
            }
        }

        return $items;
    }
}
