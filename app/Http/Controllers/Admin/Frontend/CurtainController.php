<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Frontend\Curtain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CurtainController extends Controller
{
    public function index()
    {
        $items = Curtain::query()->get();

        return view('default.panel.admin.frontend.curtain.index', compact('items'));
    }

    public function edit(Curtain $curtain)
    {
        return view('default.panel.admin.frontend.curtain.form', [
            'item' => $curtain,
        ]);
    }

    public function update(Request $request, Curtain $curtain): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|max:191',
            'title_icon'  => 'sometimes',
            'sliders'     => 'array',
        ]);

        $curtain->update([
            'title'       => $data['title'],
            'title_icon'  => $data['title_icon'],
        ]);

        $sliders = $curtain['sliders'];

        $sliderData = [];
        foreach ($data['sliders'] as $key => $slider) {

            $sliders[$key]['title'] = $curtain['title'];

            if (isset($slider['bg_image']) && $slider['bg_image'] instanceof UploadedFile) {
                $sliders[$key]['bg_image'] = '/uploads/' . $slider['bg_image']->store('curtains', 'uploads');
            }

            if (isset($slider['bg_video']) && $slider['bg_video'] instanceof UploadedFile) {
                $sliders[$key]['bg_video'] = '/uploads/' . $slider['bg_video']->store('curtains', 'uploads');
            }

            $sliders[$key]['description'] = $slider['description'];

            if (isset($slider['bg_color'])) {
                $sliders[$key]['bg_color'] = $slider['bg_color'];
            }

            if (isset($slider['title_color'])) {
                $sliders[$key]['title_color'] = $slider['title_color'];
            }
            if (isset($slider['description_color'])) {
                $sliders[$key]['description_color'] = $slider['description_color'];
            }
        }

        $curtain->update(['sliders' => $sliders]);

        return back()->with([
            'type'    => 'success',
            'message' => __('The curtain was successfully updated'),
        ]);
    }
}
