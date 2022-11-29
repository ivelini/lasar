<?php


namespace App\Services\ImportCatalogService;

use App\Helpers\DbHelper;
use App\Repositories\Catalog\PricesRepository;
use Illuminate\Support\Facades\Storage;
use Orchestra\Parser\Xml\Facade as XmlParser;

/*
 * Шаблон получения данных из XML файла
 */
abstract class ImportXml extends RootImportCatalog
{
    private $items;
    //Метка в таблице prices с названием класса чтоб знать, какие данные удалить из таблицы при обновлении
    protected $label;

    public function __construct($path)
    {
        parent::__construct($path);
        $xml = XmlParser::local(Storage::disk('local')->path($path))->getContent();

        $this->label = (new \ReflectionClass($this))->getShortName();
        $this->items = $this->getItemsFromXml($xml);

        $this->iStart = 0;
        $this->rowAll = $this->items->count() - 1;
    }

    /*
     * На выходе коллекция массивов со следующими ключами: num (номер в учетной системе), code (код производителя), count, price, storage, label"
     */
    abstract function getItemsFromXml($xml);

    protected function deletePriceItemsByLabel()
    {
        (new PricesRepository)
            ->getModelsByAttributesBuilder(['label' => $this->label])
            ->delete();

        return true;
    }

    protected function getRow($i, $targetKeys, $renameKeys = [], $notNulls = [])
    {
        $item = $this->items[$i];
        $row = collect();
        foreach($targetKeys as $key) {
            $row->put($key, $item[$key]);

        }

        $row = $this->renameKeysAndCheckNotNull($row, $renameKeys, $notNulls);

        return $row;
    }

    protected function importSallersTable(...$targetKeys)
    {
        logger('Метод importSallersTable в слассе ImportXML не поддерживается');
        return true;
    }

    protected function getRowForCode($i, $targetKeys)
    {
        logger('Метод getRowForCode в слассе ImportXML не поддерживается');
        return true;
    }
}
