<?php

namespace App\Imports;

use Exception;
use App\Models\User;
use App\Modules\Students\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Modules\Departments\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use App\Modules\Students\StudentsServices;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentsImport implements ToModel,WithHeadingRow,PersistRelations,WithValidation
{

    private $row = 0;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->row++;

        try {
            $code = StudentsServices::generateCode();
            $email = $code . '@zu.edu.eg';

            // 1. حفظ البيانات داخل transaction
            $student = DB::transaction(function () use ($row, $code, $email) {
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $email,
                    'password' => Hash::make($email),
                    'type' => 'Student'
                ]);
                $user->assignRole('Student');

                return Student::create([
                    'user_id' => $user->id,
                    'nationality' => $row['nationality'],
                    'uni_code' => $code,
                    'personal_id' => $row['id'],
                    'department_id' => $row['department'],
                    'semester_id' => $row['semester'],
                    'group' => $row['group'] ?? StudentsServices::generateGroupe($row['department'], $row['semester']),
                ]);
            });

            Http::withHeaders([
                "Content-Type" => "application/json",
                'Authorization' => 'Bearer 765|LcXERXtUwbmVHkOQ2ntDvzPhxz8LjMmVWOMPbUWZc0a149dc',
            ])->post('https://ngu-question-hub.azurewebsites.net/chat/add', [
                'userCode' => $student->uni_code,
            ]);

            return $student;

        } catch (Exception $e) {
            throw new Exception("Row {$this->row}: Failed to import student – " . $e->getMessage());
        }
    }


    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'nationality' => 'string|in:National,International',
            'id' => 'integer|unique:students,personal_id',
            'department' => 'integer|exists:departments,id',
            'semester' => 'integer',
            'group' => 'nullable|integer|max:30',
        ];
    }

    public function fail($key,$error,$row){
        $failures[] = new Failure($this->row,$key,$error,$row);
        Throw new ValidationException(\Illuminate\Validation\ValidationException::withMessages($error),$failures);
    }
}
