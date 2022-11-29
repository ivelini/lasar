<?php


namespace App\Services\ImportCatalogService\Sallers;

use App\Services\ImportCatalogService\ImportXml;


class ImportXmlSvrauto extends ImportXml
{
    public function __construct($path)
    {
        parent::__construct($path);
    }

    function getItemsFromXml($xml) {
        $items = collect();
        foreach ($xml as $itemsXml) {
            foreach ($itemsXml as $item) {
                if ($item->getName() == 'COMMODITY') {

                    $item = collect($item)->map(function ($volume) {
                        return $volume->__toString();
                    });

                    $items->push([
                        'title' => $item->get('SMODIFNAME'),
                        'num' => $item->get('NNOMMODIF'),
                        'code' => $item->get('SMNFCODE'),
                        'count' => $item->get('NREST'),
                        'price' => $item->get('NPRICE_RRP'),
                        'storage' => $item->get('TERRITORY_NAME'),
                        'label' => $this->label
                    ]);
                }
            }
        }

        return $items;
    }
}
