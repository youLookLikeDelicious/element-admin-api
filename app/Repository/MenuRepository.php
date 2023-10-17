<?php
namespace App\Repository;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuRepository
{
    public function __construct(protected Menu $menu)
    {
    }

    /**
     * 获取所有的权限
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions()
    {
        return $this->menu->select('permission')->where('permission', '<>', '')->pluck('permission');
    }

    /**
     * 获取用户菜单,用于登录
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMenus()
    {
        $query = $this->menu->where('type', 1)->orderBy('sort', 'desc')->where('status', 1)->orderBy('id', 'asc');

        if (!Auth::user()->is_super_admin) {
            $query->whereHas('role', fn($q) => $q->whereIn('role_id', Auth::user()->role->pluck('id')->toArray()));
        }

        $originMenus = $query->get();

        $menus = convertChildren($originMenus);

        return $menus;
    }

    /**
     * 获取当前用户的所有权限
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPermissions()
    {
        $data = $this->menu
            ->where('permission', '<>', '')
            ->whereHas('role', fn($q) => $q->whereIn('role_id', Auth::user()->role->pluck('id')->toArray()))
            ->pluck('permission');

        return $data;
    }
}