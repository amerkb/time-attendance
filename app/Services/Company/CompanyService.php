<?php


namespace App\Services\Company;

use App\Filter\Company\CompanyFilter;
use App\Interfaces\Comapny\CompanyServiceInterface;
use App\Models\Company;
use App\Models\User;
use App\Repository\Company\CompanyRepository;
use App\Statuses\EmployeeStatus;
use App\Statuses\UserTypes;

class CompanyService implements CompanyServiceInterface
{

    public function __construct(private CompanyRepository $companyRepository)
    {
    }
    public function create_company($data)
    {
        return $this->companyRepository->create_company($data);
    }
    public function add_commercial_record($data)
    {
        return $this->companyRepository->add_commercial_record($data);
    }

    public function update_company($data)
    {
        return $this->companyRepository->update_company($data);
    }




    public function update_commercial_record($data)
    {
        return $this->companyRepository->update_commercial_record($data);
    }

    public function show($id)
    {
        if (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $id || auth()->user()->type == UserTypes::SUPER_ADMIN) {

            return ['success' => true, 'data' => $this->companyRepository->with('admin', 'addresess', 'locations')->getById($id)];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function show_percenatge_company()
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR) {
            $company = Company::where('id', auth()->user()->company_id)->first();
            return ['success' => true, 'data' => $company];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function show_check_type_company()
    {
        if (auth()->user()->type == UserTypes::ADMIN || auth()->user()->type == UserTypes::HR || auth()->user()->type == UserTypes::EMPLOYEE) {
            $check_type = Company::where('id', auth()->user()->company_id)->pluck('check_type');
            return response()->json(['check_type' => $check_type[0]]);
        } else {
            return response()->json(['message' => "Unauthorized"], 401);
        }
    }



    public function update_percentage()
    {
        $company = Company::where('id', auth()->user()->company_id)->first();
        if (auth()->user()->type == UserTypes::ADMIN  || auth()->user()->type == UserTypes::HR && $company->id == auth()->user()->company_id) {

            if ($company->percentage == false) {
                $company->update([
                    'percentage' => true
                ]);
            } else {
                $company->update([
                    'percentage' => false
                ]);
            }
            return ['success' => true, 'data' =>  $company];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function update_check_type($data)
    {
        $company = Company::where('id', auth()->user()->company_id)->first();
        if (auth()->user()->type == UserTypes::ADMIN  || auth()->user()->type == UserTypes::HR && $company->id == auth()->user()->company_id) {


            $company->update([
                'check_type' => $data['type']
            ]);
            $companyType =  $company->pluck('check_type');
            return response()->json(['check_type' => $companyType[0]]);
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }

    public function list_of_companies()
    {
        return $this->companyRepository->list_of_companies();
    }

    public function list_of_archive_companies()
    {
        return $this->companyRepository->list_of_archive_companies();
    }

    public static function numberOfEmployee($company_id)
    {
        $employeeCount = User::where('company_id', $company_id)->where('type', UserTypes::EMPLOYEE)->whereNotIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED])->count();
        return $employeeCount;
    }
    public static function numberOfDismissedEmployee($company_id)
    {
        $employeeCount = User::where('company_id', $company_id)->where('type', UserTypes::EMPLOYEE)->whereIn('status', [EmployeeStatus::TEMPORARY_DISMISSED, EmployeeStatus::PERMANENT_DISMISSED])->count();
        return $employeeCount;
    }

    public function destroy(int $id)
    {
        if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
            $company = Company::findOrFail($id);
            $company->delete();
        } else {
            return "Unauthorized";
        }
    }

    public function force_delete(int $id)
    {
        if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
            Company::where('id', $id)->withTrashed()->forceDelete();
        } else {
            return "Unauthorized";
        }
    }
    public function restore(int $id)
    {
        if (auth()->user()->type == UserTypes::SUPER_ADMIN) {

            Company::where('id', $id)->withTrashed()->restore();
            $company = Company::where('id', $id)->first();

            return ['success' => true, 'data' =>  $company->load(['admin',  'employees', 'dismissedEmployees', 'addresess', 'locations'])];
        } else {
            return "Unauthorized";
        }
    }
}
