<?php
namespace App\Http\Controllers;

use App\Http\Requests\Login\Request;
use App\Models\User;
use App\Repository\MenuRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Login
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->validated();

        $user = User::where('name', $data['name'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            $user->append(['is_super_admin']);
            $accessToken = $user->createToken('web');
            $token = $accessToken->plainTextToken;
            return response()->success(compact('token'));
        }

        return response()->error('用户名或密码错误');
    }

    /**
     * 获取当前用户的菜单
     *
     * @return \Illuminate\Http\Response
     */
    public function currentUser(MenuRepository $menuRepository)
    {
        $menus          = $menuRepository->getMenus();
        $permissions    = $menuRepository->getUserPermissions();
        $user           = Auth::user();
        $user->append(['is_super_admin']);
        return response()->success(compact('menus', 'permissions', 'user'));
    }
}
