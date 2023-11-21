<?php


namespace App\Services\Shift;

use App\Interfaces\Shift\ShiftServiceInterface;
use App\Repository\Shift\ShiftRepository;

class ShiftSevice implements ShiftServiceInterface
{

    public function __construct(private ShiftRepository $shiftRepository)
    {
    }
    public function update_employee_shift($data)
    {
        return $this->shiftRepository->update_employee_shift($data);
    }
}
