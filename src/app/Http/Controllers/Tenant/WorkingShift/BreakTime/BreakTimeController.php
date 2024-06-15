<?php

namespace App\Http\Controllers\Tenant\WorkingShift\BreakTime;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\CompanyAssetTypeFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\WorkingShift\BreakTime\BreakTime;
use Illuminate\Http\Request;

class BreakTimeController extends Controller
{
    public function __construct(CompanyAssetTypeFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return BreakTime::filters($this->filter)
            ->orderBy('id', request()->get('orderBy', 'desc'))
            ->paginate(request()->get('per_page', 10));
    }

    public function selectableBreakTimes()
    {
        return BreakTime::query()
            ->orderBy('id', request()->get('orderBy', 'desc'))
            ->get();
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'duration' => 'required|date_format:H:i',
        ]);
        BreakTime::query()->create($request->only('name', 'duration'));

        return created_responses('break_time');
    }


    public function show(BreakTime $breakTime)
    {
        return $breakTime;
    }


    public function update(Request $request, BreakTime $breakTime)
    {
        $request->validate([
            'name' => 'required|string',
            'duration' => 'required|date_format:H:i',
        ]);
        $breakTime->update($request->only('name', 'duration'));

        return updated_responses('break_time');
    }


    public function destroy(BreakTime $breakTime)
    {
        try {
            $breakTime->delete();
        } catch (\Exception $e) {
            throw new GeneralException(__t('can_not_delete_used_break_time'));
        }

        return deleted_responses('break_time');
    }
}