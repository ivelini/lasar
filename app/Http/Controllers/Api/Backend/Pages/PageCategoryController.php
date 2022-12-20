<?php

namespace App\Http\Controllers\Api\Backend\Pages;

use App\Helpers\DbHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Pages\PageCategoriesRepository;
use Illuminate\Http\Request;

class PageCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $categories = (new PageCategoriesRepository)->getAllForIndex('id', 'name');
        return ['categories' => $categories];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dbHelper = new DbHelper();
        $dbHelper->setModel('PageCategory');
        $dbHelper->addRelation('seo', ['label' => $request->input('label')]);
        $category = $dbHelper->save(['name' => $request->input('name')]);

        return ['value' => $category];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dbHelper = new DbHelper();
        $result = $dbHelper
            ->setModel('PageCategory')
            ->addRelation('seo')
            ->delete($id);

        return ['delete' => $result];
    }
}
