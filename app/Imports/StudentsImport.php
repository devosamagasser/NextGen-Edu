<?php

namespace App\Imports;

use App\Models\User;
use App\Modules\Departments\Department;
use App\Modules\Students\Student;
use App\Modules\Students\StudentsServices;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

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
            return DB::transaction(function () use ($row) {
                $department = Department::where('name', 'like', "%{$row['department']}%")->firstOrFail();

                $code = StudentsServices::generateCode();
                $email = $code . '@zu.edu.eg';

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $email,
                    'password' => Hash::make($email),
                    'type' => "Student"
                ])->assignRole('Student');

                Student::create([
                    'user_id' => $user->id,
                    'nationality' => $row['nationality'],
                    'uni_code' => $code,
                    'personal_id' => $row['id'],
                    'department_id' => $department->id,
                    'semester_id' => $row['semester'],
                    'group' => $row['group'] ?? StudentsServices::generateGroupe( $department->id, $row['semester']),
                ]);
            });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Department or Semester not found for row: " . $this->row);
        } catch (Exception $e) {
            throw new Exception("Failed to import student: ".$e->getMessage());
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
            'department' => 'string',
            'semester' => 'integer',
            'group' => 'nullable|integer|max:30',
        ];
    }

    public function fail($key,$error,$row){
        $failures[] = new Failure($this->row,$key,$error,$row);
        Throw new ValidationException(\Illuminate\Validation\ValidationException::withMessages($error),$failures);
    }
}
