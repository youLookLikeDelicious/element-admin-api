<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\RoleRequest;
use App\Http\Resources\CommonCollection;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::select('id', 'name', 'remark');

        if ($name = $request->input('name')) {
            $query->where('name', 'like', "%$name%");
        }

        $data = $query->paginate($request->input('per_page', 10));

        return CommonCollection::make($data);
    }

    /**
     * 获取角色详情
     *
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load('menu:id,name');

        return Response::success($role);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            $role = Role::create($data);

            $role->menu()->sync($data['menu']);
        });

        return response('');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $role) {
            $role->name = $data['name'];
            $role->remark = $data['remark'] ?? '';
            $role->save();

            $role->menu()->sync($data['menu']);
        });

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response('');
    }

    /**
     * 获取全部角色
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $data = Role::get();

        return response(compact('data'));
    }
}
