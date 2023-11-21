<?php

namespace App\Repository\Holiday;


use App\Models\Holiday;

use App\Repository\BaseRepositoryImplementation;
use App\Statuses\HolidayTypes;
use App\Statuses\UserTypes;
use Illuminate\Support\Facades\DB;

class HolidayRepository extends BaseRepositoryImplementation
{
    public function getFilterItems($filter)
    {
        $records = Holiday::query()->where('company_id', auth()->user()->company_id);
        $records->when(isset($filter->orderBy), function ($records) use ($filter) {
            $records->orderBy($filter->getOrderBy(), $filter->getOrder());
        });
        return $records->paginate($filter->per_page);
    }
    public function create_weekly_holiday(array $data)
    {
        try {
            if (auth()->user()->type == UserTypes::ADMIN) {
                $holidays = [];
                $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $days = $data['days'];

                $existingHolidays = Holiday::whereIn('day', $days)
                    ->where('type', HolidayTypes::WEEKLY)
                    ->where('company_id', auth()->user()->company_id)
                    ->get();

                if ($existingHolidays->count() > 0) {
                    return ['success' => false, 'message' => "One or more holidays already exist,Please Choose Another Day"];
                }

                foreach ($days as $day) {
                    $day = (int) $day;
                    if ($day >= 1 && $day <= 7) {
                        $holiday = new Holiday();
                        $holiday->day = $day;
                        $holiday->day_name = $dayNames[$day - 1];
                        $holiday->type = HolidayTypes::WEEKLY;
                        $holiday->company_id = auth()->user()->company_id;
                        $holiday->save();
                        $holidays[] = $holiday;
                    }
                }

                return ['success' => true, 'data' => $holidays];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function create_annual_holiday($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::ADMIN) {

                $holiday = new Holiday();
                $holiday->holiday_name = $data['holiday_name'];
                $holiday->start_date = $data['start_date'];
                $holiday->end_date = $data['end_date'];
                $holiday->type = HolidayTypes::ANNUL;
                $holiday->company_id = auth()->user()->company_id;
                $holiday->save();
                DB::commit();
                if ($holiday === null) {
                    return ['success' => false, 'message' => "Holiday was not created"];
                }
                return ['success' => true, 'data' => $holiday];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function list_of_holidays($filter)
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $records = Holiday::query()->where('company_id', auth()->user()->company_id);
            return $records->get();
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function update_annual_holiday($data)
    {
        DB::beginTransaction();

        $holiday = $this->getById($data['holiday_id']);

        try {
            if (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $holiday->company_id) {

                $newHoliday = $this->updateById($data['holiday_id'], $data);

                DB::commit();
                if ($newHoliday === null) {
                    return ['success' => false, 'message' => "Holiday was not Updated"];
                }
                return ['success' => true, 'data' => $newHoliday];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function model()
    {
        return Holiday::class;
    }
}
