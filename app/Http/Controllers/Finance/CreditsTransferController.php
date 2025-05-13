<?php

namespace App\Http\Controllers\Finance;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Http\Controllers\Controller;
use App\Services\Credits\CreditsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreditsTransferController extends Controller
{
    public ?CreditsService $creditsService;

    public function __construct(CreditsService $creditsService)
    {
        $this->creditsService = $creditsService;
    }

    // Transfer Users Credits (Between Entities)
    public function transferUsersEntityCredits(Request $request): JsonResponse
    {
        $this->validate($request, [
            'oldModel' => 'required|string',
            'newModel' => 'required|string',
        ]);

        $oldDefaultEntity = EntityEnum::fromSlug($request->oldModel);
        $newDefaultEntity = EntityEnum::fromSlug($request->newModel);

        try {
            $this->creditsService
                ->setOldEntity($oldDefaultEntity)
                ->setNewEntity($newDefaultEntity)
                ->moveDefaultEntityCreditsForUsers();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['message' => 'Error transferring users credits from ' . $oldDefaultEntity->value . ' to ' . $newDefaultEntity->value], 500);
        }

        return response()->json(['message' => 'Users credits from ' . $oldDefaultEntity->value . ' transferred to ' . $newDefaultEntity->value . ' successfully']);
    }

    public function transferUsersEntityCreditsOfEngines(Request $request): JsonResponse
    {
        $this->validate($request, [
            'oldEngine' => 'required|string',
            'newEngine' => 'required|string',
        ]);

        $oldEngineEnum = EngineEnum::fromSlug($request->oldEngine);
        $newEngineEnum = EngineEnum::fromSlug($request->newEngine);
        $isAW = filter_var($request->isAW, FILTER_VALIDATE_BOOLEAN);

        try {
            $this->creditsService
                ->setOldEngine($oldEngineEnum)
                ->setNewEngine($newEngineEnum)
                ->setAW($isAW)
                ->moveDefaultEngineCreditsForUsers();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['message' => 'Error transferring users credits from ' . $oldEngineEnum->value . ' to ' . $newEngineEnum->value], 500);
        }

        return response()->json(['message' => 'Users credits from ' . $oldEngineEnum->value . ' transferred to ' . $newEngineEnum->value . ' successfully']);
    }

    public function transferPlansEntityCredits(Request $request): JsonResponse
    {
        $this->validate($request, [
            'oldModel' => 'required|string',
            'newModel' => 'required|string',
        ]);

        $oldDefaultEntity = EntityEnum::fromSlug($request->oldModel);
        $newDefaultEntity = EntityEnum::fromSlug($request->newModel);

        try {
            $this->creditsService
                ->setOldEntity($oldDefaultEntity)
                ->setNewEntity($newDefaultEntity)
                ->moveDefaultEntityCreditsForPlans();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['message' => 'Error transferring plans credits from ' . $oldDefaultEntity->value . ' to ' . $newDefaultEntity->value], 500);
        }

        return response()->json(['message' => 'Plans credits transferred successfully from ' . $oldDefaultEntity->value . ' to ' . $newDefaultEntity->value]);
    }

    public function transferPlansEntityCreditsOfEngines(Request $request): JsonResponse
    {
        $this->validate($request, [
            'oldEngine' => 'required|string',
            'newEngine' => 'required|string',
        ]);

        $oldEngineEnum = EngineEnum::fromSlug($request->oldEngine);
        $newEngineEnum = EngineEnum::fromSlug($request->newEngine);
        $isAW = filter_var($request->isAW, FILTER_VALIDATE_BOOLEAN);

        try {
            $this->creditsService
                ->setOldEngine($oldEngineEnum)
                ->setNewEngine($newEngineEnum)
                ->setAW($isAW)
                ->moveDefaultEngineCreditsForPlans();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['message' => 'Error transferring plans credits from ' . $oldEngineEnum->value . ' to ' . $newEngineEnum->value], 500);
        }

        return response()->json(['message' => 'Plans credits transferred successfully from ' . $oldEngineEnum->value . ' to ' . $newEngineEnum->value]);
    }
}
