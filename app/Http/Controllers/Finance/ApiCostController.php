<?php

namespace App\Http\Controllers\Finance;

use App\Domains\Entity\Models\Entity;
use App\Enums\StatusEnum;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiCostController extends Controller
{
    public function index()
    {
        $entities = Entity::with('tokens')->where('status', StatusEnum::ENABLED)->get();

        $groupedAiModels = $entities->groupBy('engine');

        return view('panel.admin.finance.api-cost.index', compact('groupedAiModels'));
    }

    public function update(Request $request)
    {
        if (Helper::appIsDemo()) {
            return redirect()->back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }
        $data = $request->except('_token');
        foreach ($data as $aiModelId => $costPerToken) {
            $aiModel = Entity::find($aiModelId);
            if ($aiModel) {
                $aiModel->tokens()->update(['cost_per_token' => $costPerToken]);
            }
        }

        return redirect()->route('dashboard.admin.finance.api-cost-management.index');
    }
}
