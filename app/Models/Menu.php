<?php

namespace App\Models;

use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Menu
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $role
 * @property-read int|null $role_count
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu withoutTrashed()
 * @mixin \Eloquent
 */
class Menu extends Model
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
    protected $fillable = ['name', 'url', 'sort', 'permission', 'status', 'type', 'parent_id', 'icon'];

    /**
     * 子菜单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->with(['children' => fn($q) => $q->orderBy('sort', 'asc')->orderBy('id', 'asc')]);
    }

    /**
     * 角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_menus');
    }
}
