<?php


namespace App\Repositories\Catalog;

use App\Models\Price as Model;
use App\Repositories\CoreRepository;


class PricesRepository extends CoreRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getModelClass()
    {
        return Model::class;
    }

    public function getPaginationByFilterData($data, $paginate = 20)
    {
        $data = [
//            'price'     => ['min' => 0, 'max' => 3200],
            'vendorId'  => 4,
//            'modelId'   => 727,
//            'seasonId'  => 1,
            'width'     => 185,
            'height'    => 55,
            'diameter'  => 16,
//            'isSpikes'  => 1,
        ];

        $paginator = $this->startConditions();

        if (array_key_exists('price.min', $data)) $paginator = $paginator->where('price', '>=', $data['price.min']);
        if (array_key_exists('price.max', $data)) $paginator = $paginator->where('price', '<=', $data['price.max']);

        $paginator = $paginator->whereHasMorph(
                'priceable',
                ['vendor_code', 'saller_code'],
                function ($query) use ($data) {
                    $query
                        ->wherehas(
                            'tire',
                            function ($query) use ($data)
                            {
                                if (array_key_exists('modelId', $data)) $query->where('model_id', $data['modelId']);
                                if (array_key_exists('seasonId', $data)) $query->where('season_id', $data['seasonId']);
                                if (array_key_exists('width', $data)) $query->where('width', $data['width']);
                                if (array_key_exists('height', $data)) $query->where('height', $data['height']);
                                if (array_key_exists('diameter', $data)) $query->where('diameter', $data['diameter']);
                                if (array_key_exists('isSpikes', $data)) $query->where('is_spikes', $data['isSpikes']);

                                $query->whereHas(
                                    'modelPosition',
                                    function ($query) use ($data)
                                    {
                                        if (array_key_exists('vendorId', $data)) $query->where('vendor_id', $data['vendorId']);
                                    }
                                );
                            });
                }
            )
            ->with('priceable.tire.modelPosition.vendor', 'priceable.saller', 'storage')
            ->get()
            ->unique(function ($item) {
                return $item->priceable->tire->id;
            });

        if ($paginator->count() > 0) {

            $paginatorChunk = $paginator->chunk($paginate);

            $currentPage = (!empty($_GET['page']) && $_GET['page'] < $paginatorChunk->count()) ? $_GET['page'] : 1;

            $customPaginator = new \Illuminate\Pagination\LengthAwarePaginator($paginatorChunk[$currentPage - 1], $paginator->count(), $paginate);

            $tires = collect($customPaginator->items())->map(function ($price) {
                $data['id'] = $price->priceable->tire->id;

                $data['title'] = 'Шина ' .
                    $price->priceable->tire->modelPosition->name . ' ' .
                    $price->priceable->tire->width . '/' .
                    $price->priceable->tire->height . ' R' .
                    $price->priceable->tire->diameter;
                if(!empty($price->priceable->tire->index_speed)) $data['title'] .= ' ' . $price->priceable->tire->index_speed;

                $data['num'] = $price->priceable->tire->num;
                $data['price'] = number_format(rtrim(rtrim($price->price, '0'), '.'), 0, '', ' ');
                $data['storage'] = $price->storage->name;
                $data['storage_vol'] = $price->storage->value;
                $data['count'] = $price->count;
                $data['vendor'] = $price->priceable->tire->modelPosition->vendor->name;
                $data['model'] = $price->priceable->tire->modelPosition->name;
                $data['isSpikes'] = $price->priceable->tire->is_spikes;

                return $data;
            });

            $paginatePage['paginator']['title'] = 'Шины';

            if (array_key_exists('seasonId', $data)) {
                $paginatePage['paginator']['title'] .= ' ' . (new SeasonRepository())->getAttributeById('name', $data['seasonId']);
            }

            if (array_key_exists('isSpikes', $data)) {
                $paginatePage['paginator']['title'] .= ($data['isSpikes']) ? ' шипованные' :  ' нешипованные';
            }

            if (array_key_exists('vendorId', $data)) {
                $paginatePage['paginator']['title'] .= ' ' . (new VendorRepository)->getAttributeById('name', $data['vendorId']);
            }

            if (array_key_exists('width', $data)) {
                $paginatePage['paginator']['title'] .= ' ' . $data['width'];
            }

            if (array_key_exists('height', $data)) {
                $paginatePage['paginator']['title'] .= (array_key_exists('width', $data)) ? '/' . $data['height'] : ' ' . $data['height'];
            }

            if (array_key_exists('diameter', $data)) {
                $paginatePage['paginator']['title'] .= ' R' . $data['diameter'];
            }



            $paginatePage['items'] = $tires->all();
            $paginatePage['paginator']['current_page'] = $customPaginator->currentPage();
            $paginatePage['paginator']['last_page'] = $customPaginator->lastPage();
            $paginatePage['paginator']['total_items'] = $paginator->count();

        } else {
            $paginatePage['items'] = 0;
            $paginatePage['paginator']['current_page'] = 1;
            $paginatePage['paginator']['last_page'] = 1;
            $paginatePage['paginator']['total_items'] = 0;
        }

        return $paginatePage;
    }

    public function getMinAndMaxPrice()
    {
        $models = $this->startConditions()->pluck('price');

        return [
            'min' => number_format(rtrim(rtrim($models->min(), '0'), '.'), 0, '', ' '),
            'max' => number_format(rtrim(rtrim($models->max(), '0'), '.'), 0, '', ' ')
        ];
    }
}
