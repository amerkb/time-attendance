<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\Result;
use App\Events\MessageCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\Conversation;
use App\Models\Recipient;
use App\Models\User;
use App\Statuses\UserTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;



/**
 * @group Chats
 * @authenticated
 * APIs for managing Chats
 */
class ConversationContoller extends Controller
{
    /**
     * Get HRs List in the Company
     *
     * This endpoint retrieves a list of HRs (Human Resources) in the company.
     *
     * @response 200 scenario="HRs List"{
     *   "success": true,
     *   "error_code": null,
     *   "message": "Messages.TaskCompleteSuccessfully",
     *   "data": [
     *     {
     *       "id": 10,
     *       "name": "hamza fawaz",
     *       "email": "hamzaFawaz@gmail.com",
     *       "work_email": "hamzafawaz121@gmail.com",
     *       "status": 1,
     *       "type": 3,
     *       "gender": 1,
     *       "mobile": "0969010722",
     *       "phone": "0935461177",
     *       "department": "it",
     *       "address": "Damascus",
     *       "position": null,
     *       "skills": "no skills",
     *       "serial_number": "000077",
     *       "birthday_date": "1998-11-26",
     *       "material_status": 3,
     *       "guarantor": "admin",
     *       "branch": "syria branch",
     *       "start_job_contract": "2023-06-01",
     *       "end_job_contract": "2023-09-01",
     *       "end_visa": null,
     *       "end_passport": null,
     *       "end_employee_sponsorship": null,
     *       "end_municipal_card": null,
     *       "end_health_insurance": null,
     *       "end_employee_residence": null,
     *       "image": null,
     *       "id_photo": null,
     *       "biography": null,
     *       "employee_sponsorship": null,
     *       "visa": null,
     *       "passport": null,
     *       "municipal_card": null,
     *       "health_insurance": null,
     *       "employee_residence": null,
     *       "entry_time": 0,
     *       "leave_time": 0,
     *       "is_verified": false,
     *       "number_of_working_hours": 0,
     *       "percentage": "0",
     *       "basic_salary": 200000
     *     }
     *   ]
     * }
     */
    public function getHrsList()
    {
        $data = User::where('type', UserTypes::HR)->where('company_id', auth()->user()->company_id)->get();

        $returnData = EmployeeResource::collection($data);
        return ApiResponseHelper::sendResponse(
            new Result($returnData, "DONE")
        );
    }


    /**
     * Show My Conversations List
     *
     * This endpoint retrieves a list of conversations that are specific to the authenticated employee. It requires authentication to access.
     *
     * @response 200 scenario="Show My Conversations"
     * [
     *     {
     *         "id": 1,
     *         "user_id": 2,
     *         "lable": null,
     *         "type": "peer",
     *         "created_at": "2023-09-11T15:40:35.000000Z",
     *         "updated_at": "2023-09-11T15:43:04.000000Z",
     *         "last_message_id": 2,
     *         "pivot": {
     *             "user_id": 2,
     *             "conversation_id": 1,
     *             "joined_at": "2023-09-11 18:40:35"
     *         },
     *         "last_message": {
     *             "id": 2,
     *             "conversation_id": 1,
     *             "user_id": 9,
     *             "body": "Hi Firass",
     *             "type": "text",
     *             "created_at": "2023-09-11T15:43:04.000000Z",
     *             "updated_at": "2023-09-11T15:43:04.000000Z",
     *             "deleted_at": null
     *         },
     *         "participants": [
     *             {
     *                 "id": 9,
     *                 "name": "mouaz alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateebbb@gmail.com",
     *                 "email_verified_at": null,
     *                 "mobile": "0969040312",
     *                 "phone": "0935463164",
     *                 "serial_number": "7852136",
     *                 "nationalitie_id": 2,
     *                 "company_id": 1,
     *                 "birthday_date": "2022-11-26",
     *                 "material_status": 2,
     *                 "gender": 1,
     *                 "address": "Damascus",
     *                 "guarantor": "admin",
     *                 "branch": "syria branch",
     *                 "departement": "it",
     *                 "position": "it manger",
     *                 "type": 4,
     *                 "status": 1,
     *                 "skills": "no skills",
     *                 "number_working_hours": "0",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2023-10-01",
     *                 "image": "employees/2023-09-11-Employee-9.jpeg",
     *                 "id_photo": "2023-09-11-Employee-test.pdf",
     *                 "biography": "2023-09-11-Employee-320.png",
     *                 "employee_sponsorship": "2023-09-11-Employee-logo.png",
     *                 "end_employee_sponsorship": null,
     *                 "employee_residence": "employees/2023-09-11-Employee-44.png",
     *                 "end_employee_residence": "2023-09-20",
     *                 "visa": "2023-09-11-Employee-7.jpg",
     *                 "end_visa": "2023-09-11",
     *                 "passport": "2023-09-11-Employee-6.jpeg",
     *                 "end_passport": "2023-09-11",
     *                 "municipal_card": "employees/2023-09-11-Employee-5.jpg",
     *                 "end_municipal_card": "2023-09-10",
     *                 "health_insurance": "employees/2023-09-11-Employee-212.jpg",
     *                 "end_health_insurance": "2025-09-14",
     *                 "basic_salary": "400000.00",
     *                 "entry_time": 30,
     *                 "leave_time": 60,
     *                 "code": null,
     *                 "expired_at": null,
     *                 "is_verifed": 0,
     *                 "device_key": "0000",
     *                 "created_at": "2023-09-11T14:43:32.000000Z",
     *                 "updated_at": "2023-09-11T16:35:39.000000Z",
     *                 "deleted_at": null,
     *                 "pivot": {
     *                     "conversation_id": 1,
     *                     "user_id": 9,
     *                     "joined_at": "2023-09-11 18:40:35"
     *                  }
     *             }
     *       ]
     *    }
     *  ]
     */


    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function ($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },
        ])
            ->withCount([
                'recipients as new_messages' => function ($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->get();
    }

    /**
     * Show My specific Conversations List
     *
     * This endpoint retrieves a conversation that are specific to the authenticated employee. It requires authentication to access.
     * @queryParam id number required Must Be Exists In Conversations Table Custom Example: 1
     * @response 200 scenario="Show My specific Conversation"
     * {
     *     "conversation": {
     *     "id": 1,
     *     "user_id": 2,
     *     "lable": null,
     *     "type": "peer",
     *     "created_at": "2023-09-11T15:40:35.000000Z",
     *     "updated_at": "2023-09-11T15:43:04.000000Z",
     *     "last_message_id": 2,
     *     "pivot": {
     *     "user_id": 2,
     *     "conversation_id": 1,
     *     "joined_at": "2023-09-11 18:40:35"
     *     },
     *     "participants": [
     *          {
     *             "id": 9,
     *             "name": "mouaz alkhateeb",
     *             "email": "mouaz@gmail.com",
     *             "work_email": "mouazalkhateebbb@gmail.com",
     *             "email_verified_at": null,
     *             "mobile": "0969040312",
     *             "phone": "0935463164",
     *              *              *             "serial_number": "7852136",
     *             "nationalitie_id": 2,
     *             "company_id": 1,
     *             "birthday_date": "2022-11-26",
     *             "material_status": 2,
     *             "gender": 1,
     *             "address": "Damascus",
     *             "guarantor": "admin",
     *             "branch": "syria branch",
     *             "departement": "it",
     *             "position": "it manger",
     *             "type": 4,
     *             "status": 1,
     *             "skills": "no skills",
     *             "number_working_hours": "0",
     *             "start_job_contract": "2023-08-01",
     *             "end_job_contract": "2023-10-01",
     *             "image": "employees/2023-09-11-Employee-9.jpeg",
     *             "id_photo": "2023-09-11-Employee-test.pdf",
     *             "biography": "2023-09-11-Employee-320.png",
     *             "employee_sponsorship": "2023-09-11-Employee-logo.png",
     *             "end_employee_sponsorship": null,
     *             "employee_residence": "employees/2023-09-11-Employee-44.png",
     *             "end_employee_residence": "2023-09-20",
     *             "visa": "2023-09-11-Employee-7.jpg",
     *             "end_visa": "2023-09-11",
     *             "passport": "2023-09-11-Employee-6.jpeg",
     *             "end_passport": "2023-09-11",
     *             "municipal_card": "employees/2023-09-11-Employee-5.jpg",
     *             "end_municipal_card": "2023-09-10",
     *             "health_insurance": "employees/2023-09-11-Employee-212.jpg",
     *             "end_health_insurance": "2025-09-14",
     *             "basic_salary": "400000.00",
     *             "entry_time": 30,
     *             "leave_time": 60,
     *             "code": null,
     *             "expired_at": null,
     *             "is_verifed": 0,
     *             "device_key": "0000",
     *             "created_at": "2023-09-11T14:43:32.000000Z",
     *             "updated_at": "2023-09-11T16:35:39.000000Z",
     *             "deleted_at": null,
     *             "pivot": {
     *                 "conversation_id": 1,
     *                 "user_id": 9,
     *                 "joined_at": "2023-09-11 18:40:35"
     *             }
     *          }
     *             ]
     *             },
     *          *     "messages": [
     *     {
     *     "id": 2,
     *     "conversation_id": 1,
     *     "user_id": 9,
     *     "body": "Hi Firass",
     *     "type": "text",
     *     "created_at": "2023-09-11T15:43:04.000000Z",
     *     "updated_at": "2023-09-11T15:43:04.000000Z",
     *     "deleted_at": null,
     *     "user": {
     *             "id": 9,
     *             "name": "mouaz alkhateeb",
     *             "email": "mouaz@gmail.com",
     *             "work_email": "mouazalkhateebbb@gmail.com",
     *             "email_verified_at": null,
     *             "mobile": "0969040312",
     *             "phone": "0935463164",
     *             "serial_number": "7852136",
     *             "nationalitie_id": 2,
     *             "company_id": 1,
     *             "birthday_date": "2022-11-26",
     *             "material_status": 2,
     *             "gender": 1,
     *             "address": "Damascus",
     *             "guarantor": "admin",
     *             "branch": "syria branch",
     *             "departement": "it",
     *             "position": "it manger",
     *             "type": 4,
     *             "status": 1,
     *             "skills": "no skills",
     *             "number_working_hours": "0",
     *             "start_job_contract": "2023-08-01",
     *             "end_job_contract": "2023-10-01",
     *             "image": "employees/2023-09-11-Employee-9.jpeg",
     *             "id_photo": "2023-09-11-Employee-test.pdf",
     *             "biography": "2023-09-11-Employee-320.png",
     *             "employee_sponsorship": "2023-09-11-Employee-logo.png",
     *             "end_employee_sponsorship": null,
     *             "employee_residence": "employees/2023-09-11-Employee-44.png",
     *             "end_employee_residence": "2023-09-20",
     *             "visa": "2023-09-11-Employee-7.jpg",
     *             "end_visa": "2023-09-11",
     *             "passport": "2023-09-11-Employee-6.jpeg",
     *             "end_passport": "2023-09-11",
     *             "municipal_card": "employees/2023-09-11-Employee-5.jpg",
     *             "end_municipal_card": "2023-09-10",
     *             "health_insurance": "employees/2023-09-11-Employee-212.jpg",
     *             "end_health_insurance": "2025-09-14",
     *             "basic_salary": "400000.00",
     *             "entry_time": 30,
     *             "leave_time": 60,
     *             "code": null,
     *             "expired_at": null,
     *             "is_verifed": 0,
     *             "device_key": "0000",
     *             "created_at": "2023-09-11T14:43:32.000000Z",
     *             "updated_at": "2023-09-11T16:35:39.000000Z",
     *             "deleted_at": null
     *           }
     *             },
     *             {
     *     "id": 1,
     *     "conversation_id": 1,
     *     "user_id": 2,
     *     "body": "Hi Mouaz",
     *     "type": "text",
     *     "created_at": "2023-09-11T15:40:35.000000Z",
     *     "updated_at": "2023-09-11T15:40:35.000000Z",
     *     "deleted_at": null,
     *     "user": {
     *             "id": 2,
     *              "name": "Firas Jabi",
     *             "email": "firassaljabi1232@gmail.com",
     *             "work_email": "firassaljabi1237@goma.com",
     *             "email_verified_at": null,
     *             "mobile": "0969040342",
     *             "phone": "0935463111",
     *             "serial_number": "00011",
     *             "nationalitie_id": 2,
     *             "company_id": 1,
     *             "birthday_date": "1998-11-26",
     *             "material_status": 2,
     *             "gender": 1,
     *             "address": "Damascus",
     *             "guarantor": null,
     *             "branch": "syria branch",
     *             "departement": null,
     *             "position": null,
     *             "type": 2,
     *             "status": 3,
     *             "skills": null,
     *             "number_working_hours": "0",
     *             "start_job_contract": null,
     *             "end_job_contract": null,
     *             "image": null,
     *             "id_photo": null,
     *             "biography": null,
     *             "employee_sponsorship": null,
     *             "end_employee_sponsorship": null,
     *              "employee_residence": null,
     *             "end_employee_residence": null,
     *             "visa": null,
     *             "end_visa": null,
     *             "passport": null,
     *             "end_passport": null,
     *             "municipal_card": null,
     *             "end_municipal_card": null,
     *             "health_insurance": null,
     *             "end_health_insurance": null,
     *             "basic_salary": "0.00",
     *             "entry_time": null,
     *             "leave_time": null,
     *             "code": null,
     *             "expired_at": null,
     *             "is_verifed": 0,
     *             "device_key": null,
     *             "created_at": "2023-09-11T10:49:51.000000Z",
     *             "updated_at": "2023-09-11T10:49:51.000000Z",
     *             "deleted_at": null
     *             }
     *           }
     *        ]
     *             }
     */
    public function show_conversation_messages($id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()
            ->with(['participants' => function ($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            }])
            ->findOrFail($id);

        $messages = $conversation->messages()
            ->with('user')
            ->where(function ($query) use ($user) {
                $query
                    ->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->whereNull('deleted_at');
                    })
                    ->orWhereRaw('id IN (
                        SELECT message_id FROM recipients
                        WHERE recipients.message_id = messages.id
                        AND recipients.user_id = ?
                        AND recipients.deleted_at IS NULL
                    )', [$user->id]);
            })
            ->latest()
            ->get();

        return [
            'conversation' => $conversation,
            'messages' => $messages,
        ];
    }

    /**
     * Create New Message To HR
     *
     * This endpoint is used to create a new message from an employee to HR in the company.
     *
     * @bodyParam message string required The message content. Example: Hi HR
     * @bodyParam user_id int required The user ID of the employee, which must exist in the users table.
     *
     * @response 200 scenario="Create New Message To HR"{
     *     "user_id": 2,
     *     "body": "Hi Firass",
     *     "updated_at": "2023-09-12T11:56:50.000000Z",
     *     "created_at": "2023-09-12T11:56:50.000000Z",
     *     "id": 4,
     *     "user": {
     *         "id": 2,
     *         "name": "Firas Jabi",
     *         "email": "firassaljabi1232@gmail.com",
     *         "work_email": "firassaljabi1237@goma.com",
     *         "email_verified_at": null,
     *         "mobile": "0969040342",
     *         "phone": "0935463111",
     *         "serial_number": "00011",
     *         "nationalitie_id": 2,
     *         "company_id": 1,
     *         "birthday_date": "1998-11-26",
     *         "material_status": 2,
     *         "gender": 1,
     *         "address": "Damascus",
     *         "guarantor": null,
     *         "branch": "syria branch",
     *         "departement": null,
     *         "position": null,
     *         "type": 2,
     *         "status": 3,
     *         "skills": null,
     *         "number_working_hours": "0",
     *         "start_job_contract": null,
     *         "end_job_contract": null,
     *         "image": null,
     *         "id_photo": null,
     *         "biography": null,
     *         "employee_sponsorship": null,
     *         "end_employee_sponsorship": null,
     *         "employee_residence": null,
     *         "end_employee_residence": null,
     *         "visa": null,
     *         "end_visa": null,
     *         "passport": null,
     *         "end_passport": null,
     *         "municipal_card": null,
     *         "end_municipal_card": null,
     *         "health_insurance": null,
     *         "end_health_insurance": null,
     *         "basic_salary": "0.00",
     *         "entry_time": null,
     *         "leave_time": null,
     *         "code": null,
     *         "expired_at": null,
     *         "is_verified": 0,
     *         "device_key": null,
     *         "created_at": "2023-09-11T10:49:51.000000Z",
     *         "updated_at": "2023-09-11T10:49:51.000000Z",
     *         "deleted_at": null
     *     }
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'conversation_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->input('user_id');
                }),
                'integer',
                'exists:conversations,id'
            ],
            'user_id' => [
                Rule::requiredIf(function () use ($request) {
                    return !$request->input('conversation_id');
                }),
                'integer',
                'exists:users,id'
            ]
        ]);
        $user = Auth::user();
        $conversation_id = $request->post('conversation_id');
        $user_id = $request->post('user_id');

        Db::beginTransaction();
        try {

            if ($conversation_id) {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            } else {
                $conversation = Conversation::where('type', 'peer')->whereHas('participants', function ($builder)  use ($user_id, $user) {
                    $builder->join('participants as participants2', 'participants2.conversation_id', 'participants.conversation_id')
                        ->where('participants.user_id', $user_id)
                        ->where('participants2.user_id', $user->id);
                })->first();
                if (!$conversation) {
                    $conversation = Conversation::create([
                        'user_id' => $user->id,
                        'type' => 'peer'
                    ]);
                    $conversation->participants()->attach([
                        $user->id => ['joined_at' => now()],
                        $user_id => ['joined_at' => now()],
                    ]);
                }
            }

            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $request->post('message')
            ]);


            DB::statement('
            INSERT INTO recipients (user_id, message_id)
            SELECT user_id, ? FROM participants
            WHERE conversation_id = ?
            AND user_id <> ?
        ', [$message->id, $conversation->id, $user->id]);

            $conversation->update(['last_message_id' => $message->id]);


            DB::commit();

            $message->load('user');

            broadcast(new MessageCreated($message));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return $message;
    }

    /**
     * Mark Messages as Read
     *
     * This endpoint is used to mark all messages as read in a specific conversation. Only authenticated employees can access this API.
     *
     * @urlParam conversation required The ID of the conversation. Must exist in the conversations table, Custom Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "message": "Notifications marked as read"
     *     }
     * }
     */

    public function markAsRead($id)
    {
        Recipient::where('user_id', '=', Auth::id())
            ->whereNull('read_at')
            ->whereRaw('message_id IN (
                SELECT id FROM messages WHERE conversation_id = ?
            )', [$id])
            ->update([
                'read_at' => Carbon::now(),
            ]);

        return [
            'message' => 'Messages marked as read',
        ];
    }
}
