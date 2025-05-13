<?php

namespace App\Extensions\Introduction\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Extensions\Introduction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntroductionController extends Controller
{
    public function index()
    {
        $list = Introduction::all();

        return view('introduction:index', compact('list'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Introduction::query()->where('key', $key)->update(['intro' => $value]);
        }

        return response()->json([
            'type'    => 'success',
            'message' => 'User Onboarding settings updated successfully',
        ]);
    }
}
