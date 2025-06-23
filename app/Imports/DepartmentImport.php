<?php

namespace App\Imports;

use App\Modules\Departments\Department;
use Exception;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;

class DepartmentImport implements ToModel, WithHeadingRow, PersistRelations, WithValidation
{
    private $row = 0;

    public function model(array $row)
    {
        $this->row++;
        try {
            return Department::create([
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to import department in row {$this->row}: " . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ];
    }

    public function fail($key, $error, $row)
    {
        $failures = [
            new Failure($this->row, $key, [$error], $row)
        ];

        throw new ValidationException(
            \Illuminate\Validation\ValidationException::withMessages([$key => [$error]]),
            $failures
        );
    }
}
