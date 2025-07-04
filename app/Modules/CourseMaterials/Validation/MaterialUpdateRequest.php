<?php

namespace App\Modules\CourseMaterials\Validation;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Teachers\Rules\TeacherCourseDetailsRule;

class MaterialUpdateRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array 
    {
        return [
            'title'     => ['required', 'string', 'max:255'], 
            'material' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,png,jfif'],
            'week'      => ['required', 'integer', 'in:1,2,3,4,5,6,7,8,9,10,11,12,13,14'],
            'type'      => ['required', 'string', 'in:lecture,section,other'],
        ];
    }
}
