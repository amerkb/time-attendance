<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\UpdateEmailRequest;
use App\Http\Requests\Employees\VerifyCodeRequest;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\Company;
use App\Models\User;;

use App\Services\Admin\AdminService;
use App\Services\Email\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Torann\GeoIP\Facades\GeoIP;
use Stevebauman\Location\Facades\Location;

// use GeoIP;


/**
 * @group Authentication
 *
 * APIs for managing Authentication
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verify_code', 'generate_code']]);
    }
    /**
     * Login
     *
     * This endpoint is used to login a user to the system.
     *
     * @bodyParam email string required Example: mouaz@gmail.com
     * @bodyParam password string required Example: 0123456789
     *
     *
     * @response  {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjkzMDM2MzM5LCJleHAiOjE2OTMwMzk5MzksIm5iZiI6MTY5MzAzNjMzOSwianRpIjoic0JtVWZMcVdiTjNBeVVQUCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.LxUrVJ_gdDor8mju1o5Db43RM1c0yLlEYDpVV0RwdH8",
     * "user": {
     * "id": 2,
     * "name": "Firas Jabi",
     * "email": "firassaljabi1232@gmail.com",
     * "work_email": "firassaljabi1237@goma.com",
     * "email_verified_at": null,
     * "mobile": "0969040342",
     * "phone": "0935463111",
     * "serial_number": "00011",
     * "nationalitie_id": 2,
     * "company_id": 1,
     * "birthday_date": "1998-11-26",
     * "material_status": 2,
     * "gender": 1,
     * "address": "Damascus",
     * "guarantor": null,
     * "branch": "syria branch",
     * "departement": null,
     * "position": null,
     * "type": 2,
     * "status": 1,
     * "skills": null,
     * "start_job_contract": null,
     * "end_job_contract": null,
     * "image": null,
     * "id_photo": null,
     * "biography": null,
     * "employee_sponsorship": null,
     * "end_employee_sponsorship": null,
     * "employee_residence": null,
     * "end_employee_residence": null,
     * "visa": null,
     * "end_visa": null,
     * "passport": null,
     * "end_passport": null,
     * "municipal_card": null,
     * "end_municipal_card": null,
     * "health_insurance": null,
     * "end_health_insurance": null,
     * "basic_salary": "0.00",
     * "permission_to_entry": 0,
     * "entry_time": null,
     * "permission_to_leave": 0,
     * "leave_time": null,
     * "number_of_working_hours": 0,
     * "code": null,
     * "is_verifed": false,
     * "expired_at": null,
     * "created_at": "2023-08-26T07:01:20.000000Z",
     * "updated_at": "2023-08-26T07:01:20.000000Z",
     * "deleted_at": null
     * }
     * }
     *
     * @response 401 scenario="Failed Login"{
     * "message": "Invalid login credentials"
     * }
     *
     */

    public function login(Request $request)
    {
        try {
            if ($request->query()) {
                return response()->json(['message' => 'Query parameters are not allowed'], 400);
            } else {
                $user = User::where('email', $request->email)->first();

                $rules = [
                    "email" => "required",
                    "password" => "required",
                    'device_key' => 'nullable'
                ];

                $validator = Validator::make($request->only(['email', 'password']), $rules);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                $credentials = $request->only(['email', 'password']);

                $token = Auth::guard('api')->attempt($credentials);

                if (!$token) {
                    return response()->json(['error' => 'Invalid login credentials'], 403);
                }

                $user = Auth::guard('api')->user();

                if (isset($request->device_key)) {
                    $user->update([
                        'device_key' => $request->device_key
                    ]);
                }

                if ($user->code != null && $user->is_verifed == false) {
                    return response()->json(['message' => 'Please Verfied Your Account'], 422);
                }

                return response()->json(['token' => $token, 'user' => EmployeeResource::make($user)]);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Verfiy Code
     *
     * This endpoint is used to Verfiy Code for user in the system.
     * @bodyParam code number required Should Check Your Email Inbox Custom Example: 2587
     * @response {
     * "message": "Success Verfiy Code..!!"
     * }
     *
     */
    public function verify_code(VerifyCodeRequest $request)
    {

        $user = User::where('email', $request->email)->first();
        $newUserEmail = User::where('temporary_email', $request->email)->first();
        if ($user && $request->code == $user->code && $user->email == $request->email) {
            $user->reset_code();
            $user->update([
                'is_verifed' => true
            ]);
            return response()->json(['message' => 'Success Verfiy Code..!!']);
        } elseif ($newUserEmail && $request->code == $newUserEmail->code && $newUserEmail->temporary_email == $request->email) {
            $newUserEmail->reset_code();
            $newUserEmail->update([
                'email' => $newUserEmail->temporary_email,
                'is_verifed' => true,
                'temporary_email' => null
            ]);
            return response()->json(['message' => 'Success Verfiy Code..!!']);
        } else {
            return response()->json(['message' => 'The Code You Entered is Incorrect..!!'], 400);
        }



        if ($request->code == $user->code && $user->email == $request->email) {
            $user->reset_code();
            $user->update([
                'is_verifed' => true
            ]);
            return response()->json(['message' => 'Success Verfiy Code..!!']);
        } else {
            return response()->json(['message' => 'The Code You Entered is Incorrect..!!'], 400);
        }
    }
    /**
     * Generate Code
     *
     * This endpoint is used to Generate Code for user in the system.
     * @bodyParam email email required Should Check Your Email Inbox Custom Example: 0011
     * @response {
     * "message": "Code Generated Successfully"
     * }
     *
     */
    public function generate_code(UpdateEmailRequest $request)
    {
        DB::beginTransaction();
        $user = User::where('email', $request['email'])->first();
        try {
            $user->update([
                'is_verifed' => false,
            ]);
            $user->generate_code();
            $userCode = $user->code;
            $userEmail = $user->email;
            $userPassword = $user->reset_password;
            $recipientEmail = $userEmail;
            $company = Company::where('id', $user->company_id)->first();
            $companyName = $company->name;
            EmailService::sendHtmlEmail($recipientEmail, $companyName, $userCode, $userEmail, $userPassword);

            DB::commit();

            return response()->json(['message' => "Code Generated Successfully"], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage(), $e->getCode()]);
        }
    }

    /**
     * Logout
     *
     * This endpoint is used to log out a user from the system.
     * @authenticated
     * @response {
     * "message": "User successfully signed out"
     * }
     *
     */
    public function logout()
    {
        $user = auth()->user();
        if ($user->device_key != null) {
            $user->update([
                'device_key' => null
            ]);
        }

        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}
