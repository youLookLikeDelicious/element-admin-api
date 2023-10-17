<?php

namespace App\Models;

use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Role
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Role withoutTrashed()
 * @property int $id
 * @property string $name 角色名称
 * @property string|null $remark 备注
 * @property \Illuminate\Support\Carbon $created_at 创建时间
 * @property \Illuminate\Support\Carbon $updated_at 修改时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menu
 * @mixin \Eloquent
 */
class Role extends Model
{
    use HasFactory, SerializeDate, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'remark'];

    /**
     * 菜单权限
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menu(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menus');
    }
}
