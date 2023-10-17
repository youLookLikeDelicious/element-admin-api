<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

trait SortByFields
{
    public $SORT_ORDER_MAP = [
        'ascending' => 'asc',
        'descending' => 'desc'
    ];

    public function scopeSortByField(Builder $query, Request $request)
    {
        $request->validate([
            'sort' => 'sometimes',
            'sort.*.prop' => 'required',
            'sort.*.order' => 'required|in:ascending,descending'
        ]);

        $sortField = array_filter($request->input('sort', []), fn($item) => !empty($item['order']));

        if (!$sortField) {
            return;
        }

        foreach ($sortField as $field) {
            $order = $this->SORT_ORDER_MAP[$field['order']] ?? null;
            if (empty($order)) {
                throw new BadRequestException('未知的排序顺寻');
            }

            $query->orderBy($field['prop'], $order);
        }
    }
}