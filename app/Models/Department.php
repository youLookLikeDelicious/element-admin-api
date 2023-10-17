<?php

namespace App\Models;

use App\Models\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Department
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Department> $children
 * @property-read Department|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @method static Builder|Department newModelQuery()
 * @method static Builder|Department newQuery()
 * @method static Builder|Department onlyTrashed()
 * @method static Builder|Department permission()
 * @method static Builder|Department query()
 * @method static Builder|Department withTrashed()
 * @method static Builder|Department withoutTrashed()
 * @mixin \Eloquent
 */
class Department extends Model
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
    protected $fillable = ['name', 'description', 'operation_type', 'structure_type', 'charge_departments', 'parent_id', 'split_saler'];

    /**
     * 递归获取子级部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')->with('children');
    }

    /**
     * 定义父级部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
    
    /**
     * 用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsToMany(User::class, 'department_users');
    }

    /**
     * 数据权限
     *
     * @param Builder $query
     * @return void
     */
    public function scopePermission(Builder $query)
    {        
        if (!auth()->user()->is_super_admin) {
            $query->whereHas('user', fn($q) => $q->where('user_id', auth()->id()));
        }
    }
}
