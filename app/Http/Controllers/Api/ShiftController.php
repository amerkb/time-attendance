<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\UpdateEmployeeShift;
use App\Http\Resources\Shifts\ShiftRersource;
use App\Services\Shift\ShiftSevice;
use Illuminate\Http\Request;

/**
 * @group Shifts
 * @authenticated
 * APIs for managing Shifts
 */
class ShiftController extends Controller
{
    public function __construct(private ShiftSevice $shiftSevice)
    {
    }

    /**
     * Update Employee Shift
     *
     * This endpoint is used to Create Update Employee Shifts and Admin Or Hr Can Access To This Api.
     * @bodyParam shift_id int required Must Be Exists In shifts Table
     * @bodyParam start_time time required The start time of the shift in the format `HH:MM:SS` Custom Example: 09:00:00.
     * @bodyParam end_time time required The end time of the shift in the format `HH:MM:SS` Custom Example: 15:00:00.
     * @bodyParam start_break_hour time required The start break hour of the shift in the format `HH:MM:SS` Custom Example: 12:30:00.
     * @bodyParam end_break_hour time required The end break hour of the shift in the format `HH:MM:SS` Custom Example: 13:30:00.
     *
     * @response 200 scenario="Update Employee Shift"{
     *"data": {
     *"id": 1,
     *"start_time": "17:30:00",
     *"end_time": "23:30:00",
     *"start_break_hour": "21:00:00",
     *"end_break_hour": "21:30:00"
     *}
     *}
     */
    public function update_employee_shift(UpdateEmployeeShift $request)
    {
        $createdData =  $this->shiftSevice->update_employee_shift($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ShiftRersource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
}
