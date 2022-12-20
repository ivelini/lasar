<?php

namespace App\Http\Controllers\Api\Backend\Catalog\Pages;

use App\Helpers\DbHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Catalog\StorageRepository;
use Illuminate\Http\Request;

class PageStorageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = (new StorageRepository)->getIndex();
        return ['storages' => $items];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->input();

        (new DbHelper)
            ->setModel('Storage')
            ->updateTable($data);

        return ['status' => 'OK'];
    }

}
