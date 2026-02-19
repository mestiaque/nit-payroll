<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeInfo extends Model
{
    protected $table = 'employee_info';

    protected $fillable = [
        'user_id', 'employee_id', 'card_no', 'nid', 'birth_certificate',
        'date_of_birth', 'gender', 'marital_status', 'blood_group', 'religion',
        'nationality', 'present_address', 'permanent_address',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'father_name', 'mother_name', 'spouse_name', 'photo', 'signature',
        'department_id', 'designation_id', 'division_id', 'section_id',
        'grade_id', 'shift_id', 'employee_type_id', 'line_number_id',
        'joining_date', 'confirmation_date', 'retirement_date', 'resign_date',
        'employee_status', 'service_confirmed',
        'basic_salary', 'house_rent', 'medical_allowance', 'transport_allowance', 'other_allowance',
        'remarks'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'confirmation_date' => 'date',
        'retirement_date' => 'date',
        'resign_date' => 'date',
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Attribute::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Attribute::class, 'designation_id');
    }

    public function division()
    {
        return $this->belongsTo(Attribute::class, 'division_id');
    }

    public function section()
    {
        return $this->belongsTo(Attribute::class, 'section_id');
    }

    public function grade()
    {
        return $this->belongsTo(Attribute::class, 'grade_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(Attribute::class, 'employee_type_id');
    }

    public function lineNumber()
    {
        return $this->belongsTo(Attribute::class, 'line_number_id');
    }

    public function education()
    {
        return $this->hasMany(EmployeeEducation::class, 'user_id', 'user_id');
    }

    public function training()
    {
        return $this->hasMany(EmployeeTraining::class, 'user_id', 'user_id');
    }

    public function experience()
    {
        return $this->hasMany(EmployeeExperience::class, 'user_id', 'user_id');
    }

    public function bankInfo()
    {
        return $this->hasMany(EmployeeBank::class, 'user_id', 'user_id');
    }

    public function totalSalary()
    {
        return $this->basic_salary + $this->house_rent + $this->medical_allowance +
               $this->transport_allowance + $this->other_allowance;
    }
}
