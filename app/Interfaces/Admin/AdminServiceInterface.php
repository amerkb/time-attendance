<?php

namespace App\Interfaces\Admin;

use App\Http\Requests\Employees\CreateEmployeeRequest;

interface AdminServiceInterface
{
    public function create_employee($data);
}
