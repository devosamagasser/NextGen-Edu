<?php

namespace App\Modules\Table\Validation;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Table\Models\Session;

class TablePostponeSessionRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        // Check if the teacher is assigned to this session
        return $user->hasRole('Teacher') ;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'from' => 'required|date_format:H:i',
            'to' => 'required|date_format:H:i|after:from',
            'hall_id' => 'required|exists:halls,id',
            'attendance' => 'nullable|in:online,offline',
        ];
    }
} 