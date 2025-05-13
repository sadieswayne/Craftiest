<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Frontend\ContentBox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContentBoxController extends Controller
{
    public function index()
    {
        $items = ContentBox::query()->get();

        return view('default.panel.admin.frontend.content-box.index', compact('items'));
    }

    public function edit(ContentBox $contentBox)
    {
        return view('default.panel.admin.frontend.content-box.form', [
            'item' => $contentBox,
        ]);
    }

    public function update(Request $request, ContentBox $contentBox): RedirectResponse
    {
        $data = $request->validate([
            'emoji'       => 'required|max:191',
            'title'       => 'required|max:191',
            'description' => 'required|max:2000',
            'background'  => 'required|max:191',
            'foreground'  => 'required|max:191',
        ]);

        $contentBox->update($data);

        return back()->with([
            'type'    => 'success',
            'message' => __('The content box was successfully updated'),
        ]);
    }
}
