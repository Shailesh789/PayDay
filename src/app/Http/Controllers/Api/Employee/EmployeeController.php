<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Assets\CompanyAssetController;
use App\Http\Controllers\Tenant\Employee\AnnouncementController;
use App\Http\Controllers\Tenant\Employee\EmployeeAddressController;
use App\Http\Controllers\Tenant\Employee\EmployeeJobHistoryController;
use App\Http\Controllers\Tenant\Employee\EmployeeSalaryController;
use App\Http\Resources\Payday\Employee\AnnouncementResource;
use App\Http\Resources\Payday\Employee\AssetDetailsResource;
use App\Http\Resources\Payday\Employee\AssetResource;
use App\Http\Resources\Payday\Employee\JobHistoryResource;
use App\Http\Resources\Payday\Employee\SalaryOverviewResource;
use App\Models\Tenant\Assets\CompanyAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function jobHistory()
    {
        try {
            $response = resolve(EmployeeJobHistoryController::class)->index(Auth::user());
            return success_response('Job history', new JobHistoryResource($response));
        } catch (\Exception $ex) {
            return error_response('Server error!', [], 500);
        }
    }

    public function salaryOverview()
    {
        try {
            $array = [];
            $salaryOverview = resolve(EmployeeSalaryController::class)->index(Auth::user());
            foreach ($salaryOverview as $key => $data) {
                $basicSalary = false;
                $message = '';
                $level = '';
                $increment = false;
                $effectiveDate = '';
                $createdDate = '';
                $previousSalary = 0.00;
                $currentSalary = 0.00;

                if (!$data->end_at) {
                    if (Carbon::parse($data->start_at)->gt(Carbon::now())) {
                        if (isset($salaryOverview[$key + 1])) {
                            $effectiveDate = date('d M Y', strtotime($data->start_at));
                            $createdDate = date('d M Y', strtotime($data->created_at));
                            if ($data->amount > $salaryOverview[$key + 1]->amount) {
                                $level = "Salary Increment";
                                $message = "has awarded a salary increment, from ";
                                $increment = true;
                            } else {
                                $level = "Salary Decrement";
                                $message = "has punished a salary decrement. from ";
                                $increment = false;
                            }
                            $previousSalary = $salaryOverview[$key + 1]->amount;
                            $currentSalary = $data->amount;
                        }

                    } else {
                        $level = date('d M Y', strtotime($data->start_at)) . ' - Present';
                    }
                }

                if (Carbon::parse($data->start_at)->lt(Carbon::now()) && ($data->end_at && Carbon::parse($data->end_at)->gt(Carbon::now()))) {
                    $level = date('d M Y', strtotime($data->start_at)) . ' - Present';
                    $basicSalary = true;
                } else if (Carbon::parse($data->start_at)->lt(Carbon::now()) && ($data->end_at && Carbon::parse($data->end_at)->lt(Carbon::now()))) {
//                    $level = date('d M Y', strtotime($data->start_at)) . ' - ' . date('d M Y', strtotime($data->end_at));
                }


                $array[$key] = [
                    'added_by' => $data->addedBy->full_name,
                    'increment' => $increment,
                    'level' => $level,
                    'effective_date' => $effectiveDate,
                    'created_date' => $createdDate,
                    'basic_salary' => $basicSalary,
                    'amount' => number_format($data->amount, 2),
                    'previous_salary' => number_format($previousSalary, 2),
                    'current_salary' => number_format($currentSalary, 2),
                    'message' => $message,
                ];
            }
            return success_response('Salary Overview', $array);
        } catch (\Exception $ex) {
            return error_response('Server error', [], 500);
        }
    }

    public function addresses()
    {
        try {
            $addresses = resolve(EmployeeAddressController::class)->show(Auth::user());

            $result = ['present_address' => null, 'permanent_address' => null];
            $value = [];


            foreach ($addresses as $item) {
                $key = $item['key'];
                $value['details'] = @$item['value']->details;
                $value['area'] = @$item['value']->area;
                $value['city'] = @$item['value']->city;
                $value['state'] = @$item['value']->state;
                $value['zip_code'] = @$item['value']->zip_code;
                $value['country'] = @$item['value']->country;
                $value['phone_number'] = @$item['value']->phone_number;
                $value['country_code'] = @$item['value']->country_code;
                $result[$key] = $value;
            }


            return success_response('Addresses', $result);
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }

    public function addressUpdate(Request $request)
    {
        try {
            $response = resolve(EmployeeAddressController::class)->update(Auth::user(), $request);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 422);
        }
    }

    public function addressDelete(Request $request)
    {
        try {
            $response = resolve(EmployeeAddressController::class)->delete(Auth::user(), $request->type);
            if (is_array($response)) {
                $response['data'] = [];
            }
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }

    public function asset()
    {
        try {
            $assets = resolve(CompanyAssetController::class)->employeeAssets(Auth::user());
            $assetCount = CompanyAsset::query()
                ->where('user_id', auth()->id())
                ->count();

            $assetResource = new AssetResource($assets);

            return success_response('Assets of employee', [
                'asset_count' => $assetCount,
                'assets' => $assetResource
            ]);

        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }

    public function assetDetails(CompanyAsset $asset)
    {
        try {
            $assetDetails = new AssetDetailsResource($asset->load('type'));

            return success_response('Asset details', $assetDetails);
        } catch (\Exception $ex) {
            return error_response('Server Error', [], 500);
        }
    }

    public function announcement(CompanyAsset $asset)
    {
        try {
            $announcement = resolve(AnnouncementController::class)->index();

            $formattedAnnouncement = new AnnouncementResource($announcement);

            return success_response('Announcement data fetched', $formattedAnnouncement);
        } catch (\Exception $ex) {
            return error_response('Server Error', [], 500);
        }
    }
}
