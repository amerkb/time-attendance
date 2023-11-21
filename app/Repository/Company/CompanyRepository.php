<?php

namespace App\Repository\Company;

use App\Http\Trait\UploadImage;
use App\Models\Address;
use App\Models\Company;
use App\Models\Location;
use App\Repository\BaseRepositoryImplementation;
use App\Statuses\UserTypes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class CompanyRepository extends BaseRepositoryImplementation
{
    use UploadImage;

    public function getFilterItems($filter)
    {
    }

    public function list_of_companies()
    {
        if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
            $records = Company::query()->with(['admin', 'employees', 'dismissedEmployees', 'addresess', 'locations']);

            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }
    public function list_of_archive_companies()
    {
        if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
            $records = Company::query()->with(['admin', 'employees', 'dismissedEmployees', 'addresess', 'locations']);


            return ['success' => true, 'data' => $records->get()];
        } else {
            return ['success' => false, 'message' => "Unauthorized"];
        }
    }


    public function create_company($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::SUPER_ADMIN) {
                $company = new Company();
                $company->name = $data['name'];
                $company->email = $data['email'];
                $company->check_type = $data['type'];
                $company->save();
                if (isset($data['longitude']) && isset($data['latitude']) && isset($data['radius'])) {
                    Location::create([
                        'company_id' => $company->id,
                        'longitude' => $data['longitude'],
                        'latitude' => $data['latitude'],
                        'radius' => $data['radius'],
                    ]);
                }
                if (isset($data['mac_address'])) {
                    foreach ($data['mac_address'] as $value) {
                        Address::create([
                            'company_id' => $company->id,
                            'mac_address' => $value
                        ]);
                    }
                }
                DB::commit();
                if ($company === null) {
                    return ['success' => false, 'message' => "Company was not created"];
                }
                return ['success' => true, 'data' => $company->load('admin', 'addresess', 'locations')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function add_commercial_record($data)
    {
        DB::beginTransaction();
        try {

            if (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $data['company_id']) {

                $company = $this->getById($data['company_id']);
                $file = Arr::get($data, 'commercial_record');
                if ($file !== null && $file !== '' && is_file($file)) {

                    $file_name = $this->uploadCompanyAttachment($file, $company->id);
                    $company->commercial_record = $file_name;
                }
                $company->start_commercial_record = $data['start_commercial_record'];
                $company->end_commercial_record = $data['end_commercial_record'];
                $company->save();

                DB::commit();
                if ($company === null) {
                    return ['success' => false, 'message' => "commercial record was not Added"];
                }
                return ['success' => true, 'data' => $company->load('admin', 'addresess', 'locations')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_company($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::SUPER_ADMIN || (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $data['company_id'])) {
                $company = Company::where('id', $data['company_id'])->first();
                $location = Location::where('company_id', $data['company_id'])->first();
                if (isset($data['name'])) {
                    $company->update([
                        'name' => $data['name']
                    ]);
                }
                if (isset($data['email'])) {
                    $company->update([
                        'email' => $data['email']
                    ]);
                }
                if ($location) {
                    if (isset($data['longitude'])) {
                        $location->update([
                            'longitude' => $data['longitude']
                        ]);
                    }
                    if (isset($data['latitude'])) {
                        $location->update([
                            'latitude' => $data['latitude']
                        ]);
                    }
                    if (isset($data['radius'])) {
                        $location->update([
                            'radius' => $data['radius']
                        ]);
                    }
                }
                if (!$location && isset($data['longitude']) && isset($data['latitude']) && isset($data['radius'])) {
                    Location::create([
                        'company_id' => $company->id,
                        'longitude' => $data['longitude'],
                        'latitude' => $data['latitude'],
                        'radius' => $data['radius'],
                    ]);
                }

                if ( isset($data['mac_address'])) {
                    foreach ($data['mac_address'] as $value) {
                        if (isset($value['id'])) {
                            $address = Address::where('id', $value['id'])->first();
                            if ($address) {
                                $address->update([
                                    'mac_address' => $value['value'],
                                ]);
                            }
                            elseif (!$address) {
                                Address::create([
                                'company_id' => $company->id,
                                'mac_address' => $value['value']
                            ]);
                            }
                        }
                        elseif (!isset($value['id'])) {
                            Address::create([
                                'company_id' => $company->id,
                                'mac_address' => $value['value']
                            ]);
                        }

                        }
                }

                $company->save();
                DB::commit();
                return ['success' => true, 'data' => $company->load('admin', 'addresess', 'locations')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update_commercial_record($data)
    {
        DB::beginTransaction();
        try {
            if (auth()->user()->type == UserTypes::ADMIN && auth()->user()->company_id == $data['company_id']) {
                $company = $this->updateById($data['company_id'], $data);
                if (Arr::has($data, 'commercial_record')) {
                    $file = Arr::get($data, 'commercial_record');

                    if ($file !== null && $file !== '' && is_file($file)) {
                        $file_name = $this->deleteAndUploadCompanyAttachment($file, $company->id, 'commercial_record');
                        $company->commercial_record = $file_name;
                    }
                }
                $company->save();
                DB::commit();
                if ($company === null) {
                    return ['success' => false, 'message' => "Commercial Record was not Updated"];
                }
                return ['success' => true, 'data' => $company->load('admin', 'addresess', 'locations')];
            } else {
                return ['success' => false, 'message' => "Unauthorized"];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function model()
    {
        return Company::class;
    }
}
