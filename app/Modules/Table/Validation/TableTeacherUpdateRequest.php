<?php

namespace App\Modules\Table\Validation;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Table\Models\Session;

class TableTeacherUpdateRequest extends AbstractApiRequest
{
    public function authorize(): bool
    {
        $sessionId = request()->route('session_id');
        $user = auth()->user();
        if ($user->hasRole(['Super admin', 'Admin'])) {
            return true;
        }
        $session = Session::find($sessionId);
        if (!$session) return false;
        // Check if the teacher is assigned to this session
        return $user->hasRole('Teacher') && $session->course && $session->course->teachers->contains('user_id', $user->id);
    }

    public function rules(): array
    {
        return [
            'date' => 'nullable|date',
            'day' => 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'from' => 'nullable|date_format:H:i',
            'to' => 'nullable|date_format:H:i|after:from',
            'hall_id' => ['nullable','exists:halls,id', new EmptyHallRule()],
            'attendance' => 'nullable|in:online,offline',
        ];
    }
} 