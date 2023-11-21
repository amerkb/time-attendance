<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alerts\CrateAlertRequest;
use App\Http\Requests\Alerts\GetAlertsListRequest;
use App\Http\Resources\Alerts\AlertResource;
use App\Http\Resources\PaginationResource;
use App\Services\Alerts\AlertService;


/**
 * @group Alerts
 * @authenticated
 * APIs for managing Alert Operations
 */
class AlertController extends Controller
{
    public function __construct(private AlertService $alertService)
    {
    }
    /**
     * Create New Alert For Employee
     *
     * This endpoint allows admins or HR personnel to create a new alert for an employee in the company.
     *
     * @bodyParam content string required The content of the alert. Must not exceed 100 characters. Example: test alert to this employeeee
     *
     * @bodyParam email email required The email of the employee. Must be a valid email address and exist in the Users table. Example: mouaz@gmail.com
     *
     * @bodyParam type int optional The type of the alert. Must be one of the following values:
     * - `1`: Swearing
     * - `2`: Fabricate Problems
     * - `3`: Others. Example: 1
     *
     * @response 200 scenario="Success"{
     *     "data": {
     *         "id": 3,
     *         "email": "mouaz@gmail.com",
     *         "content": "test alert to this employeeee",
     *         "created_at": "2 seconds ago",
     *         "user": {
     *             "id": 2,
     *             "name": "Firass Jabi",
     *             "image": null,
     *             "position": null
     *         }
     *     }
     * }
     */
    public function store(CrateAlertRequest $request)
    {
        $createdData =  $this->alertService->create_alert($request->validated());
        if ($createdData['success']) {
            $alert = $createdData['data'];
            $returnData = AlertResource::make($alert);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Show My Alerts List
     *
     * This endpoint displays the list of alerts that are specific to the authenticated employee, and requires authentication to access.
     *
     * @response 200 scenario="Success"{
     *     "data": [
     *         {
     *             "id": 3,
     *             "email": "mouaz@gmail.com",
     *             "content": "test alert to this employeeee",
     *             "created_at": "2023-08-27",
     *             "user": {
     *                 "id": 2,
     *                 "name": "Firass Jabi",
     *                 "image": null,
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */

    public function getMyAlert(GetAlertsListRequest $request)
    {
        $data = $this->alertService->getMyAlerts($request->generateFilter());

        $returnData = AlertResource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
    /**
     * Show All Alerts List
     *
     * This endpoint displays the list of alerts in System, and only  authentication to access.
     *
     * @response 200 scenario="Success"{
     *     "data": [
     *         {
     *             "id": 3,
     *             "email": "mouaz@gmail.com",
     *             "content": "test alert to this employeeee",
     *             "created_at": "2023-08-27",
     *             "user": {
     *                 "id": 2,
     *                 "name": "Firass Jabi",
     *                 "image": null,
     *                 "position": null
     *             }
     *         }
     *         {
     *             "id": 4,
     *             "email": "hamza@gmail.com",
     *             "content": "test alert to this employeeee",
     *             "created_at": "2023-08-27",
     *             "user": {
     *                 "id": 2,
     *                 "name": "Firass Jabi",
     *                 "image": null,
     *                 "position": null
     *             }
     *         }
     *     ]
     * }
     */
    public function all_alerts(GetAlertsListRequest $request)
    {
        $data = $this->alertService->all_alerts($request->generateFilter());

        $returnData = AlertResource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
}
