<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperarioFormRequest extends FormRequest{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){

        return [
            'nombreOperario' => 'required|max:255',
            'rutOperario' => 'required|max:255',
            'correoOperario' => 'required|email|max:255',
            'empresa' => 'required|max:255',
            'tipoOperario' => 'required|max:255'
        ];
    }
}
