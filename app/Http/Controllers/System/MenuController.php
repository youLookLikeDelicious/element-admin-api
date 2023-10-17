<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Menu\BatchCreateRequest;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Menu::where('parent_id', 0)->orderBy('sort', 'desc')->with(['children' => fn($q) => $q->orderBy('sort', 'asc')])->get();
        
        return response()->success($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BatchCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BatchCreateRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            foreach ($data['menus'] as $menu) {
                Menu::create($menu);
            }
        });

        return response('');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $data = $request->all();
        $data['url'] = $data['url'] ?? '';
        $data['permission'] = $data['permission'] ?? '';
        $data['parent_id']  = $data['parent_id'] ?? 0;

        $menu->update($data);

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response('');
    }
}
