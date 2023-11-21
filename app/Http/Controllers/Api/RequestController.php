<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\GetMonthlyShiftListRequest;
use App\Http\Requests\Requests\CreateJustifyRequest;
use App\Http\Requests\Requests\CreateResignationRequest;
use App\Http\Requests\Requests\CreateRetirementRequest;
use App\Http\Requests\Requests\CreateVacationRequest;
use App\Http\Requests\Requests\GetRequestListRequest;
use App\Http\Requests\Requests\RejectVacationRequest;
use App\Http\Resources\Requests\RequestResource;
use App\Services\Requests\RequestService;

/**
 * @group Requests
 * @authenticated
 * APIs for managing Requests
 */
class RequestController extends Controller
{
    public function __construct(private RequestService $requestService)
    {
    }
    /**
     * Add Vacation Request
     *
     * This endpoint is used to add a vacation request. Employee can access this API Once A Day.
     *
     * @bodyParam start_time time required required_if vacation type equal 1 The start time of the vacation request in the format `HH:MM:SS` Custom Example: 09:00:00.
     *
     * @bodyParam end_time time required required_if vacation type equal 1 The end time of the vacation request in the format `HH:MM:SS` Custom Example: 15:30:00.
     *
     * @bodyParam reason string required The reason for the vacation request Custom Example: death of a lover.
     *
     * @bodyParam start_date date required The end date of the vacation request Custom Example: 2023-08-27
     *
     * @bodyParam end_date date required The end date of the vacation request Custom Example: 2023-08-27
     *
     * @bodyParam payment_type int required The payment type of the vacation request. Must be one of the following values:
     * - `1`: payment.
     * - `2`: unpayment. Custom Example: 1.

     * @bodyParam vacation_type int required . Must be one of the following values:
     * - `1`: HOURLY.
     * - `2`: DAILY.
     * - `3`: DEATH.
     * - `4`: SATISFYING.
     * - `5`: PILGRIMAME.
     * - `6`: NEW_BABY.
     * - `7`: EXAM.
     * - `8`: PREGNANT_WOMAN.
     * - `9`: METERNITY.
     * - `10`: SICK_CHILD
     * - `11`: MARRIED
     *
     * @bodyParam person int required_if vacation_type equal 4. Must be one of the following values:
     * - `1`: FATHER.
     * - `2`: MOTHER.
     * - `3`: SISTER.
     * - `4`: PROTHER.
     * - `5`: SON.
     * - `6`: DAUGHTER.
     * - `7`: HUSBAND.
     * - `8`: ME.
     * - `9`: GRAND_FATHER.
     * - `10`: GRAND_MOTHER
     * - `11`: UNCLE.
     * - `12`: AUNT.
     * - `13`: MATERNAL_UNCLE.
     * - `14`: MATERNAL_AUNT. Custom Example: 5.
     *

     * @bodyParam dead_person int required_if vacation_type equal 3 (death). Must be one of the following values:
     * - `1`: FATHER.
     * - `2`: MOTHER.
     * - `3`: SISTER.
     * - `4`: PROTHER.
     * - `5`: SON.
     * - `6`: DAUGHTER.
     * - `7`: HUSBAND.
     * - `8`: ME.
     * - `9`: GRAND_FATHER.
     * - `10`: GRAND_MOTHER
     * - `11`: UNCLE.
     * - `12`: AUNT.
     * - `13`: MATERNAL_UNCLE.
     * - `14`: MATERNAL_AUNT. Custom Example: 5.
     *
     *
     * @bodyParam degree_of_kinship int required_if vacation_type equal 3 (death). Must be one of the following values:
     * - `1`: FIRST.
     * - `2`: SECOND. Custom Example: 2.
     *
     * @bodyParam attachments  file Must not be greater than 5120 kilobytes
     *
     * @response 200 scenario="Add Vacation Request"{
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 3,
     *      "reason": "death of a lover",
     *      "start_time": "05:00:00",
     *      "end_time": "07:00:00",
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-19",
     *      "payment_type": 1,
     *      "vacation_type": 1,
     *      "person": null,
     *      "dead_person": null,
     *      "degree_of_kinship": null,
     *      "created_at": "1 second ago",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * OR
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 3,
     *      "reason": "death of a lover",
     *      "start_time": null,
     *      "end_time": null,
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-25",
     *      "payment_type": 1,
     *      "vacation_type": 4,
     *      "person": 5,
     *      "dead_person": null,
     *      "degree_of_kinship": null,
     *      "created_at": "1 second ago",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * OR
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 3,
     *      "reason": "death of a lover",
     *      "start_time": null,
     *      "end_time": null,
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-25",
     *      "payment_type": 1,
     *      "vacation_type": 4,
     *      "person": null,
     *      "dead_person": 11,
     *      "degree_of_kinship": 2,
     *      "created_at": "1 minute ago",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * }
     */
    public function add_vacation_request(CreateVacationRequest $request)
    {
        $createdData =  $this->requestService->add_vacation_request($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }

    /**
     * Add Justify Request
     *
     * This endpoint is used to add a Justify request. Employee can access this API Once A Day.
     *
     * @bodyParam reason string required The reason for the justify request Custom Example: death of a lover.
     *
     * @bodyParam date date The date of the justify request Custom Example: 2023-08-27
     *
     * @bodyParam start_date date The end date of the justify request Custom Example: 2023-08-27
     *
     * @bodyParam end_date date The end date of the justify request Custom Example: 2023-08-27
     *
     * @bodyParam justify_type int required The justify type of the justify request. Must be one of the following values:
     * - `1`: ILLNESS.
     * - `2`: TRAVEL.
     * - `3`: OTHERS. Custom Example: 2.
     *
     * @bodyParam attachments  file Must not be greater than 5120 kilobytes
     *
     * @response 200 scenario="Add Justify Request"{
     *   "data": {
     *      "id": 2,
     *      "type": 2,
     *      "status": 3,
     *      "justify_type": 1,
     *      "reason": "this is the reason",
     *      "date": "2023-06-22",
     *      "attachments": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * }
     */

    public function add_justify_request(CreateJustifyRequest $request)
    {
        $createdData =  $this->requestService->add_justify_request($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }

    /**
     * Add Retirement Request
     *
     * This endpoint is used to add a retirement request. Employee can access this API Once A Day.
     *
     * @bodyParam reason string required The reason for the retirement request Custom Example: death of a lover.
     *
     * @bodyParam attachments  file Must not be greater than 5120 kilobytes
     *
     * @response 200 scenario="Add Retirement Request"{
     *   "data": {
     *      "id": 3,
     *      "type": 3,
     *      "date": "2023-08-28",
     *      "status": 3,
     *      "reason": "this is the reason for retirement request",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      },
     *       "attachments": null,
     *   }
     * }
     */
    public function add_retirement_request(CreateRetirementRequest $request)
    {
        $createdData =  $this->requestService->add_retirement_request($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }
    /**
     * Add Resignation Request
     *
     * This endpoint is used to add a resignation request. Employee can access this API Once A Day.
     *
     * @bodyParam reason string required The reason for the resignation request Custom Example: death of a lover.
     *
     * @bodyParam attachments  file Must not be greater than 5120 kilobytes
     *
     * @response 200 scenario="Add Resignation Request"{
     *   "data": {
     *      "id": 3,
     *      "type": 4,
     *      "date": "2023-08-28",
     *      "status": 3,
     *      "reason": "this is the reason for resignation request",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      },
     *       "attachments": null,
     *   }
     * }
     */
    public function add_resignation_request(CreateResignationRequest $request)
    {
        $createdData =  $this->requestService->add_resignation_request($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }

    /**
     * Get Request
     *
     * This endpoint is used to Get Request in Company and Admin Or Hr Can Access To This Api.
     *
     * @urlParam id int required Must Be Exists In requests Table
     *@response 201 scenario="Get Request"{
     *   "data": {
     *      "id": 3,
     *      "type": 4,
     *      "date": "2023-08-28",
     *      "status": 3,
     *      "reason": "this is the reason for resignation request",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      },
     *       "attachments": null,
     *   },
     * Or
     *   "data": {
     *      "id": 2,
     *      "type": 2,
     *      "status": 3,
     *      "justify_type": 1,
     *      "reason": "this is the reason",
     *      "date": "2023-06-22",
     *      "attachments": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * Or
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 3,
     *      "reason": "death of a lover",
     *      "start_time": "05:00:00",
     *      "end_time": "07:00:00",
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-19",
     *      "payment_type": 1,
     *      "vacation_type": 1,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * }
     */
    public function show($id)
    {
        $request = $this->requestService->show($id);
        if ($request['success']) {
            $newData = $request['data'];
            $returnData = RequestResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => "Unauthorized"], 401);
        }
    }
    /**
     * Approve Request
     *
     * This endpoint is used to approve a request in the company. Admin or HR can access this API. The status will be set to 1, where number 1 refers to approved,number 2 refers to rejected, and number 3 refers to pending status.
     *
     * @urlParam id int required Must Be Exists In requests Table
     *@response 201 scenario="Approve Request"{
     *   "data": {
     *      "id": 3,
     *      "type": 4,
     *      "date": "2023-08-28",
     *      "status": 1,
     *      "reason": "this is the reason for resignation request",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      },
     *       "attachments": null,
     *   },
     * Or
     *   "data": {
     *      "id": 2,
     *      "type": 2,
     *      "status": 1,
     *      "justify_type": 1,
     *      "reason": "this is the reason",
     *      "date": "2023-06-22",
     *      "attachments": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * Or
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 1,
     *      "reason": "death of a lover",
     *      "start_time": "05:00:00",
     *      "end_time": "07:00:00",
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-19",
     *      "payment_type": 1,
     *      "vacation_type": 1,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * }
     */
    public function approve_request($id)
    {
        $vacationRequest = $this->requestService->approve_request($id);
        if ($vacationRequest['success']) {
            $newData = $vacationRequest['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $vacationRequest['message']];
        }
    }

    /**
     * Rjected Request
     *
     * This endpoint is used to rjected a request in the company. Admin or HR can access this API. The status will be set to 1, where number 1 refers to approved,number 2 refers to rejected, and number 3 refers to pending status.
     *
     * @bodyParam request_id int required Must Be Exists In requests Table
     * @bodyParam reject_reason string required The reject reason for the request Custom Example: Without Any Reason.
     *
     *@response 201 scenario="Rjected Request"{
     *   "data": {
     *      "id": 3,
     *      "type": 4,
     *      "date": "2023-08-28",
     *      "status": 2,
     *      "reason": "this is the reason for resignation request",
     *      "reject_reason": "Without Any Reason",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      },
     *       "attachments": null,
     *   },
     * Or
     *   "data": {
     *      "id": 2,
     *      "type": 2,
     *      "status": 2,
     *      "justify_type": 1,
     *      "reason": "this is the reason",
     *      "reject_reason": "Without Any Reason",
     *      "date": "2023-06-22",
     *      "attachments": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * Or
     *   "data": {
     *      "id": 1,
     *      "type": 1,
     *      "status": 2,
     *      "reason": "death of a lover",
     *      "reject_reason": "Without Any Reason",
     *      "start_time": "05:00:00",
     *      "end_time": "07:00:00",
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-19",
     *      "payment_type": 1,
     *      "vacation_type": 1,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": "mouaz@gmail.com",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *          "position": "Backend Developer"
     *      }
     *   }
     * }
     */


    public function reject_request(RejectVacationRequest $request)
    {
        $createdData =  $this->requestService->reject_request($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = RequestResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Show My Requests List
     *
     * This endpoint displays a list of requests specific to the authenticated employee.
     *
     * @queryParam type integer optional Filter the requests by type. Possible values: 1 (approved), 2 (rejected), 3 (pending).
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 3,
     *             "type": 3,
     *             "date": "2023-08-28",
     *             "status": 3,
     *             "reason": "no reason",
     *             "attachments": null
     *         },
     *         {
     *             "id": 2,
     *             "type": 2,
     *             "status": 3,
     *             "justify_type": 1,
     *             "reason": "this is the reason",
     *             "date": "2023-06-22",
     *             "attachments": null
     *         },
     *         {
     *             "id": 1,
     *             "type": 1,
     *             "status": 3,
     *             "reason": "death of a lover",
     *             "start_time": "05:00:00",
     *             "end_time": "07:00:00",
     *             "start_date": "2023-06-19",
     *             "end_date": "2023-06-19",
     *             "payment_type": 1,
     *             "vacation_type": 1
     *         }
     *     ]
     * }
     */
    public function my_requests(GetRequestListRequest $request)
    {
        $data = $this->requestService->my_requests($request->generateFilter());
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }


    /**
     * Show My Vacation Requests List
     *
     * This endpoint displays a list of Approved Vacation requests specific to the authenticated employee.
     *
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "type": 1,
     *             "status": 1,
     *             "reason": "death of a lover",
     *             "start_time": "05:00:00",
     *             "end_time": "07:00:00",
     *             "start_date": "2023-06-19",
     *             "end_date": "2023-06-19",
     *             "payment_type": 1,
     *             "vacation_type": 1
     *         }
     *         {
     *             "id": 2,
     *             "type": 1,
     *             "status": 1,
     *             "reason": "death of a lover",
     *             "start_time": "05:00:00",
     *             "end_time": "07:00:00",
     *             "start_date": "2023-06-19",
     *             "end_date": "2023-06-19",
     *             "payment_type": 1,
     *             "vacation_type": 1
     *         }
     *     ]
     * }
     */
    public function my_approved_vacations_requests()
    {
        $data = $this->requestService->my_approved_vacations_requests();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }



    /**
     * List Of Vacation Requests
     *
     * This endpoint is used to display a list of vacation requests Where status equal 3 (pending) in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "type": 1,
     *             "status": 3,
     *             "reason": "death of a lover",
     *             "start_time": "05:00:00",
     *             "end_time": "07:00:00",
     *             "start_date": "2023-06-19",
     *             "end_date": "2023-06-19",
     *             "payment_type": 1,
     *             "vacation_type": 1,
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */
    public function vacation_requests()
    {
        $data = $this->requestService->vacation_requests();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }

    /**
     * List Of Justify Requests
     *
     * This endpoint is used to display a list of Justify requests in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 2,
     *             "type": 2,
     *             "status": 3,
     *             "justify_type": 1,
     *             "reason": "this is the reason",
     *             "date": "2023-06-22",
     *             "attachments": null,
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */
    public function justify_requests()
    {
        $data = $this->requestService->justify_requests();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }

    /**
     * List Of Retirement Requests
     *
     * This endpoint is used to display a list of Retirement requests in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 3,
     *             "type": 3,
     *             "date": "2023-08-28",
     *             "status": 3,
     *             "reason": "no reason",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             },
     *             "attachments": null,
     *         }
     *     ]
     * }
     */
    public function retirement_requests()
    {
        $data = $this->requestService->retirement_requests();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }

    /**
     * List Of Resignation Requests
     *
     * This endpoint is used to display a list of Resignation requests in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 4,
     *             "type": 4,
     *             "date": "2023-08-28",
     *             "status": 3,
     *             "reason": "no reason",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "position": null
     *             },
     *             "attachments": null,
     *         }
     *     ]
     * }
     */
    public function resignation_requests()
    {
        $data = $this->requestService->resignation_requests();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = RequestResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
    /**
     * My Monthly Shift
     *
     * This endpoint displays the monthly shift specific to the authenticated employee.
     *
     *  @queryParam duration integer optional Filter the Monthly Shifts by duration. Possible values: 1 (only day absences), 2 (several days absences).
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "date": "2023-08-28",
     *             "status": 1
     *         },
     *         {
     *             "id": 2,
     *             "date": "2023-08-29",
     *             "status": 1
     *         },
     *         {
     *             "id": 3,
     *             "date": "2023-08-30",
     *             "status": 0,
     *             "relatedAbsent": [
     *                 {
     *                     "id": 3,
     *                     "date": "2023-08-30",
     *                     "status": 0
     *                 }
     *             ]
     *         }
     *     ]
     * }
     */
    public function getMonthlyData(GetMonthlyShiftListRequest $request)
    {
        $data = $this->requestService->getMonthlyData($request->generateFilter());
        return $data;
    }

    /**
     * Display All Request In Admin Dashboard
     *
     *This endpoint displays all requests in the admin dashboard, including vacation requests, justification requests, retirement requests, resignation requests, and clearance requests. Each object in the response includes a "type_of_request" flag that indicates the type of request. The "type_of_request" flag can have the following values: "Request"  or "Clearance Request".
     *
     * @response 200 {
     * "data": [
     *   {
     *      "id": 1,
     *      "type_of_request": "Request",
     *      "type": 1,
     *      "status": 3,
     *      "reason": "death of a lover",
     *      "start_time": "05:00:00",
     *      "end_time": "07:00:00",
     *      "start_date": "2023-06-19",
     *      "end_date": "2023-06-19",
     *      "payment_type": 1,
     *      "vacation_type": 1,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": null,
     *          "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *          "position": null
     *       }
     *   },
     *   {
     *      "id": 2,
     *      "type_of_request": "Request",
     *      "type": 2,
     *      "status": 3,
     *      "justify_type": 1,
     *      "reason": "this is the reason",
     *      "date": "2023-06-22",
     *      "attachments": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": null,
     *          "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *          "position": null
     *        }
     *    },
     *    {
     *      "id": 3,
     *      "type_of_request": "Request",
     *      "type": 3,
     *      "date": "2023-08-29",
     *      "status": 3,
     *      "reason": "no reason",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": null,
     *          "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *          "position": null
     *       },
     *      "attachments": null
     *   },
     *   {
     *      "id": 4,
     *      "type_of_request": "Request",
     *      "type": 4,
     *      "date": "2023-08-29",
     *      "status": 3,
     *      "reason": "no reason",
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "email": null,
     *          "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *          "position": null
     *        },
     *      "attachments": null
     *    },
     *    {
     *      "id": 1,
     *      "type_of_request": "Clearance Request",
     *      "type": 1,
     *      "status": 1,
     *      "extra_status": 4,
     *      "car_number": "0101",
     *      "car_model": "bmw3",
     *      "manufacturing_year": "2023",
     *      "mechanic_card_number": "2348",
     *      "car_image": "http://127.0.0.1:8000/employees_deposits/2023-08-29-EmployeeDeposit-359.jpg",
     *      "reason_reject": null,
     *      "reason_clearance_reject": null,
     *      "deposit_request_date": "2023-08-29",
     *      "clearance_request_date": null,
     *      "user": {
     *          "id": 3,
     *          "name": "mouaz alkhateeb",
     *          "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *          "position": null
     *        }
     *    }
     *   ]
     * }
     */
    public function all_requests()
    {
        $data = $this->requestService->all_requests();
        return $data;
    }

    public function show_all_requests()
    {
        $data = $this->requestService->show_all_requests();
        return $data;
    }
}
