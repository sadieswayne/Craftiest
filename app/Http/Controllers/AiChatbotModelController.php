<?php

namespace App\Http\Controllers;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Models\Entity;
use App\Models\Finance\AiChatModelPlan;
use App\Models\Plan;
use Illuminate\Http\Request;

class AiChatbotModelController extends Controller
{
    public function index()
    {
        $enablesEngines = EngineEnum::whereHasEnabledModels();
        $plans = Plan::query()
            ->where('type', 'subscription')
            ->get();

        return view('panel.admin.chatbot.ai-models', compact('enablesEngines', 'plans'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'selected_title.*' => 'required',
            'selected_plans.*' => 'sometimes',
            'no_plan_users.*'  => 'sometimes',
        ]);

        foreach ($data['selected_title'] as $key => $value) {
            Entity::query()
                ->where('id', $key)
                ->update([
                    'selected_title' => $value,
                ]);
        }

        AiChatModelPlan::query()->delete();

        $selected_plans = $request->input('selected_plans');

        if ($selected_plans) {
            foreach ($selected_plans as $id => $value) {
                foreach ($value as $item) {
                    AiChatModelPlan::query()
                        ->create([
                            'plan_id'     => $item,
                            'entity_id'   => $id,
                        ]);
                }
            }
        }

        Entity::query()->update([
            'is_selected' => false,
        ]);

        $no_plan_users = $request->input('no_plan_users');

        if ($no_plan_users) {
            foreach ($no_plan_users as $key => $value) {
                Entity::query()
                    ->where('id', $key)
                    ->update([
                        'is_selected' => true,
                    ]);
            }
        }

        return redirect()->back()->with([
            'message' => 'AI Models updated successfully',
            'type'    => 'success',
        ]);
    }
}
