<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\UserRequest;
use App\Http\Resources\CommonCollection;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    /**
     * 构建查询请求
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQuery(Request $request)
    {
        $query = User::where('id', '>', 1);

        // 姓名|手机|微信
        if ($name = $request->input('name')) {
            $query->where(fn($q) => $q->where('name', 'like', "%$name%")->orWhere('wechat', 'like', "%$name%")->orWhere('phone', 'like', "%$name%"));
        }
        
        // 部门
        // $query->departmentPermission($request);

        // 角色
        if ($roleId = $request->input('role_id')) {
            $query->whereHas('roles', fn($q) => $q->where('role_id', $roleId));
        }

        return $query;
    }

    /**
     * 获取全部的用户
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $query = $this->buildQuery($request);

        // 加载微信
        if ($request->input('with_wechat')) {
            $query->with('weixin:id,no,user_id');
        }

        $query->select('id', 'real_name', 'name')->with('departments:id,name,id_path')->where('job_status', 0);

        $data = $query->get();

        return response()->success($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \App\Http\Resources\CommonCollection
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $query->with(['roles:id,name', 'departments:id,name']);

        $data = $query->paginate($request->input('per_page', 10));

        return new CommonCollection($data);
    }

    /**
     * 按照部门分组,获取用户信息
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function groupedByDepartment(Request $request)
    {
        $query = User::select('users.id', 'real_name', 'department_id');
        $query->leftJoin('department_users', 'department_users.user_id', 'users.id');

        // 父级部门
        $departmentIds = [];
        if ($departmentId = $request->input('department_id')) {
            $department = Department::select('id', 'id_path')->findOrFail($departmentId);
            $departmentIds = Department::select('id')->where('id_path', 'like', $department->id_path.'%')->get()->map->id->toArray();
            $query->whereIn('department_id', $departmentIds);
        }

        $users = $query->get();

        // 获取部门
        if (!$departmentIds) {
            $departmentIds = $users->map->id->toArray();
        }
        $departments = Department::select('id', 'name')->whereIn('id', $departmentIds)->get();

        $data = $departments->each(fn($department) => $department->childrens = $users->where('department_id', $department->id)->values());

        return Response::success($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['password'] = Hash::make('123456');
            $user = User::create($data);

            $user->roles()->sync($data['roles'] ?? []);
            $user->departments()->sync($data['departments'] ?? []);
        });

        return response('');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load([
            'departments',
            'roles:id,name',
        ]);
        $user->append(['gender_name', 'role_names']);

        $user->bankCard->makeHidden('amount');
        $user->weixin->makeHidden('amount');

        return Response::success($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $data = $request->validated();

            $user->update($request->validated());

            // 如果修改为已离职, 删除登录凭证
            if ($user->wasChanged('job_status') && $user->job_status == 1) {
                $user->tokens()->delete();
            }
            $user->roles()->sync($data['roles'] ?? []);
            $user->departments()->sync($data['departments'] ?? []);
        });

        return response('');
    }

    /**
     * 用户修改自己的基本信息
     *
     * @param Request $request
     * @return @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $user = Auth::user();

        $user->avatar           = $request->input('avatar');
        $user->gender           = $request->input('gender');
        $user->save();

        return Response::success();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return Response::success();
    }

    /**
     * 修改密码
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->input('old_pwd'), $user->password)) {
            return response(['msg' => '原始密码错误'], 422);
        }

        $user->password = Hash::make($request->input('pwd'));
        $user->save();

        return Response::success();
    }

    /**
     * 重置密码
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(User $user)
    {
        $user->password = Hash::make('123456');

        $user->save();

        return Response::success();
    }
}
