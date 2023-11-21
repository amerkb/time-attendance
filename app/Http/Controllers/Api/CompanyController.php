<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\AddCommercialRecordRequeat;
use App\Http\Requests\Company\CheckTypeRequest;
use App\Http\Requests\Company\CreateComapnyRequest;
use App\Http\Requests\Company\GetCompaniesListRequest;
use App\Http\Requests\Company\UpdateCommercialRecordRequeat;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\Company\ComapnyResource;
use App\Http\Resources\Company\CompanyPercentageResource;
use App\Services\Company\CompanyService;


/**
 * @group Companies
 * @authenticated
 * APIs for managing Companies
 */
class CompanyController extends Controller
{

    public function __construct(private CompanyService $companyService)
    {
    }


    /**
     * Create Company
     *
     * This endpoint is used to create a new company. Only super admins can access this API.
     *
     * @bodyParam name string required The name of the company. Custom  Example: Goma Company
     * @bodyParam email email required The email address of the company. Custom  Example: goma@goma.com
     * @bodyParam longitude number The longitude of the company location. Custom  Example: 25.12
     * @bodyParam latitude number The latitude of the company location. Custom Example: 15.32
     * @bodyParam radius number The radius of the company location. Custom Example: 15
     * @bodyParam mac_address array An array of Mac Addresses for the company.
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": null,
     *         "start_commercial_record": null,
     *         "end_commercial_record": null,
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function store(CreateComapnyRequest $request)
    {
        $createdData =  $this->companyService->create_company($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ComapnyResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Add Commercal To Company
     *
     * This endpoint is used to create a new company. Only super admins can access this API.
     * @bodyParam company_id int required The ID of the company. Must exist in the companies table.
     * @bodyParam start_commercial_record date The start date of the commercial record. Custom Example: 2023-08-27
     * @bodyParam end_commercial_record date The end date of the commercial record. Custom  Example: 2023-08-27
     * @bodyParam commercial_record file The commercial record file. Must not be greater than 5120 kilobytes.
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": "http://127.0.0.1:8000/companies/2023-09-05-Company-2.png",
     *         "start_commercial_record": "2023-02-01",
     *         "end_commercial_record": "2023-09-01",
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function add_commercial_record(AddCommercialRecordRequeat $request)
    {
        $createdData =  $this->companyService->add_commercial_record($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ComapnyResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Show Comapny
     *
     * This endpoint is used to display company and authenticate admin access to this API. It will show company specific to the authenticated admin Or Super Admin.
     *
     * @urlParam id int required Must Be Exists In companies Table
     *
     * @response 201 scenario="Show Comapny"{
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": "http://127.0.0.1:8000/companies/2023-09-05-Company-2.png",
     *         "start_commercial_record": "2023-02-01",
     *         "end_commercial_record": "2023-09-01",
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function show($id)
    {
        $company = $this->companyService->show($id);

        if ($company['success']) {
            $alert = $company['data'];
            $returnData = ComapnyResource::make($alert);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $company['message']];
        }
    }

    /**
     * Show Company Percentage
     *
     * This endpoint is used to display the company percentage. Only authenticated admins or HR personnel can access this API. It will show the percentage specific to the authenticated admin or HR personnel.
     *
     * @response 200 {
     *     "data": {
     *         "percentage": false
     *     }
     * }
     */
    public function show_percenatge_company()
    {
        $company = $this->companyService->show_percenatge_company();

        if ($company['success']) {
            $data = $company['data'];
            $returnData = CompanyPercentageResource::make($data);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $company['message']];
        }
    }

    /**
     * Show Company Check Type
     *
     * This endpoint is used to display the company Check Type. Only authenticated admins or HR personnel can access this API. It will show the percentage specific to the authenticated admin or HR personnel.
     *
     * @response 200 {
     *        "check_type": 1
     * }
     */
    public function show_check_type_company()
    {
        $company = $this->companyService->show_check_type_company();
        return $company;
    }

    /**
     * Update Company Percentage
     *
     * This endpoint is used to Update the company percentage. Only authenticated admins or HR personnel can access this API. It will Update the percentage specific to the authenticated admin or HR personnel.
     *
     * @response 200 {
     *      "percentage": true
     * }
     */
    public function update_percentage()
    {
        $company = $this->companyService->update_percentage();

        if ($company['success']) {
            $alert = $company['data'];
            $returnData = CompanyPercentageResource::make($alert);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $company['message']];
        }
    }


    /**
     * Update Company
     *
     * This endpoint is used to update a company. Only super admins can access this API.
     *
     * @bodyParam company_id int required The ID of the company. Must exist in the companies table.
     * @bodyParam name string required The name of the company. Custom Example: Goma Company
     * @bodyParam email email required The email address of the company.Custom Example: goma@goma.com
     * @bodyParam longitude number required The longitude of the company location. Custom  Example: 25.12
     * @bodyParam latitude number required The latitude of the company location. Custom Example: 15.32
     * @bodyParam radius number required The radius of the company location. Custom Example: 15
     * @bodyParam mac_address array An array of Mac Addresses for the company.
     * @bodyParam address_id int required required if mac_address array exists The ID of the Address of company. Must exist in the addreses table.

     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": null,
     *         "start_commercial_record": "2023-02-01",
     *         "end_commercial_record": "2023-09-01",
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function update_comapny(UpdateCompanyRequest $request)
    {
        $createdData =  $this->companyService->update_company($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ComapnyResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Update Company Check Type
     *
     * This endpoint is used to Update the company Check Type. Only authenticated admins or HR personnel can access this API. It will show the percentage specific to the authenticated admin or HR personnel.
     * @bodyParam type int required. Must Be 1 (Mac Address) Or 2 (Location) Or 3 (Together),Custom Example: 2
     * @response 200 {
     *     "data": {
     *         "check_type": 1
     *     }
     * }
     */
    public function update_check_type(CheckTypeRequest $request)
    {
        $createdData =  $this->companyService->update_check_type($request->validated());
        return $createdData;
    }


    /**
     * Update Commercal Record in Company
     *
     * This endpoint is used to update commercal record and authenticate admin access to this API. It will update commercal comapny to the authenticated admin.
     *
     * @bodyParam company_id int required The ID of the company. Must exist in the companies table.
     * @bodyParam start_commercial_record date The start date of the commercial record. Custom Example: 2023-08-27
     * @bodyParam end_commercial_record date The end date of the commercial record. Custom  Example: 2023-08-27
     * @bodyParam commercial_record file The commercial record file. Must not be greater than 5120 kilobytes.
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": "http://127.0.0.1:8000/companies/2023-09-05-Company-2.png",
     *         "start_commercial_record": "2023-02-01",
     *         "end_commercial_record": "2023-09-01",
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function update_commercial_record(UpdateCommercialRecordRequeat $request)
    {
        $createdData =  $this->companyService->update_commercial_record($request->validated());

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ComapnyResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }


    /**
     * Retrieve List of Companies
     *
     * This endpoint is used to retrieve the list of companies in the system. Only Super admins can access this API.
     *
     * @response 200 scenario="Show Companies"{
     *     "data": [
     *         {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": null,
     *         "start_commercial_record": null,
     *         "end_commercial_record": null,
     *         "check_type": 1,
     *         "number_of_employees": 0,
     *         "number_of_dismissed_employees": 0,
     *             "locations": [
     *                 {
     *                     "id": integer,
     *                     "longitude": "60.33",
     *                     "latitude": "50.22",
     *                     "radius": "50"
     *                 }
     *             ],
     *             "addresess": [
     *                 {
     *                     "id": 1,
     *                     "mac_address": "Ab:goma:123456"
     *                 }
     *             ],
     *             "employees": [],
     *             "dismissedEmployees": [],
     *             "admin": null
     *         }
     *     ]
     * }
     */
    public function list_of_companies()
    {
        $data = $this->companyService->list_of_companies();

        if ($data['success']) {
            $newData = $data['data'];
            $returnData = ComapnyResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData,  "DONE")
            );
        } else {
            return response()->json(['message' => $data['message']], 401);
        }
    }

    /**
     * Retrieve List of Archive Companies
     *
     * This endpoint is used to retrieve the list of Archive companies in the system. Only Super admins can access this API.
     *
     * @response 200 scenario="Show  Archive Companies"{
     *     "data": [
     *         {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": null,
     *         "start_commercial_record": null,
     *         "end_commercial_record": null,
     *         "check_type": 1,
     *         "number_of_employees": 0,
     *         "number_of_dismissed_employees": 0,
     *             "locations": [
     *                 {
     *                     "id": integer,
     *                     "longitude": "60.33",
     *                     "latitude": "50.22",
     *                     "radius": "50"
     *                 }
     *             ],
     *             "addresess": [
     *                 {
     *                     "id": 1,
     *                     "mac_address": "Ab:goma:123456"
     *                 }
     *             ],
     *             "employees": [],
     *             "dismissedEmployees": [],
     *             "admin": null
     *         }
     *     ]
     * }
     */
    public function list_of_archive_companies()
    {
        $data = $this->companyService->list_of_archive_companies();

        if ($data['success']) {
            $newData = $data['data'];
            $returnData = ComapnyResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData,  "DONE")
            );
        } else {
            return response()->json(['message' => $data['message']], 401);
        }
    }


    /**
     * Archive Company From the System
     *
     * This endpoint is used to Archive Company From the System and Super Admin Can Access To This Api.
     *
     *@urlParam id int required Must Be Exists In Companies Table
     * @response 200 scenario="Archive a Company"{
     *     "message": "Company Archived..!"
     * }
     */
    public function destroy($id)
    {
        $deletionResult = $this->companyService->destroy($id);
        if (is_string($deletionResult)) {
            return response()->json(["message" => "Company Archived..!"]);
        }
        return response()->json(["message" => "Company Archived..!"]);
    }

    /**
     * Destroy Company From the System
     *
     * This endpoint is used to Destroy Company From the System and Super Admin Can Access To This Api.
     *
     *@urlParam id int required Must Be Exists In Companies Table
     * @response 200 scenario="Delete a Company"{
     *     "message": "Company Deleted..!"
     * }
     */
    public function force_delete($id)
    {
        $deletionResult = $this->companyService->force_delete($id);
        if (is_string($deletionResult)) {
            return response()->json(["message" => "Company Deleted..!"]);
        }
        return response()->json(["message" => "Company Deleted..!"]);
    }
    /**
     * Restore Company
     *
     * This endpoint is used to Restore a company. Only super admins can access this API.
     *
     * @urlParam id int required Must Be Exists In Companies Table
     *
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Goma Company",
     *         "email": "goma@goma.com",
     *         "commercial_record": null,
     *         "start_commercial_record": null,
     *         "end_commercial_record": null,
     *         "check_type": 1,
     *         "locations": [
     *             {
     *                 "id": 1,
     *                 "Longitude": "25.12",
     *                 "Latitude": "15.32",
     *                 "Radius": "21"
     *             }
     *         ],
     *         "addresess": [
     *             {
     *         "id": 1,
     *          "mac_address": "Ab:goma:123456"
     *             }
     *         ],
     *         "admin": null
     *     }
     * }
     */
    public function restore_comapny($id)
    {
        $createdData =  $this->companyService->restore($id);

        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ComapnyResource::make($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
}
