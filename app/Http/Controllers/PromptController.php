<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function getAll()
    {
        $prompts = Prompt::where('user_id', auth()->user()->id)->orWhere('show_for_all', true)->get();
        $favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();

        return view('panel.user.openai_chat.components.prompt_library_list', ['promptData' => $prompts, 'favData' => $favourites])->render();
    }

    public function addNew(Request $req)
    {
        $req->validate([
            'title'  => 'required',
            'prompt' => 'required',
        ]);

        $showForAll = $req->has('show_for_all');

        $title = $req->title;
        $prompt = $req->prompt;

        $prompt_record = new Prompt([
            'user_id'      => auth()->user()->id,
            'title'        => $title,
            'prompt'       => $prompt,
            'show_for_all' => $showForAll,
        ]);

        $prompt_record->save();

        $prompts = Prompt::where('user_id', auth()->user()->id)->get();
        $favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();

        return view('panel.user.openai_chat.components.prompt_library_list', ['promptData' => $prompts, 'favData' => $favourites])->render();
    }

    public function updateFav(Request $req)
    {
        $id = $req->id;

        $favourites = Favourite::where('type', 'prompt')
            ->where('item_id', $id)
            ->where('user_id', auth()->user()->id)
            ->get();

        if ($favourites->count() != 0) {
            $favourites->each->delete();
        } else {
            $favourite = new Favourite;
            $favourite->user_id = auth()->user()->id;
            $favourite->type = 'prompt';
            $favourite->item_id = $id;
            $favourite->save();
        }

        $favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();

        return response()->json($favourites);
    }

    public function deletePrompt(Request $req)
    {
        $id = $req->id;
        Prompt::where('id', $id)->delete();

        return response()->json(['status' => 'success']);
    }
}
