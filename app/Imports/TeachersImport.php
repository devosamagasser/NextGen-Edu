<?php

namespace App\Imports;

use Exception;
use App\Models\User;
use App\Modules\Students\Student;
use App\Modules\Teachers\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Modules\Departments\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use App\Modules\Students\StudentsServices;
use App\Modules\Teachers\TeachersServices;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeachersImport implements ToModel,WithHeadingRow,PersistRelations,WithValidation
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

                $code = TeachersServices::generateCode();
                $email = $code . '@zu.edu.eg';

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $email,
                    'password' => Hash::make($email),
                    'type' => "Teacher"
                ])->assignRole('Teacher');

                Teacher::create([
                    'user_id' => $user->id,
                    'uni_code' => $code,
                    'department_id' => $department->id,
                    'description' => $row['description'] ?? null,
                ]);
            });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Department not found for row: " . $this->row);
        } catch (Exception $e) {
            throw new Exception("Failed to import teacher: ".$e->getMessage());
        }
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'department' => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function fail($key,$error,$row){
        $failures[] = new Failure($this->row,$key,$error,$row);
        Throw new ValidationException(\Illuminate\Validation\ValidationException::withMessages($error),$failures);
    }
}
