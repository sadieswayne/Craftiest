<?php

namespace App\Http\Controllers;

use App\Models\Advertis;
use Illuminate\Http\Request;

class AdvertisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('panel.admin.advertis.index')->with([
            'advertises' => Advertis::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Advertis $advertis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($advertis)
    {
        return view('panel.admin.advertis.edit')->with([
            'advertis' => Advertis::find($advertis),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Advertis $advertis)
    {
        $request->validate([
            'title'         => 'required',
            'tracking_code' => 'required|array',
        ]);

        $trackingCode = $request->tracking_code;

        foreach (array_keys($trackingCode) as $key) {
            if (! in_array($key, ['desktop', 'mobile', 'tablet'])) {
                $trackingCode[$key] = null;
            }
        }

        $advertis->title = $request->title;
        $advertis->tracking_code = $trackingCode;
        $advertis->status = $request->has('status');
        $advertis->save();

        return redirect()->route('dashboard.admin.advertis.index')->with([
            'message' => __('Update success!'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Advertis $advertis)
    {
        //
    }
}
