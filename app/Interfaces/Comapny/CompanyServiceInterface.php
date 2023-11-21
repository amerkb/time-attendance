<?php

namespace App\Interfaces\Comapny;


interface CompanyServiceInterface
{
    public function create_company($data);
    public function show($id);
}
