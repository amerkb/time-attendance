<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\TerminateContractRequest;
use App\Http\Requests\Employees\AdminUpdateEmployeeRequest;
use App\Http\Requests\Employees\AttendancsOverviewListRequest;
use App\Http\Requests\Employees\CheckAddessRequest;
use App\Http\Requests\Employees\CheckInAttendanceRequest;
use App\Http\Requests\Employees\CheckLocationRequest;
use App\Http\Requests\Employees\CheckOutAttendanceRequest;
use App\Http\Requests\Employees\CreateAdminRequest;
use App\Http\Requests\Employees\CreateEmployeeRequest;
use App\Http\Requests\Employees\DetermineWorkingHoursRequest;
use App\Http\Requests\Employees\GetContractExpirationList;
use App\Http\Requests\Employees\GetEmployeesAttendancesListRequest;
use App\Http\Requests\Employees\GetEmployeesListRequest;
use App\Http\Requests\Employees\GetEmployeesSalariesListRequest;
use App\Http\Requests\Employees\GetLeaveCalendarListRequest;
use App\Http\Requests\Employees\RewerdsAdversriesSalaryRequest;
use App\Http\Requests\Employees\UpdateEmailRequest;
use App\Http\Requests\Employees\UpdateEmployeeContractRequest;
use App\Http\Requests\Employees\UpdateEmployeePermissionRequest;
use App\Http\Requests\Employees\UpdateEmployeeRequest;
use App\Http\Requests\Employees\UpdateSalaryRequest;
use App\Http\Requests\Nationalitie\GetNationalitiesRequest;
use App\Http\Resources\Admin\DashboardDataResource;
use App\Http\Resources\Admin\EmployeeResource;
use App\Http\Resources\Contract\ContractResource;
use App\Http\Resources\Employees\AttendanceResource;
use App\Http\Resources\Employees\EmployeeAvailableTimeResource;
use App\Http\Resources\Employees\LeaveCalendarListResource;
use App\Http\Resources\Employees\SalaryResource;
use App\Http\Resources\Nationalitie\NationalitiesRrsource;
use App\Http\Resources\Percentage\PercentageResource;
use App\Http\Resources\Shifts\ShiftRersource;
use App\Services\Admin\AdminService;

/**
 * @group Admin Operations
 *
 * @authenticated
 *
 * APIs for managing Admin Operations
 */
class AdminController extends Controller
{

    public function __construct(private AdminService $adminService)
    {
    }
    /**
     * Add Employee to the Company
     *
     * This endpoint is used to Add Employee to the Company and Just Admin Or Hr Can Access To This Api.
     *
     * @bodyParam name string required The name of the employee must be required Example:mouaz alkhateeb.
     *
     * @bodyParam email string required The email of the employee, The email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol.
     *                                 Example: mouaz@gmail.com
     *
     * @bodyParam password string required The password of the employee. Must not be greater than 24 characters and should contain both numbers and characters. Example: Abc071096904038
     *
     * @bodyParam work_email email The email work of the employee, The work_email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol. Example:mouazalkhateeb@goma.com.
     *
     * @bodyParam image file Must not be greater than 5120 kilobytes
     *
     * @bodyParam id_photo file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam biography file  Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam visa file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam municipal_card file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam health_insurance file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam passport file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam employee_residence file Must be image or pdf and not be greater than 5120 kilobytes
     *
     *
     *
     * @bodyParam serial_number number required Custom Example: 0101122
     *
     * @bodyParam birthday_date date Custom Example: 2023-08-27
     *
     * @bodyParam nationalitie_id int required Must be from 1 to 56
     *
     * @bodyParam material_status int optional The material status of the user. Must be one of the following values:
     * - `1`: single status.
     * - `2`: married status.
     * - `3`: married status.
     * - `4`: widower status. Custom Example: 1
     *
     * @bodyParam gender int optional The gender of the user. Must be one of the following values:
     * - `1`: male.
     * - `2`: female. Custom Example: 1
     *
     * @bodyParam departement string Custom Example: It
     *
     * @bodyParam position  string Custom Example: Developer
     *
     * @bodyParam address  string Custom Example: Damascus
     *
     * @bodyParam guarantor  string Custom Example: Mouaz Alkhateeb
     *
     * @bodyParam branch  string Custom Example: Syria
     *
     * @bodyParam skills  string Custom Example: Drwaing
     *
     *
     * @bodyParam start_job_contract date required Custom Example: 2023-08-27
     * @bodyParam end_job_contract date required Custom Example: 2023-08-27
     * @bodyParam end_visa date Custom Example: 2023-08-27
     * @bodyParam end_employee_sponsorship date Custom Example: 2023-08-27
     * @bodyParam end_municipal_card date Custom Example: 2023-08-27
     * @bodyParam end_health_insurance date Custom Example: 2023-08-27
     * @bodyParam end_employee_residence date Custom Example: 2023-08-27
     *
     *
     * @bodyParam leave_time number Must be one of the following values:
     * - 30.
     * - 60.
     * - 90  Custom Example: 60
     *
     *
     * @bodyParam entry_time number Must be one of the following values:
     * - 30.
     * - 60.
     * - 90  Custom Example: 90
     *
     * @bodyParam basic_salary double required  Custom Example: 250000
     *
     * @bodyParam housing_allowance double The housing allowance. Custom Example: 100000
     *
     * @bodyParam transportation_allowance double The transportation allowance. Custom Example: 80000
     *
     * @bodyParam hourly_annual number  Custom Example: 12
     * @bodyParam daily_annual number  Custom Example: 12
     * @bodyParam number_of_shifts int Must be at least 1 Custom Example: 3
     *
     * @bodyParam shifts array optional An array of shift for the user. Required when `number_of_shifts` is not null.
     * @bodyParam user_id int required optional The ID of the user That Exists in users Table.
     * @bodyParam start_time time required The start time of the shift in the format `HH:MM:SS` Custom Example: 09:00:00.
     * @bodyParam end_time time required The end time of the shift in the format `HH:MM:SS` Custom Example: 09:00:00.
     * @bodyParam start_break_hour time required The start time of the break in the format `HH:MM:SS` Custom Example: 09:00:00.
     * @bodyParam end_break_hour time required The end time of the break in the format `HH:MM:SS` Custom Example: 09:00:00.

     *@response 201 scenario="Add a Employee"{
     * "data": {
     * "id": 4,
     * "name": "mouaz alkhateeb",
     * "email": "mouaz@gmail.com",
     * "work_email": "mouazalkhateeb@gmail.com",
     * "status": 3,
     * "type": 4,
     * "gender": "1",
     * "mobile": "0969040322",
     * "phone": "0969040322",
     * "departement": "it",
     * "address": "Damascus",
     * "position": null,
     * "skills": "no skills",
     * "serial_number": "000007",
     * "birthday_date": "2022-11-26",
     * "marital_status": null,
     * "guarantor": "admin",
     * "branch": "syria branch",
     * "start_job_contract": "2023-08-01",
     * "end_job_contract": "2023-10-01",
     * "end_visa": "2023-09-11",
     * "end_passport": "2023-09-11",
     * "end_employee_sponsorship": null,
     * "end_municipal_card": "2023-09-10",
     * "end_health_insurance": "2023-09-14",
     * "end_employee_residence": "2023-09-20",
     * "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     * "id_photo": null,
     * "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     * "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     * "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     * "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     * "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     * "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     * "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     * "entry_time": 30,
     * "leave_time": 60,
     * "number_of_working_hours": 0,
     * "nationalitie": {
     *   "name": "Syrian"
     *  },
     * "percentage": "0",
     * "basic_salary": 200000,
     * "shifts": [
     *   {
     *      "id": 3,
     *      "start_time": "09:00:00",
     *      "end_time": "15:00:00",
     *      "start_break_hour": "12:30:00",
     *      "end_break_hour": "13:00:00"
     *   },
     *   {
     *     "id": 4,
     *     "start_time": "16:00:00",
     *     "end_time": "22:00:00",
     *     "start_break_hour": "20:30:00",
     *     "end_break_hour": "21:00:00"
     *   }
     * ],
     *         "deposits": []
     *         "my_alerts": []
     *  }
     * }
     */
    public function store(CreateEmployeeRequest $request)
    {

        $createdData =  $this->adminService->create_employee($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }

    /**
     * Add Hr to the Company
     *
     * This endpoint is used to Add Hr to the Company and Just Admin Can Access To This Api.
     *
     * @bodyParam name string required The name of the employee must be required Example:mouaz alkhateeb.
     *
     * @bodyParam email string required The email of the employee, The email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol.
     *                                 Example: mouaz@gmail.com
     *
     * @bodyParam password string required The password of the employee. Must not be greater than 24 characters and should contain both numbers and characters. Example: Abc071096904038
     *
     * @bodyParam work_email email The email work of the employee, The work_email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol. Example:mouazalkhateeb@goma.com.
     *
     * @bodyParam image file Must not be greater than 5120 kilobytes
     *
     * @bodyParam id_photo file Must be pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam biography file  Must not be greater than 5120 kilobytes
     *
     * @bodyParam visa file Must not be greater than 5120 kilobytes
     *
     * @bodyParam municipal_card file Must not be greater than 5120 kilobytes
     *
     * @bodyParam health_insurance file Must not be greater than 5120 kilobytes
     *
     * @bodyParam passport  file Must not be greater than  kilobytes
     * @bodyParam serial_number number required Custom Example: 0101122
     * @bodyParam birthday_date date Custom Example: 2023-08-27
     * @bodyParam nationalitie_id int required Must be from 1 to 56
     * @bodyParam material_status int optional The material status of the user. Must be one of the following values:
     * - `1`: single status.
     * - `2`: married status.
     * - `3`: divorced status. Custom Example: 1
     *
     *
     * @bodyParam departement string Custom Example: It
     *
     * @bodyParam position  string Custom Example: Developer
     *
     * @bodyParam address  string Custom Example: Damascus
     *
     * @bodyParam guarantor  string Custom Example: Mouaz Alkhateeb
     *
     * @bodyParam branch  string Custom Example: Syria
     *
     * @bodyParam skills  string Custom Example: Drwaing
     *
     * @bodyParam status int optional The status of the user. Must be one of the following values:
     * - `1`: on duty.
     * - `2`: on vacation. Custom Example: 1
     *
     * @bodyParam start_job_contract date required Custom Example: 2023-08-27
     * @bodyParam end_job_contract date required Custom Example: 2023-08-27
     * @bodyParam end_visa date Custom Example: 2023-08-27
     * @bodyParam end_employee_sponsorship date Custom Example: 2023-08-27
     * @bodyParam end_municipal_card date Custom Example: 2023-08-27
     * @bodyParam end_health_insurance date Custom Example: 2023-08-27
     * @bodyParam end_employee_residence date Custom Example: 2023-08-27
     * @bodyParam end_passport date Custom Example: 2023-08-27
     *
     * @bodyParam basic_salary double required  Custom Example: 250000
     *
     *@response 201 scenario="Add a Hr"{
     * "data": {
     * "id": 5,
     * "name": "Hamza Fawaz",
     * "email": "hamzafawaz123@gmail.com",
     * "work_email": "hamzafawaz122@gmail.com",
     * "status": "1",
     * "type": 3,
     * "gender": "1",
     * "mobile": "0969010781",
     * "phone": "0935461184",
     * "departement": "it",
     * "address": "Damascus",
     * "position": null,
     * "skills": "no skills",
     * "serial_number": "000003",
     * "birthday_date": "1998-11-26",
     * "marital_status": null,
     * "guarantor": "admin",
     * "branch": "syria branch",
     * "start_job_contract": "2023-06-01",
     * "end_job_contract": "2023-09-01",
     * "end_visa": null,
     * "end_passport": null,
     * "end_employee_sponsorship": null,
     * "end_municipal_card": null,
     * "end_health_insurance": null,
     * "end_employee_residence": null,
     * "image": null,
     * "id_photo": null,
     * "biography": null,
     * "employee_sponsorship": null,
     * "visa": null,
     * "passport": null,
     * "municipal_card": null,
     * "health_insurance": null,
     * "employee_residence": null,
     * "entry_time": null,
     * "leave_time": null,
     * "percentage": "0",
     * "is_verifed": false,
     * "basic_salary": 200000
     *}
     */

    public function store_hr(CreateEmployeeRequest $request)
    {
        $createdData =  $this->adminService->create_hr($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Add Admin to the System
     *
     * This endpoint is used to Add Admin to the System and Just Super Admin Can Access To This Api.
     *
     * @bodyParam name string required The name of the admin must be required Example:Firas Aljabi.
     *
     * @bodyParam email string required The email of the admin, The email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol.
     *                                 Example: mouaz@gmail.com
     *
     * @bodyParam password string required The password of the admin. Must not be greater than 24 characters and should contain both numbers and characters. Example: Abc071096904038
     *
     * @bodyParam work_email email The email work of the employee, The work_email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol. Example:mouazalkhateeb@goma.com.
     *
     * @bodyParam image file Must not be greater than 5120 kilobytes
     * @bodyParam serial_number number required Custom Example: 0101122
     * @bodyParam birthday_date date Custom Example: 2023-08-27
     * @bodyParam nationalitie_id int required Must be from 1 to 56
     * @bodyParam comapny_id int required Must be exists in companies table.
     *
     * @bodyParam gender int optional The gender of the admin. Must be one of the following values:
     * - `1`: male.
     * - `2`: female. Custom Example: 1
     *
     * @bodyParam address  string Custom Example: Damascus
     *
     * @bodyParam branch  string Custom Example: Syria
     *
     * @response 201 scenario="Add a Admin"{
     *  "data": {
     *     "id": 6,
     *     "name": "Firas Jabi",
     *     "email": "firassaljabi1232@gmail.com",
     *    "work_email": "firassaljabi1237@goma.com",
     *     "status": null,
     *     "type": 2,
     *     "gender": "1",
     *     "mobile": "0969040342",
     *     "phone": "0935463111",
     *     "departement": null,
     *     "address": "Damascus",
     *    "position": null,
     *     "skills": null,
     *    "serial_number": "00011",
     *     "birthday_date": "1998-11-26",
     *    "marital_status": null,
     *    "guarantor": null,
     *     "branch": "syria branch",
     *     "start_job_contract": null,
     *     "end_job_contract": null,
     *     "end_visa": null,
     *     "end_passport": null,
     *     "end_employee_sponsorship": null,
     *     "end_municipal_card": null,
     *     "end_health_insurance": null,
     *     "end_employee_residence": null,
     *     "image": null,
     *     "id_photo": null,
     *     "biography": null,
     *     "employee_sponsorship": null,
     *     "visa": null,
     *     "passport": null,
     *     "municipal_card": null,
     *     "health_insurance": null,
     *     "employee_residence": null,
     *     "entry_time": null,
     *     "leave_time": null,
     *     "percentage": "0",
     *     "basic_salary": 0,
     *     "is_verifed": false,
     * }
     * }
     */

    public function store_admin(CreateAdminRequest $request)
    {
        $createdData =  $this->adminService->create_admin($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * An Employee Edits His personal Information
     *
     * This endpoint is used to An Employee Edits His personal Information in the Company and Just Same Employee Can Access To This Api.
     *
     * @bodyParam email string The email of the employee, The email must have a maximum length of 255 characters. It should follow the pattern of having 16 letters before the @ symbol.
     *                                 Example: mouaz@gmail.com
     *
     * @bodyParam image file Must not be greater than 5120 kilobytes
     *
     * @bodyParam address  string Custom Example: Damascus
     * @bodyParam skills  string Custom Example: Programming
     *
     *@response 201 scenario="An Employee Edits His personal Information"{
     * "data": {
     * "id": 4,
     * "name": "mouaz alkhateeb",
     * "email": "mouaz@gmail.com",
     * "work_email": "mouazalkhateeb@gmail.com",
     * "status": 1,
     * "type": 4,
     * "gender": 1,
     * "mobile": "0969040322",
     * "phone": "0969040322",
     * "departement": "it",
     * "address": "Damascus",
     * "position": null,
     * "skills": "no skills",
     * "serial_number": "000007",
     * "birthday_date": "2022-11-26",
     * "marital_status": null,
     * "guarantor": "admin",
     *"branch": "syria branch",
     * "start_job_contract": "2023-08-01",
     * "end_job_contract": "2023-10-01",
     * "end_visa": "2023-09-11",
     * "end_passport": "2023-09-11",
     * "end_employee_sponsorship": null,
     * "end_municipal_card": "2023-09-10",
     * "end_health_insurance": "2023-09-14",
     * "end_employee_residence": "2023-09-20",
     * "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *"id_photo": null,
     * "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     * "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     * "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *"passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     * "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     * "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     * "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     * "entry_time": "30",
     *"leave_time": "60",
     *"is_verifed": false,
     *"number_of_working_hours": 0,
     *"nationalitie": {
     *   "name": "Syrian"
     * },
     *"percentage": "0",
     * "basic_salary": 200000,
     * "shifts": [
     *   {
     *      "id": 3,
     *     "start_time": "09:00:00",
     *    "end_time": "15:00:00",
     *   "start_break_hour": "12:30:00",
     *   "end_break_hour": "13:00:00"
     *},
     *{
     *   "id": 4,
     *   "start_time": "16:00:00",
     *  "end_time": "22:00:00",
     *  "start_break_hour": "20:30:00",
     *  "end_break_hour": "21:00:00"
     *}
     * ],
     * "deposits": []
     * "my_alerts": []
     * }
     *}
     */
    public function update_employee(UpdateEmployeeRequest $request)
    {
        $createdData =  $this->adminService->update_employee($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * An Employee Edits His Email
     *
     * This endpoint allows an employee to edit their personal information in the company. Only the employee themselves can access this API.
     *
     * @bodyParam email string required The email of the employee. The email must have a maximum length of 255 characters and follow the pattern of having 16 letters before the @ symbol. Example: moaz.alkhateeb99@gmail.com
     *
     * @response 201 scenario="Success"{
     *     "data": {
     *         "id": 4,
     *         "name": "mouaz alkhateeb",
     *         "email": "mouaz@gmail.com",
     *         "work_email": "mouazalkhateeb@gmail.com",
     *         "status": 1,
     *         "type": 4,
     *         "gender": 1,
     *         "mobile": "0969040322",
     *         "phone": "0969040322",
     *         "departement": "it",
     *         "address": "Damascus",
     *         "position": null,
     *         "skills": "no skills",
     *         "serial_number": "000007",
     *         "birthday_date": "2022-11-26",
     *         "marital_status": null,
     *         "guarantor": "admin",
     *         "branch": "syria branch",
     *         "start_job_contract": "2023-08-01",
     *         "end_job_contract": "2023-10-01",
     *         "end_visa": "2023-09-11",
     *         "end_passport": "2023-09-11",
     *         "end_employee_sponsorship": null,
     *         "end_municipal_card": "2023-09-10",
     *         "end_health_insurance": "2023-09-14",
     *         "end_employee_residence": "2023-09-20",
     *         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *         "id_photo": null,
     *         "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *         "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *         "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *         "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *         "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *         "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *         "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *         "entry_time": "30",
     *         "leave_time": "60",
     *         "is_verified": false,
     *         "number_of_working_hours": 0,
     *         "nationality": {
     *             "name": "Syrian"
     *         },
     *         "percentage": "0",
     *         "basic_salary": 200000,
     *         "shifts": [
     *             {
     *                 "id": 3,
     *                 "start_time": "09:00:
     *                  "end_time": "15:00:00",
     *                  "start_break_hour": "12:30:00",
     *                  "end_break_hour": "13:00:00"
     *             },
     *             {
     *                  "id": 4,
     *                  "start_time": "16:00:00",
     *                  "end_time": "22:00:00",
     *                  "start_break_hour": "20:30:00",
     *                  "end_break_hour": "21:00:00"
     *             }
     *      ],
     *         "deposits": [],
     *         "my_alerts": []
     *     }
     *   }
     */
    public function update_email(UpdateEmailRequest $request)
    {
        $createdData =  $this->adminService->update_email($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }



    /**
     * Admin Update Employee in the Company
     *
     * This endpoint is used to Update Employee Data in the Company and Just Admin Or Hr Can Access To This Api.
     *
     *@bodyParam user_id int required Must Be Exists In Users Table

     * @bodyParam material_status int optional The material status of the user. Must be one of the following values:
     * - `1`: single status.
     * - `2`: married status.
     * - `3`: married status.
     * - `4`: widower status. Custom Example: 1
     *
     * @bodyParam housing_allowance double The housing allowance. Custom Example: 100000
     *
     * @bodyParam transportation_allowance double The transportation allowance. Custom Example: 80000
     *
     * @bodyParam end_visa date Custom Example: 2023-08-27
     * @bodyParam end_employee_sponsorship date Custom Example: 2023-08-27
     * @bodyParam end_municipal_card date Custom Example: 2023-08-27
     * @bodyParam end_health_insurance date Custom Example: 2023-08-27
     * @bodyParam end_employee_residence date Custom Example: 2023-08-27
     * @bodyParam end_passport date Custom Example: 2023-08-27

     * @bodyParam birthday_date date Custom Example: 2023-08-27
     *
     * @bodyParam departement string Custom Example: It
     *
     * @bodyParam position  string Custom Example: Developer
     *
     * @bodyParam address  string Custom Example: Damascus
     *
     * @bodyParam guarantor  string Custom Example: Mouaz Alkhateeb
     *
     * @bodyParam branch  string Custom Example: Syria
     *
     * @bodyParam skills  string Custom Example: Drwaing



     * @bodyParam id_photo file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam biography file  Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam visa file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam municipal_card file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam health_insurance file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam passport file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam employee_residence file Must be image or pdf and not be greater than 5120 kilobytes
     *
     * @bodyParam employee_sponsorship file Must be image or pdf and not be greater than 5120 kilobytes
     *
     *@response 201 scenario="Admin Update Employee"{
     * "data": {
     * "id": 4,
     * "name": "mouaz alkhateeb",
     * "email": "mouaz@gmail.com",
     * "work_email": "mouazalkhateeb@gmail.com",
     * "status": 1,
     * "type": 4,
     * "gender": 1,
     * "mobile": "0969040322",
     * "phone": "0969040322",
     * "departement": "it",
     * "address": "Damascus",
     * "position": null,
     * "skills": "no skills",
     * "serial_number": "000007",
     * "birthday_date": "2022-11-26",
     * "marital_status": null,
     * "guarantor": "admin",
     *"branch": "syria branch",
     * "start_job_contract": "2023-08-01",
     * "end_job_contract": "2023-10-01",
     * "end_visa": "2023-09-11",
     * "end_passport": "2023-09-11",
     * "end_employee_sponsorship": null,
     * "end_municipal_card": "2023-09-10",
     * "end_health_insurance": "2023-09-14",
     * "end_employee_residence": "2023-09-20",
     * "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *"id_photo": null,
     * "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     * "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     * "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *"passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     * "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     * "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     * "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     * "entry_time": "30",
     *"leave_time": "60",
     *"is_verifed": false,
     *"number_of_working_hours": 0,
     *"nationalitie": {
     *   "name": "Syrian"
     * },
     *"percentage": "0",
     * "basic_salary": 200000,
     * "shifts": [
     *   {
     *      "id": 3,
     *     "start_time": "09:00:00",
     *    "end_time": "15:00:00",
     *   "start_break_hour": "12:30:00",
     *   "end_break_hour": "13:00:00"
     *},
     *{
     *   "id": 4,
     *   "start_time": "16:00:00",
     *  "end_time": "22:00:00",
     *  "start_break_hour": "20:30:00",
     *  "end_break_hour": "21:00:00"
     *}
     * ],
     * "deposits": []
     * "my_alerts": []
     * }
     *}
     */
    public function admin_update_employee(AdminUpdateEmployeeRequest $request)
    {
        $createdData =  $this->adminService->admin_update_employee($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 422);
        }
    }
    /**
     * Update Vacation Hours for Employee
     *
     * This endpoint allows an admin or HR to update the vacation hours for an employee in the company.
     *
     * @bodyParam user_id int required The ID of the user in the Users table.
     * @bodyParam hourly_annual int The number of hourly vacation hours per year.
     * @bodyParam daily_annual int The number of daily vacation hours per year.
     *
     * @response 200 scenario="Determine Working Hours for Employee"{
     *     "id": 2,
     *     "user_id": 8,
     *     "hourly_annual": 30,
     *     "daily_annual": 80
     * }
     */
    public function update_working_hours(DetermineWorkingHoursRequest $request)
    {
        $createdData =  $this->adminService->update_working_hours($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeAvailableTimeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 402);
        }
    }
    /**
     * Update Employee Salary for Employee
     *
     * This endpoint allows an admin or HR to update the salary of an employee in the company.
     *
     * @bodyParam user_id int required The ID of the user in the Users table.
     * @bodyParam new_salary number required The new salary for the employee. Example: 75000.
     *
     * @response 200 scenario="Update Employee Salary"{
     *     "data": {
     *         "id": 6,
     *         "name": "Firas Jabi",
     *         "email": "firassaljabi1232@gmail.com",
     *         "work_email": "firassaljabi1237@goma.com",
     *         "status": 1,
     *         "type": 2,
     *         "gender": 1,
     *         "mobile": "0969040342",
     *         "phone": "0935463111",
     *         "departement": null,
     *         "address": "Damascus",
     *         "position": null,
     *         "skills": null,
     *         "serial_number": "00011",
     *         "birthday_date": "1998-11-26",
     *         "marital_status": null,
     *         "guarantor": null,
     *         "branch": "syria branch",
     *         "start_job_contract": null,
     *         "end_job_contract": null,
     *         "end_visa": null,
     *         "end_passport": null,
     *         "end_employee_sponsorship": null,
     *         "end_municipal_card": null,
     *         "end_health_insurance": null,
     *         "end_employee_residence": null,
     *         "image": null,
     *         "id_photo": null,
     *         "biography": null,
     *         "employee_sponsorship": null,
     *         "visa": null,
     *         "passport": null,
     *         "municipal_card": null,
     *         "health_insurance": null,
     *         "employee_residence": null,
     *         "entry_time": null,
     *         "leave_time": null,
     *         "basic_salary": 750000,
     *         "is_verifed": false,
     *         "percentage": "0"
     *     }
     * }
     */
    public function update_salary(UpdateSalaryRequest $request)
    {
        $createdData =  $this->adminService->update_salary($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Renewal Employee Contract
     *
     * This endpoint is used to  Renewal Employee Contract in the Company and Admin Or Hr Can Access To This Api.
     *
     * @bodyParam user_id int required Must Be Exists In Users Table
     * @bodyParam new_date date Must Be Required if number_of_month equal null Custom Example: 2025-05-01.
     *
     * @bodyParam number_of_month int Must Be Required if new_date equal null, Must be one of the following values:
     * - `3`: Three Month.
     * - `6`: Six Month.
     * - `12`: One Year. Custom Example: 12
     *
     * @response 200 scenario=" Renewal Employee Contract"{
     *  "data": {
     *      "id": 1,
     *      "Start Employee Contract Date": "2023-08-01",
     *      "End Employee Contract Date": "2025-05-01",
     *      "Contract Termination Date": null,
     *      "Contract Termination Period": null,
     *      "Contract Termination Reason": null,
     *      "user": {
     *            "id": 3,
     *            "name": "mouaz alkhateeb"
     *            }
     *     }
     */
    public function renewal_employment_contract(UpdateEmployeeContractRequest $request)
    {
        $createdData =  $this->adminService->renewal_employment_contract($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ContractResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Cancle Employee Contract
     *
     * This endpoint is used to terminate an employee's contract in the company. Only admins or HR personnel can access this API.
     *
     * @bodyParam user_id int required The ID of the user whose contract is being terminated. Must exist in the Users table.
     *
     * @bodyParam terminate_type int required The type of termination. Must be one of the following values:
     * - `1`: Temporary
     * - `2`: Permanent Custom Example: 2
     *
     * @bodyParam contract_termination_period int The period of the contract termination. Required if terminate_type is 1. Must be one of the following values:
     * - `0`: Open term
     * - `3`: Three months
     * - `6`: Six months
     * - `12`: One year
     *
     * @bodyParam contract_termination_reason string The reason for the contract cancle. Required if terminate_type is 1.
     *
     * @response 200 scenario="Cancle Employee Contract"{
     *   "data": {
     *     "id": 4,
     *     "Start Employee Contract Date": "2023-09-05",
     *     "End Employee Contract Date": "2024-01-09",
     *     "Contract Termination Date": "2023-08-27",
     *     "Contract Termination Period": "3 months, 0 days",
     *     "Contract Termination Reason": "Without Reason",
     *     "user": {
     *       "id": 8,
     *       "name": "mouaz alkhateeb",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *       "position": null
     *     }
     *   }
     * OR This Response If terminate_type = 2
     * "data": {
     *     "id": 4,
     *     "Start Employee Contract Date": "2023-09-05",
     *     "End Employee Contract Date": "2024-01-09",
     *     "Contract Termination Date": "2023-08-27",
     *     "Contract Termination Period": "Dismissed",
     *     "Contract Termination Reason": "Dismissed",
     *     "user": {
     *       "id": 8,
     *       "name": "mouaz alkhateeb",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *       "position": null
     *     }
     *   }
     * }
     *
     */
    public function cancle_employees_contract(TerminateContractRequest $request)
    {
        $createdData =  $this->adminService->cancle_employees_contract($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = ContractResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Employee check in System Daily
     *
     * This endpoint is used to check in System in the Company and Employee Can Access To This Api.
     *
     * @bodyParam check_in int required Must Be Value 1 Custom Example: 1

     * @response 200 scenario="Employee check in System Daily"{
     *    "data": {
     *        "id": 2,
     *        "Date": "2023-08-27",
     *        "login_time": "14:03:19",
     *        "logout_time": null,
     *        "user": {
     *              "id": 9,
     *              "name": "ahmad alkhateeb"
     *                }
     *        }
     * }
     */
    public function check_in_attendance(CheckInAttendanceRequest $request)
    {
        $createdData =  $this->adminService->check_in_attendance($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = AttendanceResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 400);
        }
    }

    /**
     * Employee Check Out in System Daily
     *
     * This endpoint is used to check Out From System in the Company and Employee Can Access To This Api.
     *
     * @bodyParam check_out    int required Must Be Value 1 Custom Example: 1

     * @response 200 scenario="Employee check Out System Daily"{
     *    "data": {
     *        "id": 2,
     *        "Date": "2023-08-27",
     *        "login_time": "14:03:19",
     *        "logout_time": null,
     *        "user": {
     *        "id": 9,
     *        "name": "ahmad alkhateeb"
     *             }
     *        }
     * }
     */

    public function check_out_attendance(CheckOutAttendanceRequest $request)
    {
        $createdData =  $this->adminService->check_out_attendance($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = AttendanceResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $createdData['message']], 400);
        }
    }

    /**
     * Add Reward and Adversaries for Employee
     *
     * This endpoint is used to add reward and adversaries allowance for an employee in the company. Only admins or HR personnel can access this API.
     *
     * @bodyParam user_id int required The user ID, which must exist in the Users table.
     *
     * @bodyParam rewards_type int optional The type of rewards for the user. Must be one of the following values:
     * - `1`: Number.
     * - `2`: Rate. (Custom Example: 1)
     *
     * @bodyParam rewards double required if rewards_type is not null. The amount of rewards. (Custom Example: 25000, Custom Example: 5)
     *
     * @bodyParam adversaries_type int optional The type of adversaries for the user. Must be one of the following values:
     * - `1`: Number.
     * - `2`: Rate. (Custom Example: 1)
     *
     * @bodyParam adversaries double required if adversaries_type is not null. The amount of adversaries. (Custom Example: 78000, Custom Example: 3)
     *
     *
     * @response 200 scenario="Add Reward and Adversaries for Employee"{
     *     "Net_Salary": 220000,
     *     "Rewards": 50000,
     *     "rewards_type": 1,
     *     "Adversaries": 95000,
     *     "adversaries_type": 1,
     *     "Housing allowance": 0,
     *     "Transportation Allowance": 0,
     *     "date": "2023-08-27",
     *     "user": {
     *         "id": 8,
     *         "name": "mouaz alkhateeb"
     *     }
     * }
     */

    public function reward_adversaries_salary(RewerdsAdversriesSalaryRequest $request)
    {
        $createdData =  $this->adminService->reward_adversaries_salary($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = SalaryResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }
    /**
     * Archive Employee From the Company
     *
     * This endpoint is used to Archive Employee From the Company and Admin Or Hr Can Access To This Api.
     *
     *@urlParam id int required Must Be Exists In Users Table
     * @response 200 scenario="Delete a Employee"{
     *
     * }
     */
    public function destroyEmployee($id)
    {
        $deletionResult = $this->adminService->deleteEmployee($id);

        if ($deletionResult) {
            return $deletionResult;
        } else {
            return response()->json(['message' => 'Error Deleting Employee,Please Try Again'], 500);
        }
    }



    /**
     * Restore Employee
     *
     * This endpoint is used to Restore a Employee. Only admins and hrs can access this API.
     *
     * @urlParam id int required Must Be Exists In Users Table

     * @response 200 {
     *      "message": "Employee Restored Successfully"
     * }
     *
     */
    public function restore_employee($id)
    {
        $deletionResult = $this->adminService->restore_employee($id);

        if ($deletionResult) {
            return $deletionResult;
        } else {
            return response()->json(['message' => 'Error Deleting Employee,Please Try Again'], 500);
        }
    }

    /**
     * Get dashboard statistics in the company.
     *
     * This endpoint is used to retrieve the company dashboard statistics, which can be accessed by admin or HR users.
     *
     * @response 200 scenario="Dashboard Data"{
     *   "all_employees_count": "1",
     *   "attendance_rate": "0",
     *   "active_employees_count": "1",
     *   "on_vacation_employees_count": "0",
     *   "active_employees_percentage": "100",
     *   "on_vacation_employees_percentage": "0",
     *   "male_employees_percentage": "0",
     *   "female_employees_percentage": "0",
     *   "nationalities_rate": {
     *     "most_nationalities": [
     *       {
     *         "nationality_id": 2,
     *         "nationality_name": "Syrian",
     *         "percent": 40
     *       },
     *       {
     *         "nationality_id": 16,
     *         "nationality_name": "Kuwaiti",
     *         "percent": 20
     *       },
     *       {
     *         "nationality_id": 38,
     *         "nationality_name": "Ukrainian",
     *         "percent": 20
     *       }
     *     ],
     *     "others": 20
     *   },
     *  },
     *   "contract_expiration_percentage": "0",
     *   "contract_expiration": [],
     *   "expired_passports_percentage": "100",
     *   "expired_passports": [
     *      {
     *         "id": 8,
     *         "name": "mouaz alkhateeb",
     *         "end_passport": "2023-09-11"
     *      }
     *   ]
     * }
     */
    public function getDashboardData()
    {
        $data = $this->adminService->getDashboardData();
        $returnData = DashboardDataResource::make($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
    /**
     * Get Employees List in the Company
     *
     * This endpoint is used to retrieve a list of employees in the company. Only admins or HR personnel can access this API.
     *
     * @queryParam name string Filter employees by name. Example: mouaz
     *
     * @response 200 scenario="Employees List"{
     *   "data": [
     *     {
     *       "id": 8,
     *       "name": "mouaz alkhateeb",
     *       "email": "mouaz@gmail.com",
     *       "work_email": "mouazalkhateeb@gmail.com",
     *       "status": 1,
     *       "type": 4,
     *       "gender": 1,
     *       "mobile": "0969040322",
     *       "phone": "0969040322",
     *       "department": "it",
     *       "address": "Damascus",
     *       "position": null,
     *       "skills": "no skills",
     *       "serial_number": "000007",
     *       "birthday_date": "2022-11-26",
     *       "marital_status": null,
     *       "guarantor": "admin",
     *       "branch": "syria branch",
     *       "start_job_contract": "2023-08-01",
     *       "end_job_contract": "2023-10-01",
     *       "end_visa": "2023-09-11",
     *       "end_passport": "2023-09-11",
     *       "end_employee_sponsorship": null,
     *       "end_municipal_card": "2023-09-10",
     *       "end_health_insurance": "2023-09-14",
     *       "end_employee_residence": "2023-09-20",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *       "id_photo": null,
     *       "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *       "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *       "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *       "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *       "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *       "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *       "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *       "entry_time": 30,
     *       "leave_time": 60,
     *       "percentage": "0",
     *       "basic_salary": 200000,
     *       "is_verified": false,
     *       "number_of_working_hours": 0
     *     },
     *{
     *     {
     *       "id": 9,
     *       "name": "ahmad alkhateeb",
     *       "email": "ahmad@gmail.com",
     *       "work_email": "ahmadlkhateeb@gmail.com",
     *       "status": 1,
     *       "type": 4,
     *       "gender": 1,
     *       "mobile": "0969040355",
     *       "phone": "0969040344",
     *       "department": "it",
     *       "address": "Damascus",
     *       "position": "laravel developer",
     *       "skills": "no skills",
     *       "serial_number": "000011",
     *       "birthday_date": "2022-11-26",
     *       "marital_status": null,
     *       "guarantor": "User",
     *       "branch": "syria branch",
     *       "start_job_contract": "2023-08-01",
     *       "end_job_contract": "2023-10-01",
     *       "end_visa": "2023-09-11",
     *       "end_passport": "2023-09-11",
     *       "end_employee_sponsorship": null,
     *       "end_municipal_card": "2023-09-10",
     *       "end_health_insurance": "2023-09-14",
     *       "end_employee_residence": "2023-09-20",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *       "id_photo": null,
     *       "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *       "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *       "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *       "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *       "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *       "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *       "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *       "entry_time": 30,
     *       "leave_time": 60,
     *       "percentage": "0",
     *       "basic_salary": 200000,
     *       "is_verified": false,
     *       "number_of_working_hours": 0
     *     }
     *   ]
     * }
     */
    public function getEmployeesList(GetEmployeesListRequest $request)
    {
        $data = $this->adminService->getEmployees($request->generateFilter());
        $returnData = EmployeeResource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }

    /**
     * Get Dismissed Employees List in the Company
     *
     * This endpoint is used to retrieve a list of dismissed employees in the company. Only admins or HR personnel can access this API.
     *
     * @queryParam name string Filter employees by name. Custom Example: mouaz
     *
     * @response 200 scenario="Dismissed Employees List"{
     *   "data": [
     *     {
     *       "id": 8,
     *       "name": "mouaz alkhateeb",
     *       "email": "mouaz@gmail.com",
     *       "work_email": "mouazalkhateeb@gmail.com",
     *       "status": 4,
     *       "type": 4,
     *       "gender": 1,
     *       "mobile": "0969040322",
     *       "phone": "0969040322",
     *       "department": "it",
     *       "address": "Damascus",
     *       "position": null,
     *       "skills": "no skills",
     *       "serial_number": "000007",
     *       "birthday_date": "2022-11-26",
     *       "marital_status": null,
     *       "guarantor": "admin",
     *       "branch": "syria branch",
     *       "start_job_contract": "2023-08-01",
     *       "end_job_contract": "2023-10-01",
     *       "end_visa": "2023-09-11",
     *       "end_passport": "2023-09-11",
     *       "end_employee_sponsorship": null,
     *       "end_municipal_card": "2023-09-10",
     *       "end_health_insurance": "2023-09-14",
     *       "end_employee_residence": "2023-09-20",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *       "id_photo": null,
     *       "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *       "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *       "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *       "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *       "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *       "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *       "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *       "entry_time": 30,
     *       "leave_time": 60,
     *       "percentage": "0",
     *       "basic_salary": 200000,
     *       *"is_verifed": false,
     *       "number_of_working_hours": 0
     *     },
     *     {
     *       "id": 9,
     *       "name": "ahmad alkhateeb",
     *       "email": "ahmad@gmail.com",
     *       "work_email": "ahmadlkhateeb@gmail.com",
     *       "status": 4,
     *       "type": 4,
     *       "gender": 1,
     *       "mobile": "0969040355",
     *       "phone": "0969040344",
     *       "department": "it",
     *       "address": "Damascus",
     *       "position": "laravel developer",
     *       "skills": "no skills",
     *       "serial_number": "000011",
     *       "birthday_date": "2022-11-26",
     *       "marital_status": null,
     *       "guarantor": "User",
     *       "branch": "syria branch",
     *       "start_job_contract": "2023-08-01",
     *       "end_job_contract": "2023-10-01",
     *       "end_visa": "2023-09-11",
     *       "end_passport": "2023-09-11",
     *       "end_employee_sponsorship": null,
     *       "end_municipal_card": "2023-09-10",
     *       "end_health_insurance": "2023-09-14",
     *       "end_employee_residence": "2023-09-20",
     *       "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *       "id_photo": null,
     *       "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *       "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *       "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *       "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *       "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *       "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *       "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *       "entry_time": 30,
     *       "leave_time": 60,
     *       "percentage": "0",
     *       "basic_salary": 200000,
     *       "is_verifed": false,
     *       "number_of_working_hours": 0,
     *     }
     *    ]
     *  }
     */
    public function getEmployeesDismissedList(GetEmployeesListRequest $request)
    {
        $data = $this->adminService->getEmployeesDismissedList($request->generateFilter());
        $returnData = EmployeeResource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }

    /**
     * Show Salaries in the Company
     *
     * This endpoint is used to retrieve a list of salaries in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 scenario="Show Salaries"{
     *     "data": [
     *         {
     *             "Net_Salary": 220000,
     *             "Rewards": 10,
     *             "Adversaries": 0,
     *             "Housing allowance": 0,
     *             "Transportation Allowance": 0,
     *             "date": "2023-08-27",
     *             "user": {
     *                 "id": 8,
     *                 "name": "mouaz alkhateeb"
     *             }
     *         },
     *         {
     *             "Net_Salary": 600000,
     *             "Rewards": 10,
     *             "Adversaries": 0,
     *             "Housing allowance": 0,
     *             "Transportation Allowance": 0,
     *             "date": "2023-08-27",
     *             "user": {
     *                 "id": 9,
     *                 "name": "ahmad alkhateeb"
     *             }
     *         }
     *     ]
     * }
     */
    public function employees_salaries(GetEmployeesSalariesListRequest $request)
    {
        $data = $this->adminService->employees_salaries($request->generateFilter());

        if ($data['success']) {
            $newData = $data['data'];
            $returnData = SalaryResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
    /**
     * Show Employees Attendance in the Company
     *
     * This endpoint is used to retrieve the attendance records of employees in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 scenario="Employees Attendance"{
     *     "data": [
     *         {
     *             "id": 1,
     *             "Date": "2023-08-27",
     *             "login_time": "14:01:54",
     *             "logout_time": "14:02:07",
     *             "user": {
     *                 "id": 8,
     *                 "name": "mouaz alkhateeb"
     *             }
     *         },
     *         {
     *             "id": 2,
     *             "Date": "2023-08-27",
     *             "login_time": "14:03:19",
     *             "logout_time": "14:03:27",
     *             "user": {
     *                 "id": 9,
     *                 "name": "ahmad alkhateeb"
     *             }
     *         }
     *     ]
     * }
     */
    public function employees_attendances(GetEmployeesAttendancesListRequest $request)
    {
        $data = $this->adminService->employees_attendances($request->generateFilter());

        if ($data['success']) {
            $newData = $data['data'];
            $returnData = AttendanceResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }

    /**
     * Get Contract Expiration in the Company
     *
     * This endpoint is used to retrieve the contract expiration details for employees in the company. Only admins or HR personnel can access this API.
     *
     * @response 200 scenario="Get Contract Expiration"{
     *     "data": [
     *         {
     *             "id": 4,
     *             "Start Employee Contract Date": "2023-09-05",
     *             "End Employee Contract Date": "2023-09-27",
     *             "Contract Termination Date": null,
     *             "Contract Termination Period": null,
     *             "Contract Termination Reason": null,
     *             "user": {
     *                 "id": 8,
     *                 "name": "mouaz alkhateeb"
     *             }
     *         }
     *     ]
     * }
     */
    public function get_contract_expiration(GetContractExpirationList $request)
    {
        $data = $this->adminService->get_contract_expiration($request->generateFilter());
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = ContractResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }

    /**
     * Get Employee
     *
     * This endpoint is used to retrieve employee details in the company. Only admins or HR personnel can access this API.
     *
     * @urlParam id int required Must Be Exists In Users Table
     * @response 201 scenario="Show Employee"{
     *     "data": {
     *         "id": 4,
     *         "name": "mouaz alkhateeb",
     *         "email": "mouaz@gmail.com",
     *         "work_email": "mouazalkhateeb@gmail.com",
     *         "status": "1",
     *         "type": 4,
     *         "gender": "1",
     *         "mobile": "0969040322",
     *         "phone": "0969040322",
     *         "departement": "it",
     *         "address": "Damascus",
     *         "position": null,
     *         "skills": "no skills",
     *         "serial_number": "000007",
     *         "birthday_date": "2022-11-26",
     *         "marital_status": null,
     *         "guarantor": "admin",
     *         "branch": "syria branch",
     *         "start_job_contract": "2023-08-01",
     *         "end_job_contract": "2023-10-01",
     *         "end_visa": "2023-09-11",
     *         "end_passport": "2023-09-11",
     *         "end_employee_sponsorship": null,
     *         "end_municipal_card": "2023-09-10",
     *         "end_health_insurance": "2023-09-14",
     *         "end_employee_residence": "2023-09-20",
     *         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *         "id_photo": null,
     *         "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *         "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *         "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *         "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *         "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *         "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *         "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *         "entry_time": 30,
     *         "leave_time": 60,
     *         "number_of_working_hours": 0,
     *         "nationalitie": {
     *             "name": "Syrian"
     *         },
     *         "percentage": "0",
     *         "basic_salary": 200000,
     *         "is_verifed": false,
     *         "shifts": [
     *             {
     *                 "id": 3,
     *                 "start_time": "09:00:00",
     *                 "end_time": "15:00:00",
     *                 "start_break_hour": "12:30:
     *                 "end_break_hour": "13:00:00"
     *             },
     *             {
     *                 "id": 4,
     *                 "start_time": "16:00:00",
     *                 "end_time": "22:00:00",
     *                 "start_break_hour": "20:30:00",
     *                 "end_break_hour": "21:00:00"
     *             }
     *         ],
     *        "deposits": [
     *        {
     *            "id": 1,
     *            "type": 2,
     *            "status": 1,
     *            "extra_status": null,
     *            "laptop_type": "asus",
     *            "Serial_laptop_number": "2010",
     *            "laptop_color": "blue",
     *            "laptop_image": "http://127.0.0.1:8000/employees_deposits/2023-08-27-EmployeeDeposit-844.png",
     *            "reason_reject": null,
     *            "reason_clearance_reject": null
     *        }
     *       ]
     *    }
     *  }
     */
    public function getEmployee($id)
    {
        $employeeData = $this->adminService->showEmployee($id);
        if ($employeeData['success']) {
            $newData = $employeeData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return response()->json(['message' => $employeeData['message']], 401);
        }
    }


    /**
     * Show My Shifts List
     *
     * This endpoint is used to retrieve a list of shifts specific to the authenticated employee. Only authenticated employees can access this API.
     *
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "start_time": "09:00:00",
     *             "end_time": "15:00:00",
     *             "start_break_hour": "12:30:00",
     *             "end_break_hour": "13:00:00"
     *         },
     *         {
     *             "id": 2,
     *             "start_time": "16:00:00",
     *             "end_time": "22:00:00",
     *             "start_break_hour": "20:30:00",
     *             "end_break_hour": "21:00:00"
     *         }
     *     ]
     * }
     */
    public function my_shifts()
    {
        $data = $this->adminService->my_shifts();
        if ($data['success']) {
            $newData = $data['data'];
            $returnData = ShiftRersource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }


    /**
     * Get Remaining Vacation Hours for an Employee
     *
     * This endpoint is used to retrieve the remaining vacation hours for an employee in a company. Only admins or HR personnel can access this API.
     *
     * @urlParam id int required The ID of the employee in the Users table.
     *
     * @response 201 {
     *     "data": {
     *         "id": 7,
     *         "user_id": 11,
     *         "hourly_annual": 70,
     *         "daily_annual": 80
     *     }
     * }
     */
    public function remining_vacation_hour_employee($id)
    {
        $data = $this->adminService->remining_vacation_hour_employee($id);
        if ($data['success']) {
            if (isset($data['data'])) {
                $returnData = EmployeeAvailableTimeResource::make($data['data']);
                return ApiResponseHelper::sendResponse(
                    new Result($returnData,  "DONE")
                );
            } else {
                return ApiResponseHelper::sendFailedResponse('Employee Dont Have Paid Vcation Hours!', 404);
            }
        } else {
            return ApiResponseHelper::sendFailedResponse($data['message'], $data['code']);
        }
    }


    /**
     * Show My Profile
     *
     * This endpoint is used to Show Personal Profile and Just Employee Can Access To This Api.
     *
     * @response 200 scenario="Show My Profile"{
     * "data": {
     * "id": 4,
     * "name": "mouaz alkhateeb",
     * "email": "mouaz@gmail.com",
     * "work_email": "mouazalkhateeb@gmail.com",
     * "status": "1",
     * "type": 4,
     * "gender": "1",
     * "mobile": "0969040322",
     * "phone": "0969040322",
     * "departement": "it",
     * "address": "Damascus",
     * "position": null,
     * "skills": "no skills",
     * "serial_number": "000007",
     * "birthday_date": "2022-11-26",
     * "marital_status": null,
     * "guarantor": "admin",
     *"branch": "syria branch",
     * "start_job_contract": "2023-08-01",
     * "end_job_contract": "2023-10-01",
     * "end_visa": "2023-09-11",
     * "end_passport": "2023-09-11",
     * "end_employee_sponsorship": null,
     * "end_municipal_card": "2023-09-10",
     * "end_health_insurance": "2023-09-14",
     * "end_employee_residence": "2023-09-20",
     * "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *"id_photo": null,
     * "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     * "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     * "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *"passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     * "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     * "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     * "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     * "entry_time": 30,
     *"leave_time": 60,
     *"number_of_working_hours": 0,
     *"nationalitie": {
     *   "name": "Syrian"
     * },
     *"percentage": "0",
     * "basic_salary": 200000,
     * "is_verifed": false,
     * "shifts": [
     *   {
     *      "id": 3,
     *     "start_time": "09:00:00",
     *    "end_time": "15:00:00",
     *   "start_break_hour": "12:30:00",
     *   "end_break_hour": "13:00:00"
     *},
     *{
     *   "id": 4,
     *   "start_time": "16:00:00",
     *  "end_time": "22:00:00",
     *  "start_break_hour": "20:30:00",
     *  "end_break_hour": "21:00:00"
     *}
     * ],
     *"deposits": [
     *{
     *"id": 1,
     *"type": 2,
     *"status": 1,
     *"extra_status": null,
     *"laptop_type": "asus",
     *"Serial_laptop_number": "2010",
     *"laptop_color": "blue",
     *"laptop_image": "http://127.0.0.1:8000/employees_deposits/2023-08-27-EmployeeDeposit-844.png",
     *"reason_reject": null,
     *"reason_clearance_reject": null
     *}
     * ]
     * }
     *}
     */


    public function profile()
    {
        $employeeData = $this->adminService->profile();
        $returnData = EmployeeResource::make($employeeData);
        return ApiResponseHelper::sendResponse(
            new Result($returnData,  "DONE")
        );
    }
    /**
     *
     * Show Nationalities List in the Company
     *
     * This endpoint is used to Show Nationalities List in the Company and Admin Or Hr Can Access To This Api.
     *
     * @response 200 scenario="Show Nationalities List"{
     *"data": [
     *{
     *"id": 1,
     *"name": "Saudi Arabian"
     *},
     *{
     *"id": 2,
     *"name": "Syrian"
     *},
     *{
     *"id": 3,
     * "name": "Afghan"
     * },
     * {
     *"id": 4,
     *"name": "Algerian"
     *},
     *{
     *"id": 5,
     *"name": "Argentinian"
     *},
     *{
     * "id": 6,
     *"name": "Bahraini"
     *},
     *{
     *"id": 7,
     *"name": "Bangladeshi"
     *},
     *{
     * "id": 8,
     *"name": "Belarusian"
     * },
     *{
     *"id": 9,
     * "name": "Belgian"
     *},
     *{
     *"id": 10,
     *"name": "Egyptian"
     *},
     *{
     *"id": 11,
     *"name": "Indian"
     *},
     *{
     *"id": 12,
     *"name": "Iraqi"
     *},
     *{
     * "id": 13,
     *"name": "Irish"
     *},
     * {
     * "id": 14,
     *"name": "Italian"
     *},
     *{
     *"id": 15,
     *"name": "Jordanian"
     *},
     *{
     *"id": 16,
     *"name": "Kuwaiti"
     *},
     * {
     *"id": 17,
     *"name": "Libyan"
     *},
     *{
     *"id": 18,
     *"name": "Moroccan"
     *},
     *{
     *"id": 19,
     *"name": "Pakistani"
     *},
     *{
     *"id": 20,
     *"name": "Palestinian"
     *},
     *{
     *"id": 21,
     *"name": "Filipino"
     *},
     *{
     *"id": 22,
     *"name": "Polish"
     *},
     *{
     *"id": 23,
     *"name": "Portuguese"
     *},
     *{
     *"id": 24,
     *"name": "Portuguese"
     *},
     *{
     * "id": 25,
     * "name": "Romanian"
     *},
     *{
     * "id": 26,
     *"name": "Qatari"
     *},
     *{
     *"id": 27,
     *"name": "Russian"
     *},
     * {
     * "id": 28,
     *"name": "Singaporean"
     *},
     *{
     *"id": 29,
     *"name": "Somali"
     *},
     *{
     *"id": 30,
     *"name": "Sudanese"
     *},
     * {
     * "id": 31,
     *"name": "Swedish"
     *},
     *{
     *"id": 32,
     *"name": "Swiss"
     *},
     * {
     * "id": 33,
     *"name": "Taiwanese"
     *},
     *{
     *"id": 34,
     *"name": "Tajikistani"
     *},
     *{
     *"id": 35,
     *"name": "Thai"
     * },
     *{
     *"id": 36,
     *"name": "Tunisian"
     *},
     *{
     * "id": 37,
     *"name": "Turkish"
     *},
     *{
     *"id": 38,
     * "name": "Ukrainian"
     *},
     *{
     *"id": 39,
     *"name": "Emirati"
     *},
     *{
     *"id": 40,
     *"name": "British"
     *},
     *{
     *"id": 41,
     *"name": "American"
     *},
     *{
     *"id": 42,
     * "name": "Uruguayan"
     *},
     *{
     *"id": 43,
     *"name": "Uzbek"
     *},
     *{
     *"id": 44,
     *"name": "Venezuelan"
     *},
     *{
     *"id": 45,
     *"name": "Yemeni"
     *},
     *{
     * "id": 46,
     * "name": "South African"
     *},
     *{
     *"id": 47,
     * "name": "Serbian"
     *},
     *{
     * "id": 48,
     * "name": "Panamanian"
     *},
     *{
     *"id": 49,
     *"name": "Omani"
     *},
     * {
     * "id": 50,
     *"name": "Norwegian"
     * },
     *{
     *"id": 51,
     *"name": "Nigerian"
     *},
     *{
     *"id": 52,
     *"name": "Dutch"
     *},
     *{
     * "id": 53,
     * "name": "Mexican"
     *},
     *{
     * "id": 54,
     *"name": "Mauritanian"
     *},
     *{
     *"id": 55,
     *"name": "Malaysian"
     *},
     *{
     *"id": 56,
     *"name": "Japanese"
     *}
     *]
     * }
     */
    public function list_of_nationalities(GetNationalitiesRequest $request)
    {
        $data = $this->adminService->list_of_nationalities($request->generateFilter());
        $returnData = NationalitiesRrsource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }
    /**
     * Update Employee Permission Time
     *
     * This endpoint is used to update the permission time for an employee. Only admins or HR personnel can access this API.
     *
     * @bodyParam user_id int required The user ID, which must exist in the users table.
     *
     * @bodyParam leave_time number  Must be one of the following values:
     * - 30
     * - 60
     * - 90 (Custom Example: 60)
     *
     * @bodyParam entry_time numberMust be one of the following values:
     * - 30
     * - 60
     * - 90 (Custom Example: 90)
     *
     * @response 200 scenario="Update Employee Permission Time"{
     *     "data": {
     *         "id": 6,
     *         "name": "mouaz alkhateeb",
     *         "email": "mouaz@gmail.com",
     *         "work_email": "mouazalkhateeb@gmail.com",
     *         "status": 1,
     *         "type": 4,
     *         "gender": 1,
     *         "mobile": "0969040322",
     *         "phone": "0969040322",
     *         "departement": "it",
     *         "address": "Damascus",
     *         "position": null,
     *         "skills": "no skills",
     *         "serial_number": "000007",
     *         "birthday_date": "2022-11-26",
     *         "marital_status": null,
     *         "guarantor": "admin",
     *         "branch": "syria branch",
     *         "start_job_contract": "2023-08-01",
     *         "end_job_contract": "2023-10-01",
     *         "end_visa": "2023-09-11",
     *         "end_passport": "2023-09-11",
     *         "end_employee_sponsorship": null,
     *         "end_municipal_card": "2023-09-10",
     *         "end_health_insurance": "2023-09-14",
     *         "end_employee_residence": "2023-09-20",
     *         "image": null,
     *         "id_photo": null,
     *         "biography": null,
     *         "employee_sponsorship": null,
     *         "visa": null,
     *         "passport": null,
     *         "municipal_card": null,
     *         "health_insurance": null,
     *         "employee_residence": null,
     *         "entry_time": 60,
     *         "leave_time": 90,
     *         "percentage": "0",
     *         "basic_salary": 200000,
     *         "is_verifed": false,
     *         "number_of_working_hours": 0,
     *     }
     * }
     */
    public function update_employee_permission_time(UpdateEmployeePermissionRequest $request)
    {
        $createdData =  $this->adminService->update_employee_permission_time($request->validated());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = EmployeeResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            // return ['message' => $createdData['message']];
            return response()->json(['message' => $createdData['message']], 400);
        }
    }
    /**
     * Leave Calendar in the Company
     *
     * This endpoint is used to retrieve the Leave Calendar in the Company. Only Admin or HR can access this API.
     *
     * @queryParam date string Filter Leave Calendar by a specific date. It will return records where the date is equal to the specified date or it exists between the start_date and end_date. Example: 2023-06-03
     *
     * @queryParam day int Filter Leave Calendar by day. Use the value "1" to return records where the date is equal to the current date or it exists between the start_date and end_date. Use the value "2" to return records where the date is equal to tomorrow's date or it exists between the start_date and end_date. Example for day 1: 2023-08-30, Example for day 2: 2023-08-31
     *
     * @response 200 scenario="Leave Calendar"{
     *     "data": [
     *         {
     *             "id": 2,
     *             "date": "2023-08-31",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *                 "position": "Backend Developer"
     *             }
     *         },
     *         {
     *             "id": 5,
     *             "start_date": "2023-08-29",
     *             "end_date": "2023-08-31",
     *             "user": {
     *                 "id": 3,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-29-Employee-8.jpg",
     *                 "position": "Backend Developer"
     *             }
     *         }
     *     ]
     * }
     */
    public function leave_calendar(GetLeaveCalendarListRequest $request)
    {
        $data = $this->adminService->leave_calendar($request->generateFilter());

        if ($data['success']) {
            $newData = $data['data'];
            $returnData = LeaveCalendarListResource::collection($newData);
            return ApiResponseHelper::sendResponse(
                new Result($returnData, "DONE")
            );
        } else {
            return ['message' => $data['message']];
        }
    }
    /**
     * Attendance Overview in the Company
     *
     * This endpoint is used to Display the Attendance Overview in the Company. Only Admin or HR can access this API.
     *
     * @queryParam year string Filter Attendance Overview by year. Use the value "2023" return records where the year is equal the Year From Filter. Custom Example: 2023
     *
     * @response 200 scenario="Attendance Overview"{
     *     "data": [
     *         {
     *             "id": 3,
     *             "rate_of_absents": 42,
     *             "rate_of_committed_employees": 39,
     *             "rate_of_un_committed_employees": 61,
     *             "month": "08",
     *             "year": "2023"
     *         },
     *     ]
     * }
     */

    public function attendance_overview(AttendancsOverviewListRequest $request)
    {
        $createdData =  $this->adminService->attendance_overview($request->generateFilter());
        if ($createdData['success']) {
            $newData = $createdData['data'];
            $returnData = PercentageResource::collection($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $createdData['message']];
        }
    }

    /**
     * Check Employee Location
     *
     * This endpoint is used to Check Employee Location in the Company,If Employee Out Of Company Will Push Notification To this Employee And Checkout From Comapny.
     * @bodyParam user_id int required Must Be Exists In Users Table
     * @bodyParam longitude number required The longitude of the company location. Custom  Example: 25.12
     * @bodyParam latitude number required The latitude of the company location. Custom Example: 15.32
     *
     */


    public function check_location(CheckLocationRequest $request)
    {
        $createdData =  $this->adminService->check_location($request->validated());

        return $createdData;
    }


    /**
     * Check Employee Address
     *
     * This endpoint is used to Check Employee Mac Address in the Company,If Employee Out Of Company Will Push Notification To this Employee And Checkout From Comapny.
     * @bodyParam user_id int required Must Be Exists In Users Table
     * @bodyParam mac_address array An array of Mac Addresses for the employee.
     *
     */
    public function check_address(CheckAddessRequest $request)
    {
        $createdData =  $this->adminService->check_address($request->validated());

        return $createdData;
    }
}
