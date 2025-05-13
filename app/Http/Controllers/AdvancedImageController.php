<?php

namespace App\Http\Controllers;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Extensions\PhotoStudio\System\Services\PhotoStudioService;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Models\OpenAIGenerator;
use App\Models\Usage;
use App\Models\UserOpenai;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdvancedImageController extends Controller
{
    public function index(): View
    {
        $openai = OpenAIGenerator::whereSlug('ai_image_generator')->firstOrFail();
        $userOpenai = UserOpenai::query()
            ->where('openai_id', $openai->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('panel.user.advanced-image.index', compact(['userOpenai', 'openai']));
    }

    public function editor(Request $request)
    {
        if ($request->get('selected_tool') !== 'sketch_to_image') {
            $request->validate([
                'uploaded_image' => 'required|file|image|mimes:jpeg,png,jpg',
            ]);
        }

        if ($request->get('ai_model') == 'clipdrop' && ! MarketplaceHelper::isRegistered('photo-studio')) {
            return response()->json([
                'message' => __('Photo studio is not installed yet'),
                'status'  => 'error',
            ]);
        }

        $driver = Entity::driver(EntityEnum::CLIPDROP)->inputImageCount(1)->calculateCredit();

        $driver->redirectIfNoCreditBalance();

        $service = new PhotoStudioService;

        if ($request->get('selected_tool') !== 'sketch_to_image') {
            $photo = $service
                ->setAction($request->input('selected_tool'))
                ->setPhoto($request->file('uploaded_image'))
                ->generate();
        } else {
            $photo = $service
                ->setAction($request->input('selected_tool'))
                ->setPhoto($request->file('sketch_file'))
                ->generate();
        }

        $openai = OpenAIGenerator::query()->where('slug', 'ai_image_generator')->firstOrFail();

        if (is_string($photo)) {
            $data = [
                'team_id'   => auth()->user()->team_id,
                'title'     => str_replace('photo-studio/', '', $photo),
                'slug'      => Str::random(7) . Str::slug(auth()->user()?->fullName()) . '-workbook',
                'user_id'   => auth()->user()->id,
                'openai_id' => $openai->id,
                'input'     => $request->get('description') ?? 'Unknown',
                'response'  => 'CD',
                'output'    => Helper::parseUrl(config('app.url'), 'uploads', $photo),
                'hash'      => Str::random(256),
                'credits'   => 1,
                'words'     => 0,
                'storage'   => 'public',
                'payload'   => $request->input('selected_tool'),
            ];

            UserOpenai::query()->create($data);

            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();

            return response()->json([
                'message' => __('Generated Successfully'),
                'status'  => 'success',
                'data'    => $data,
            ]);

        }

        if (! $photo['status']) {
            return response()->json([
                'status'  => 'error',
                'message' => $photo['message'],
            ]);
        }
    }
}
