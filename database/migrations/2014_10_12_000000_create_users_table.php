<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone', 21)->default('')->index()->comment('手机号');
            $table->string('email')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态 1 正常 2 锁定');
            $table->rememberToken();
            $table->dateTime('created_at')->nullable()->default(null)->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->default(null)->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
            $table->comment('用户');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
