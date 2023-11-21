<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper\ApiResponseHelper;
use App\ApiHelper\ErrorResult;
use App\ApiHelper\Result;
use App\ApiHelper\SuccessResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\CreateCommentRequest;
use App\Http\Requests\Posts\CreateLikeCommentRequest;
use App\Http\Requests\Posts\CreateLikeRequest;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\CreateSharePostRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Posts\CommentResource;
use App\Http\Resources\Posts\LikeResource;
use App\Http\Resources\Posts\PostResource;
use App\Http\Resources\Posts\ShareResource;
use App\Services\Posts\PostService;


/**
 * @group Posts
 *
 * @authenticated
 *
 * APIs for managing Posts
 */

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }
    /**
     * Create Post
     *
     * This endpoint is used to create a new post. Admin, HR, or Employee can access this API.
     *
     * @bodyParam content string required The content of the post. Example: test post
     * @bodyParam image file The image file to be attached to the post. Must not exceed 5120 kilobytes.
     *
     * @response 200 scenario="Create Post"{
     *   "data": {
     *     "id": 2,
     *     "content": "test data",
     *     "image": null,
     *     "comments_count": null,
     *     "likes_count": null,
     *     "shares_count": null,
     *     "created_at": "2023-08-27",
     *     "comments": [],
     *     "likes": [],
     *     "shares": [],
     *     "writer": {
     *         "id": 3,
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
     *         "end_job_contract": "2025-05-01",
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
     *         "permission_to_entry": 1,
     *         "entry_time": 30
     *         "permission_to_leave": 1,
     *         "leave_time": 60,
     *         "percentage": "0",
     *         "basic_salary": 200000,
     *         "number_of_working_hours": 0,
     *       }
     *}
     */

    public function store(CreatePostRequest $request)
    {
        $createdData =  $this->postService->create_post($request->validated());

        $returnData = PostResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    /**
     * List of Posts
     *
     * This endpoint is used to show a list of posts that can be accessed by the company, admins, HR, and employees.
     *
     * @response 200 scenario="List Of Posts"{
     *     "data": [
     *         {
     *             "id": 1,
     *             "content": "test data",
     *             "image": null,
     *             "comments_count": 1,
     *             "likes_count": 1,
     *             "shares_count": 0,
     *             "created_at": "2023-08-27",
     *             "isLiked": true,
     *             "comments": [
     *                 {
     *                     "content": "New Comment",
     *                     "likes_count": 0,
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "isLiked": false,
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "likes": [
     *                 {
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "shares": [],
     *             "writer": {
     *                 "id": 3,
     *                 "name": "Mouaz Alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateeb@gmail.com",
     *                 "status": 1,
     *                 "type": 4,
     *                 "gender": 1,
     *                 "mobile": "0969040322",
     *                 "phone": "0969040322",
     *                 "department": "IT",
     *                 "address": "Damascus",
     *                 "position": null,
     *                 "skills": "no skills",
     *                 "serial_number": "000007",
     *                 "birthday_date": "2022-11-26",
     *                 "marital_status": null,
     *                 "guarantor": "admin",
     *                 "branch": "Syria Branch",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2025-05-01",
     *                 "end_visa": "2023-09-11",
     *                 "end_passport": "2023-09-11",
     *                 "end_employee_sponsorship": null,
     *                 "end_municipal_card": "2023-09-10",
     *                 "end_health_insurance": "2023-09-14",
     *                 "end_employee_residence": "2023-09-20",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "id_photo": null,
     *                 "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *                 "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *                 "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *                 "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *                 "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *                 "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *                 "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *                 "permission_to_entry": 1,
     *                 "entry_time": 30,
     *                 "permission_to_leave": 1,
     *                 "leave_time": 60,
     *                 "percentage": "0",
     *                 "basic_salary": 200000,
     *                 "number_of_working_hours": 0,
     *                }
     *             },
     *           },
     *
     *           {
     *             "id": 2,
     *             "content": "new post",
     *             "image": null,
     *             "comments_count": 1,
     *             "likes_count": 1,
     *             "shares_count": 0,
     *             "created_at": "2023-08-27",
     *             "comments": [
     *                 {
     *                     "content": "New Comment",
     *                     "likes_count": 0,
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "likes": [
     *                 {
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "shares": [],
     *             "writer": {
     *                 "id": 3,
     *                 "name": "Mouaz Alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateeb@gmail.com",
     *                 "status": 1,
     *                 "type": 4,
     *                 "gender": 1,
     *                 "mobile": "0969040322",
     *                 "phone": "0969040322",
     *                 "department": "IT",
     *                 "address": "Damascus",
     *                 "position": null,
     *                 "skills": "no skills",
     *                 "serial_number": "000007",
     *                 "birthday_date": "2022-11-26",
     *                 "marital_status": null,
     *                 "guarantor": "admin",
     *                 "branch": "Syria Branch",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2025-05-01",
     *                 "end_visa": "2023-09-11",
     *                 "end_passport": "2023-09-11",
     *                 "end_employee_sponsorship": null,
     *                 "end_municipal_card": "2023-09-10",
     *                 "end_health_insurance": "2023-09-14",
     *                 "end_employee_residence": "2023-09-20",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "id_photo": null,
     *                 "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *                 "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *                 "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *                 "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *                 "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *                 "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *                 "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *                 "permission_to_entry": 1,
     *                 "entry_time": 30,
     *                 "permission_to_leave": 1,
     *                 "leave_time": 60,
     *                 "percentage": "0",
     *                 "basic_salary": 200000,
     *                 "number_of_working_hours": 0,
     *                }
     *             },
     *           },
     *      ],
     *    }
     */

    public function getPostsList()
    {
        $data = $this->postService->getPosts();
        $returnData = PostResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }
    /**
     * My Posts
     *
     * This endpoint is used to display the list of my posts and authenticate employee access to this API. It will show the posts specific to the authenticated employee.
     * @response 200 scenario="My Posts"{
     *     "data": [
     *         {
     *             "id": 1,
     *             "content": "test data",
     *             "image": null,
     *             "comments_count": 1,
     *             "likes_count": 1,
     *             "shares_count": 0,
     *             "created_at": "2023-08-27",
     *             "isLiked": false,
     *             "comments": [
     *                 {
     *                     "content": "New Comment",
     *                     "likes_count": 0,
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "isLiked": true,
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "likes": [
     *                 {
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "shares": [],
     *             "writer": {
     *                 "id": 3,
     *                 "name": "Mouaz Alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateeb@gmail.com",
     *                 "status": 1,
     *                 "type": 4,
     *                 "gender": 1,
     *                 "mobile": "0969040322",
     *                 "phone": "0969040322",
     *                 "department": "IT",
     *                 "address": "Damascus",
     *                 "position": null,
     *                 "skills": "no skills",
     *                 "serial_number": "000007",
     *                 "birthday_date": "2022-11-26",
     *                 "marital_status": null,
     *                 "guarantor": "admin",
     *                 "branch": "Syria Branch",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2025-05-01",
     *                 "end_visa": "2023-09-11",
     *                 "end_passport": "2023-09-11",
     *                 "end_employee_sponsorship": null,
     *                 "end_municipal_card": "2023-09-10",
     *                 "end_health_insurance": "2023-09-14",
     *                 "end_employee_residence": "2023-09-20",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "id_photo": null,
     *                 "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *                 "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *                 "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *                 "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *                 "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *                 "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *                 "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *                 "permission_to_entry": 1,
     *                 "entry_time": 30,
     *                 "permission_to_leave": 1,
     *                 "leave_time": 60,
     *                 "percentage": "0",
     *                 "basic_salary": 200000,
     *                 "number_of_working_hours": 0,
     *                }
     *             },
     *           },
     *
     *           {
     *             "id": 2,
     *             "content": "new post",
     *             "image": null,
     *             "comments_count": 1,
     *             "likes_count": 1,
     *             "shares_count": 0,
     *             "created_at": "2023-08-27",
     *             "comments": [
     *                 {
     *                     "content": "New Comment",
     *                     "likes_count": 0,
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "likes": [
     *                 {
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "shares": [],
     *             "writer": {
     *                 "id": 3,
     *                 "name": "Mouaz Alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateeb@gmail.com",
     *                 "status": 1,
     *                 "type": 4,
     *                 "gender": 1,
     *                 "mobile": "0969040322",
     *                 "phone": "0969040322",
     *                 "department": "IT",
     *                 "address": "Damascus",
     *                 "position": null,
     *                 "skills": "no skills",
     *                 "serial_number": "000007",
     *                 "birthday_date": "2022-11-26",
     *                 "marital_status": null,
     *                 "guarantor": "admin",
     *                 "branch": "Syria Branch",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2025-05-01",
     *                 "end_visa": "2023-09-11",
     *                 "end_passport": "2023-09-11",
     *                 "end_employee_sponsorship": null,
     *                 "end_municipal_card": "2023-09-10",
     *                 "end_health_insurance": "2023-09-14",
     *                 "end_employee_residence": "2023-09-20",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "id_photo": null,
     *                 "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *                 "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *                 "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *                 "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *                 "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *                 "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *                 "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *                 "permission_to_entry": 1,
     *                 "entry_time": 30,
     *                 "permission_to_leave": 1,
     *                 "leave_time": 60,
     *                 "percentage": "0",
     *                 "basic_salary": 200000,
     *                 "number_of_working_hours": 0,
     *                }
     *             },
     *           },
     *      ],
     *    }
     */
    public function getMyPosts()
    {
        $data = $this->postService->getMyPosts();
        $returnData = PostResource::collection($data);
        $pagination = PaginationResource::make($data);
        return ApiResponseHelper::sendResponseWithPagination(
            new Result($returnData, $pagination, "DONE")
        );
    }

    /**
     * Show Post
     *
     * This endpoint is used to display specific post and authenticate user access to this API.
     * @response 200 scenario="Show Post"{
     *     "data": [
     *         {
     *             "id": 1,
     *             "content": "test data",
     *             "image": null,
     *             "comments_count": 1,
     *             "likes_count": 1,
     *             "shares_count": 0,
     *             "created_at": "2023-08-27",
     *             "isLiked": false,
     *             "comments": [
     *                 {
     *                     "content": "New Comment",
     *                     "likes_count": 0,
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "isLiked": true,
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "likes": [
     *                 {
     *                     "user": {
     *                         "name": "Mouaz Alkhateeb",
     *                         "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg"
     *                     },
     *                     "created_at": "2023-08-27"
     *                 }
     *             ],
     *             "shares": [],
     *             "writer": {
     *                 "id": 3,
     *                 "name": "Mouaz Alkhateeb",
     *                 "email": "mouaz@gmail.com",
     *                 "work_email": "mouazalkhateeb@gmail.com",
     *                 "status": 1,
     *                 "type": 4,
     *                 "gender": 1,
     *                 "mobile": "0969040322",
     *                 "phone": "0969040322",
     *                 "department": "IT",
     *                 "address": "Damascus",
     *                 "position": null,
     *                 "skills": "no skills",
     *                 "serial_number": "000007",
     *                 "birthday_date": "2022-11-26",
     *                 "marital_status": null,
     *                 "guarantor": "admin",
     *                 "branch": "Syria Branch",
     *                 "start_job_contract": "2023-08-01",
     *                 "end_job_contract": "2025-05-01",
     *                 "end_visa": "2023-09-11",
     *                 "end_passport": "2023-09-11",
     *                 "end_employee_sponsorship": null,
     *                 "end_municipal_card": "2023-09-10",
     *                 "end_health_insurance": "2023-09-14",
     *                 "end_employee_residence": "2023-09-20",
     *                 "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *                 "id_photo": null,
     *                 "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *                 "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *                 "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *                 "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *                 "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *                 "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *                 "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *                 "permission_to_entry": 1,
     *                 "entry_time": 30,
     *                 "permission_to_leave": 1,
     *                 "leave_time": 60,
     *                 "percentage": "0",
     *                 "basic_salary": 200000,
     *                 "number_of_working_hours": 0,
     *                }
     *             },
     *           },
     *    }
     */
    public function show($id)
    {
        $request = $this->postService->show($id);
        if ($request['success']) {
            $newData = $request['data'];
            $returnData = PostResource::make($newData);

            return ApiResponseHelper::sendResponse(
                new Result($returnData, "Done")
            );
        } else {
            return ['message' => $request['message']];
        }
    }
    /**
     * Add Comment
     *
     * This endpoint is used to create a new Comment on post. Admin, HR, or Employee can access this API.
     *
     * @bodyParam post_id int required Must Be in posts Table.
     * @bodyParam content string required The content of the post. Example: test comment
     *
     * @response 200 scenario="Add Comment"{
     *   "data": {
     *     "id": 2,
     *     "content": "New Comment",
     *     "likes_count": null,
     *      "user": {
     *         "id": 3,
     *         "name": "mouaz alkhateeb",
     *         "image": "employees/2023-08-27-Employee-8.jpg",
     *         "position: "Backend"
     *       },
     *      "post_id": 1,
     *     "created_at": "2023-08-27",
     *}
     */
    public function addComment(CreateCommentRequest $request)
    {

        $createdData =  $this->postService->create_comment($request->validated());

        $returnData = CommentResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }

    /**
     * Add Like
     *
     * This endpoint is used to Add a new Like on post. Admin, HR, or Employee can access this API.
     *
     * @bodyParam post_id int required Must Be in posts Table.
     *
     * @response 200 scenario="Add Like"{
     *   "data": {
     *      "user": {
     *         "id": 3,
     *         "name": "mouaz alkhateeb",
     *         "image": "employees/2023-08-27-Employee-8.jpg",
     *          "position: "Backend"
     *       },
     *     "created_at": "2023-08-27",
     *}
     */
    public function addLike(CreateLikeRequest $request)
    {
        $createdData =  $this->postService->add_like($request->validated());

        if ($createdData == null) {
            return response()->json(["message" => "Like Deleted..!"]);
        } else {
            $returnData = LikeResource::make($createdData);
        }

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    /**
     * Add Like To Comment
     *
     * This endpoint is used to Add Like To Comment. Admin, HR, or Employee can access this API.
     *
     * @bodyParam comment_id int required Must Be in comments Table.
     *
     * @response 200 scenario="Add Like To Comment"{
     *   "data": {
     *      "user": {
     *         "id": 3,
     *         "name": "mouaz alkhateeb",
     *         "image": "employees/2023-08-27-Employee-8.jpg",
     *         "position: "Backend"
     *       },
     *     "created_at": "2023-08-27",
     *}
     */
    public function add_like_comment(CreateLikeCommentRequest $request)
    {
        $createdData =  $this->postService->add_like_comment($request->validated());

        if ($createdData == null) {
            return response()->json(["message" => "Like Deleted..!"]);
        } else {
            $returnData = LikeResource::make($createdData);
        }

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    /**
     * Share Post
     *
     * This endpoint is used to share a post. Admin, HR, or Employee can access this API.
     *
     * @bodyParam post_id int required The ID of the post to share. Must be in the posts table.
     *
     * @response 200 scenario="Share Post"{
     *   "data": {
     *      "user": {
     *         "id": 3,
     *         "name": "mouaz alkhateeb",
     *         "image": "employees/2023-08-27-Employee-8.jpg"
     *       },
     *     "post": {
     *         "id": 1,
     *         "content": "test data",
     *         "image": null,
     *         "comments_count": 4,
     *         "likes_count": 1,
     *         "shares_count": 1,
     *         "created_at": "2023-08-27",
     *         "isLiked": true,
     *         "writer": {
     *             "id": 3,
     *             "name": "mouaz alkhateeb",
     *             "email": "mouaz@gmail.com",
     *             "work_email": "mouazalkhateeb@gmail.com",
     *             "status": 1,
     *             "type": 4,
     *             "gender": 1,
     *             "mobile": "0969040322",
     *             "phone": "0969040322",
     *             "departement": "it",
     *             "address": "Damascus",
     *             "position": null,
     *             "skills": "no skills",
     *             "serial_number": "000007",
     *             "birthday_date": "2022-11-26",
     *             "marital_status": null,
     *             "guarantor": "admin",
     *             "branch": "syria branch",
     *             "start_job_contract": "2023-08-01",
     *             "end_job_contract": "2025-05-01",
     *             "end_visa": "2023-09-11",
     *             "end_passport": "2023-09-11",
     *             "end_employee_sponsorship": null,
     *             "end_municipal_card": "2023-09-10",
     *             "end_health_insurance": "2023-09-14",
     *             "end_employee_residence": "2023-09-20",
     *             "image": "http://127.0.0.1:8000/employees/2023-08-27-Employee-8.jpg",
     *             "id_photo": null,
     *             "biography": "http://127.0.0.1:8000/employees/2023-08-27-Employee-test.pdf",
     *             "employee_sponsorship": "http://127.0.0.1:8000/employees/2023-08-27-Employee-55.jpg",
     *             "visa": "http://127.0.0.1:8000/employees/2023-08-27-Employee-7.jpg",
     *             "passport": "http://127.0.0.1:8000/employees/2023-08-27-Employee-6.jpeg",
     *             "municipal_card": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpeg",
     *             "health_insurance": "http://127.0.0.1:8000/employees/2023-08-27-Employee-9.jpg",
     *             "employee_residence": "http://127.0.0.1:8000/employees/2023-08-27-Employee-77.jpg",
     *             "permission_to_entry": 1,
     *             "entry_time": 30,
     *             "permission_to_leave": 1,
     *             "leave_time": 60,
     *             "percentage": "0",
     *             "basic_salary": 200000,
     *             "number_of_working_hours": 0,
     *           }
     *         },
     *             "created_at": "2023-08-28"
     *     }
     */
    public function sharePost(CreateSharePostRequest $request)
    {
        $createdData =  $this->postService->share_post($request->validated());


        $postData = $createdData['post'];
        $shareData = $createdData['share'];
        $returnData = PostResource::make($createdData);

        return ApiResponseHelper::sendResponse(
            new Result($returnData, "Done")
        );
    }
    /**
     * Delete Post
     *
     * This endpoint is used to to Delete Post and authenticate employee access to this API and Admin. It will delete post specific to the authenticated employee.
     *
     *@urlParam id int required Must Be Exists In posts Table
     * @response 200 scenario="Delete Post"{
     *
     * }
     */
    public function destroyPost($id)
    {
        $deletionResult = $this->postService->DeletePost($id);

        if (is_string($deletionResult)) {
            return ApiResponseHelper::sendErrorResponse(
                new ErrorResult($deletionResult)
            );
        }

        return ApiResponseHelper::sendSuccessResponse(
            new SuccessResult("Done", $deletionResult)
        );
    }
    /**
     * Delete Comment
     *
     * This endpoint is used to to Delete Comment and authenticate employee access to this API and Admin. It will delete comment specific to the authenticated employee.
     *
     *@urlParam id int required Must Be Exists In comments Table
     * @response 200 scenario="Delete Comment"{
     *
     * }
     */
    public function destroyComment($id)
    {
        $deletionResult = $this->postService->deleteComment($id);

        if (is_string($deletionResult)) {
            return ApiResponseHelper::sendErrorResponse(
                new ErrorResult($deletionResult)
            );
        }

        return ApiResponseHelper::sendSuccessResponse(
            new SuccessResult("Done", $deletionResult)
        );
    }
}
