<?php

namespace App\Interfaces\Posts;

use App\Http\Requests\Employees\CreateEmployeeRequest;

interface PostServiceInterface
{
    public function create_post($data);
}
