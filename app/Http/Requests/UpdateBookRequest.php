<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'=>'required',
            'content'=>'required',
            'description'=>'required',
            'download_link'=>'required',
            'category_id'=>'required',
            'category_id' => 'exists:categories,id',
            'status_id' => 'exists:statuses,id',
            'location'=> 'required|min:6',
        ];
    }
}
