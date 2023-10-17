<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45)->default('')->index()->comment('菜单名称');
            $table->string('url')->default('')->comment('前端路由');
            $table->unsignedInteger('sort')->default(50)->comment('排序');
            $table->string('permission')->default('')->comment('权限');
            $table->unsignedTinyInteger('type')->default(1)->comment('菜单类型 1 菜单 2 按钮');
            $table->unsignedBigInteger('parent_id')->default(0)->index()->comment('父级权限ID');
            $table->string('icon')->nullable()->default('')->comment('图标');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态 1 启用 2 禁用');
            $table->unsignedInteger('created_at')->default(0)->comment('创建时间');
            $table->unsignedInteger('updated_at')->default(0)->comment('修改时间');
            $table->unsignedInteger('deleted_at')->nullable()->default(NULL)->comment('删除时间');
            $table->comment('菜单权限');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->comment('角色名称');
            $table->string('remark')->default('')->nullable()->comment('备注');
            $table->unsignedInteger('created_at')->default(0)->comment('创建时间');
            $table->unsignedInteger('updated_at')->default(0)->comment('修改时间');
            $table->unsignedInteger('deleted_at')->nullable()->default(NULL)->comment('删除时间');
            $table->comment('角色');
        });

        Schema::create('role_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id')->default(0)->index()->comment('菜单ID');
            $table->unsignedBigInteger('role_id')->default(0)->index()->comment('角色ID');
            $table->comment('角色权限');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_menus');
    }
};
