<?php
namespace App\Http\Requests\System\Menu;
use Illuminate\Foundation\Http\FormRequest;

class BatchCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'menus'                 => 'required',
            'menus.*.name'          => 'required',
            'menus.*.sort'          => 'sometimes|numeric',
            'menus.*.status'        => 'required',
            'menus.*.type'          => 'required',
            'menus.*.url'           => 'sometimes',
            'menus.*.permission'    => 'sometimes',
            'menus.*.parent_id'     => 'sometimes',
            'menus.*.icon'          => 'sometimes',
        ];
    }
}