<?php

namespace App\Repository\Shift;

use App\Models\Shift;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\DB;

class ShiftRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = Shift::query();

        $records->when(isset($filter->orderBy), function ($records) use ($filter) {
            $records->orderBy($filter->getOrderBy(), $filter->getOrder());
        });

        return $records->paginate($filter->per_page);
    }
    public function update_employee_shift($data)
    {
        DB::beginTransaction();
        $employeeShift = $this->getById($data['shift_id']);
        try {
            if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR && auth()->user()->company_id == $employeeShift->company_id) {
                $newShift = $this->updateById($data['shift_id'], $data);
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
            DB::commit();
            return ['success' => true, 'data' => $newShift];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Specify Model class name.
     * @return mixed
     */
    public function model()
    {
        return Shift::class;
    }
}
