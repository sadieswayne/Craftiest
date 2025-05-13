<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function index(Request $request)
    {
        $data = Ad::paginate(25);

        return view('panel.admin.adsense.index', compact('data'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'code' => 'required',
        ]);

        $new_ad = new Ad;
        $new_ad->code = request('code');
        $new_ad->status = request('status') == 'on' ? 1 : 0;
        $new_ad->save();

        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad created successfully.'), 'type' => 'success']);
    }

    public function edit(Ad $id)
    {
        return view('panel.admin.adsense.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'code' => 'required',
        ]);

        $ads = Ad::where('id', $id)->firstOrFail();
        $ads->code = request('code');
        $ads->status = request('status') == 'on' ? 1 : 0;
        $ads->save();

        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad updated successfully.'), 'type' => 'success']);
    }

    public function destroy($id)
    {
        $ads = Ad::where('id', $id)->firstOrFail();
        $ads->delete();

        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad deleted successfully.'), 'type' => 'success']);
    }
}
