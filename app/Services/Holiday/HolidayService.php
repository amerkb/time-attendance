<?php

namespace App\Services\Holiday;

use App\Filter\Holiday\HolidayFilter;
use App\Interfaces\Holiday\HolidayServiceInterface;
use App\Models\Holiday;
use App\Repository\Holiday\HolidayRepository;
use App\Statuses\UserTypes;

class HolidayService implements HolidayServiceInterface
{

    public function __construct(private HolidayRepository $holidayRepository)
    {
    }


    public function create_weekly_holiday($data)
    {

        return $this->holidayRepository->create_weekly_holiday($data);
    }


    public function create_annual_holiday($data)
    {
        return $this->holidayRepository->create_annual_holiday($data);
    }
    public function update_annual_holiday($data)
    {
        return $this->holidayRepository->update_annual_holiday($data);
    }

    public function list_of_holidays(HolidayFilter $holidayFilter = null)
    {
        if ($holidayFilter != null)
            return $this->holidayRepository->list_of_holidays($holidayFilter);
        else
            return $this->holidayRepository->paginate();
    }

    public function destroy_holiday(int $id)
    {
        $holiday = Holiday::where('id', $id)->first();
        if ($holiday) {
            if (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $holiday->company_id) {
                Holiday::destroy($id);
                return response()->json(["message" => "Holiday Deleted..!"], 200);
            } else {
                return response()->json(["message" => "Unauthorized"], 401);
            }
        } else {
            return response()->json(["message" => "Holiday Not Found"], 404);
        }
    }
}
