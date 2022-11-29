<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlTochkamarket extends ImportXml
{
    private $skladName;
    public function __construct($path, $skladName)
    {
        $this->skladName = $skladName;
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
                    'storage' => $this->skladName,
                    'label' => $this->label,
                ];


                $itemsXmlProperties = collect($itemsXml->get('properties'));

                $arr['num'] = $itemsXmlProperties->get('RIMEX_TRADES_CODE')->__toString();
                $arr['code'] = $itemsXmlProperties->get('PRODUCER_TRADES_CODE')->__toString();

                $items->push($arr);
            }
        }

        return $items;
    }
}
