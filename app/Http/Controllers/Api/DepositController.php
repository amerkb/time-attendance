<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Deposit\CreateDepositRequest;
use App\Http\Requests\Deposit\GetDepositsList;
use App\Http\Requests\Deposit\RejectClearanceRequest;
use App\Http\Requests\Deposit\RejectDepositRequest;
use App\Http\Resources\Deposit\DepositResource;
use App\Services\Deposit\DepositService;

/**
 * @group Deposits
 *
 * @authenticated
 *
 * APIs for managing Deposits
 */
class DepositController extends Controller
{

    public function __construct(private DepositService $depositService)
    {
    }

    /**
     * Add Deposit
     *
     * This endpoint is used to add a deposit for an employee. Only admins or HR personnel can access this API.
     *
     * @bodyParam user_id int required The ID of the user. Must exist in the users table.
     *
     * @bodyParam type int required The deposit type. Must be one of the following values:
     * - `1`: CAR.
     * - `2`: LAPTOP.
     * - `3`: MOBILE.
     * Custom Example: 2.
     *
     * @bodyParam car_number number The car number. This field is required if the deposit type is 1 (car).
     * Custom Example: 01021.
     *
     * @bodyParam car_model string The car model. This field is required if the deposit type is 1 (car).
     * Custom Example: bmw.
     *
     * @bodyParam manufacturing_year number The manufacturing year of the car. This field is required if the deposit type is 1 (car).
     * Custom Example: 2010.
     *
     * @bodyParam Mechanic_card_number number The mechanic card number of the car. This field is required if the deposit type is 1 (car).
     * Custom Example: 200054.
     *
     * @bodyParam car_image file The car image. This field is required if the deposit type is 1 (car). Must not be greater than 5120 kilobytes.
     *
     * @bodyParam laptop_type string The laptop type. This field is required if the deposit type is 2 (laptop).
     * Custom Example: Asus.
     *
     * @bodyParam serial_laptop_number number The serial number of the laptop. This field is required if the deposit type is 2 (laptop).
     * Custom Example: 001100.
     *
     * @bodyParam laptop_color string The color of the laptop. This field is required if the deposit type is 2 (laptop).
     * Custom Example: red.
     *
     * @bodyParam laptop_image file The laptop image. This field is required if the deposit type is 2 (laptop). Must not be greater than 5120 kilobytes.
     *
     * @bodyParam serial_mobile_number number The serial number of the mobile. This field is required if the deposit type is 3 (mobile).
     * Custom Example: 01021.
     *
     * @bodyParam mobile_color string The color of the mobile. This field is required if the deposit type is 3 (mobile).
     * Custom Example: blue.
     *
     * @bodyParam mobile_type string The type of the mobile. This field is required if the deposit type is 3 (mobile).
     * Custom Example: samsung.
     *
     * @bodyParam mobile_sim number The SIM number of the mobile. This field is required if the deposit type is 3 (mobile).
     * Custom Example: 56252.
     *
     * @bodyParam mobile_image file The mobile image. This field is required if the deposit type is 3 (mobile). Must not be greater than 5120 kilobytes.
     *
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 3,
     *         "extra_status": null,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": null,
     *         "reason_clearance_reject": null,
     *         "deposit_request_date": "2023-08-28",
     *         "clearance_request_date": null,
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.http://127.0.0http://127.0"
     *             "position": null
     *         }
     *     }
     * }
     */

    public function store(CreateDepositRequest $request)
    {
        $createdData =  $this->depositService->create_deposit($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Approve Deposit
     *
     * This endpoint is used to approve a deposit. Employees can access this API to accept their deposits. The status will be set to 1, where:
     * - `1`: approved.
     * - `2`: rejected.
     * - `3`: pending status.
     *
     * @urlParam id int required The ID of the deposit. Must exist in the deposits table.
     *
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 1,
     *         "extra_status": null,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": null,
     *         "reason_clearance_reject": null,
     *         "deposit_request_date": "2023-08-28",
     *         "clearance_request_date": null,
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "position": null
     *         }
     *     }
     * }
     */
    public function approve_deposit($id)
    {
        $createdData = $this->depositService->approve_deposit($id);
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Reject Deposit
     *
     * This endpoint is used to reject a deposit. Employees can access this API to rejecte their deposits. The status will be set to 1, where:
     * - `1`: approved.
     * - `2`: rejected.
     * - `3`: pending status.
     *
     * @bodyParam deposit_id int required Must Be Exists In deposits Table
     * @bodyParam reason_reject string required The reject reason for the deposit Custom Example: Without Any Reason.
     *
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 2,
     *         "extra_status": null,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": "Without Any Reason",
     *         "reason_clearance_reject": null,
     *         "deposit_request_date": "2023-08-28",
     *         "clearance_request_date": null,
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "position": null
     *         }
     *     }
     * }
     */
    public function reject_deposit(RejectDepositRequest $request)
    {
        $createdData = $this->depositService->reject_deposit($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Clearance Request
     *
     * This endpoint is used to Clearance Request. Employees can access this API. The extra status will be set to 4, where:
     * - `4`: up paid.
     * - `5`: paid.
     * - `6`: un paid rejected status Custom Example:4.
     *
     * @urlParam id int required The ID of the deposit. Must exist in the deposits table.
     *
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 1,
     *         "extra_status": 4,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": null,
     *         "reason_clearance_reject": null,
     *         "deposit_request_date": "2023-08-28",
     *         "clearance_request_date": "2023-08-30",
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "position": null
     *         }
     *     }
     * }
     */
    public function clearance_request($id)
    {
        $createdData = $this->depositService->clearance_request($id);
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Approve Clearance Request
     *
     * This endpoint is used to Approve Clearance Request. Admin Or Hr can access this API to accept Clearance Request Deposits. The extra status will be set to 5, where:
     * - `4`: up paid.
     * - `5`: paid.
     * - `6`: un paid rejected status Custom Example:4.
     *
     * @urlParam id int required The ID of the deposit. Must exist in the deposits table.
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 1,
     *         "extra_status": 5,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": null,
     *         "reason_clearance_reject": null,
     *         "deposit_request_date": "2023-08-28",
     *         "clearance_request_date": "2023-08-30",
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "position": null
     *         }
     *     }
     * }
     */
    public function approve_clearance_request($id)
    {
        $createdData = $this->depositService->approve_clearance_request($id);
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Reject Clearance Request
     *
     * This endpoint is used to Reject Clearance Request. Admin Or Hr can access this API to Reject Clearance Request Deposits. The extra status will be set to 6, where:
     * - `4`: up paid.
     * - `5`: paid.
     * - `6`: un paid rejected status Custom Example:4.
     *
     * @urlParam id int required The ID of the deposit. Must exist in the deposits table.
     *
     * @bodyParam deposit_id int required Must Be Exists In deposits Table
     *
     * @bodyParam reason_reject string required The reject reason for the deposit Custom Example: Reject Clearance Request Without Any Reason.
     *
     * @response 200 {
     *     "data": {
     *         "id": 6,
     *         "type": 3,
     *         "status": 1,
     *         "extra_status": 6,
     *         "serial_mobile_number": 125815,
     *         "mobile_color": "red",
     *         "mobile_type": "samsung",
     *         "mobile_sim": 5422,
     *         "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *         "reason_reject": null,
     *         "reason_clearance_reject": "Reject Clearance Request Without Any Reason",
     *         "user": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "position": null
     *         }
     *     }
     * }
     */
    public function reject_clearance_request(RejectClearanceRequest $request)
    {
        $createdData = $this->depositService->reject_clearance_request($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = DepositResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * My Deposits
     *
     * This endpoint displays the deposits specific to the authenticated employee. Only deposits with a status of 3 (pending) will be show.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 6,
     *             "type": 3,
     *             "status": 3,
     *             "extra_status": null,
     *             "serial_mobile_number": 125815,
     *             "mobile_color": "red",
     *             "mobile_type": "samsung",
     *             "mobile_sim": 5422,
     *             "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *             "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-08-28",
     *             "clearance_request_date": "2023-08-30",
     *         }
     *     ]
     * }
     */
    public function my_deposits(GetDepositsList $request)
    {
        $data = $this->depositService->my_deposits($request->generateFilter());
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = DepositResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData,  "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
    /**
     * List of Deposits
     *
     * This endpoint is used to retrieve a list of deposits that can be accessed by the company, admins, and HR.
     *
     * @queryParam status integer optional Filter the deposits by status. Possible values: 1 (approved), 2 (rejected), 3 (pending) Custom Example: 2.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 6,
     *             "type": 3,
     *             "status": 3,
     *             "extra_status": null,
     *             "serial_mobile_number": 125815,
     *             "mobile_color": "red",
     *             "mobile_type": "samsung",
     *             "mobile_sim": 5422,
     *             "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *             "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-08-28",
     *             "clearance_request_date": "2023-08-30",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",

     *                 "position": null
     *             }
     *         },
     *         {
     *             "id": 7,
     *             "type": 1,
     *             "status": 2,
     *             "extra_status": null,
     *             "car_number": "0101",
     *             "car_model": "bmw3",
     *             "manufacturing_year": "2023",
     *             "mechanic_card_number": "2348",
     *             "car_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-3face.jpeg",
     *             "reason_reject": "No Reason ",
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-08-28",
     *             "clearance_request_date": "2023-08-30",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */
    public function list_of_deposits(GetDepositsList $request)
    {
        $data = $this->depositService->list_of_deposits($request->generateFilter());
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = DepositResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData,  "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
    /**
     * List of Clearance Deposits
     *
     * This endpoint is used to retrieve a list of Clearance Deposits that can be accessed by the company, admins, and HR And Extra Status of Deposit Will Be 4 (up paid).
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 6,
     *             "type": 3,
     *             "status": 1,
     *             "extra_status": 4,
     *             "serial_mobile_number": 125815,
     *             "mobile_color": "red",
     *             "mobile_type": "samsung",
     *             "mobile_sim": 5422,
     *             "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-310.jpg",
     *             "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-08-28",
     *             "clearance_request_date": "2023-08-30",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",

     *                 "position": null
     *             }
     *         },
     *         {
     *             "id": 7,
     *             "type": 1,
     *             "status": 1,
     *             "extra_status": 4,
     *             "car_number": "0101",
     *             "car_model": "bmw3",
     *             "manufacturing_year": "2023",
     *             "mechanic_card_number": "2348",
     *             "car_image": "http://127.0.0.1:8000/employees_deposits/2023-08-28-EmployeeDeposit-3face.jpeg",
     *            "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-08-28",
     *             "clearance_request_date": "2023-08-30",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */
    public function list_of_clearance_deposits()
    {
        $data = $this->depositService->list_of_clearance_deposits();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = DepositResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData,  "DONE")
            );
        } else {
            return response()->json(['message' => $data['message']], 401);
        }
    }
    /**
     * Show My Approved Deposits List
     *
     * This endpoint is used to display the list of approved deposits for the authenticated employee. Only authenticated employees can access this API. It will show the approved deposits specific to the authenticated employee.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "type": 3,
     *             "status": 1,
     *             "extra_status": null,
     *             "serial_mobile_number": "01hg23",
     *             "mobile_color": "red",
     *             "mobile_type": "samsung201",
     *             "mobile_sim": "5422ll",
     *             "mobile_image": "http://127.0.0.1:8000/employees_deposits/2023-09-10-EmployeeDeposit-1144.jpg",
     *             "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-09-10",
     *             "clearance_request_date": null
     *         },
     *         {
     *             "id": 2,
     *             "type": 2,
     *             "status": 1,
     *             "extra_status": null,
     *             "laptop_type": "asus",
     *             "Serial_laptop_number": "2010",
     *             "laptop_color": "blue",
     *             "laptop_image": "http://127.0.0.1:8000/employees_deposits/2023-09-10-EmployeeDeposit-119.jpeg",
     *             "reason_reject": null,
     *             "reason_clearance_reject": null,
     *             "deposit_request_date": "2023-09-10",
     *             "clearance_request_date": null
     *         }
     *     ]
     * }
     */
    public function my_approved_deposits(GetDepositsList $request)
    {
        $data = $this->depositService->my_approved_deposits($request->generateFilter());
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = DepositResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
}
