<?php

namespace App\Http\Controllers\Chatbot;

use App\Helpers\Classes\Helper;
use App\Helpers\Classes\OpenAiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chatbot\ChatbotTrainingRequest;
use App\Models\Chatbot\Chatbot;
use App\Models\Chatbot\ChatbotData;
use App\Models\Chatbot\ChatbotDataVector;
use App\Services\Chatbot\LinkCrawler;
use App\Services\Chatbot\ParserExcelService;
use App\Services\Chatbot\ParserService;
use Illuminate\Http\Request;

class ChatbotTrainingController extends Controller
{
    public function qa(Request $request, Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
                'content' => view('panel.admin.chatbot.particles.qa.list', [
                    'items' => $chatbot->data()->where('type', 'qa')->get(),
                ])->render(),
            ]);
        }

        $request->validate([
            'question' => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);

        $id = $request->get('qa_id');

        $chatBotData = ChatBotData::query()->where('id', $id)->first();

        if ($chatBotData) {
            $chatBotData->update([
                'type_value' => $request->get('question'),
                'content'    => $request->get('answer'),
                'status'     => 'waiting',
            ]);

            ChatbotDataVector::query()->where('chatbot_data_id', $id)->delete();
        } else {
            ChatBotData::query()->firstOrCreate([
                'chatbot_id' => $chatbot->getAttribute('id'),
                'type'       => 'qa',
                'type_value' => $request->get('question'),
            ], [
                'content' => $request->get('answer'),
                'status'  => 'waiting',
            ]);
        }

        return response()->json([
            'content' => view('panel.admin.chatbot.particles.qa.list', [
                'items' => $chatbot->data()->where('type', 'qa')->get(),
            ])->render(),
            'message' => trans('Qa uploaded successfully.'),
            'count'   => $chatbot->data()->where('type', 'qa')->count(),
        ]);
    }

    public function text(Request $request, Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
                'content' => view('panel.admin.chatbot.particles.text.list', [
                    'items' => $chatbot->data()->where('type', 'text')->get(),
                ])->render(),
            ]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'text'  => 'required|string',
        ]);

        $id = $request->get('text_id');

        $chatBotData = ChatBotData::query()->where('id', $id)->first();

        if ($chatBotData) {
            $chatBotData->update([
                'type_value' => $request->get('title'),
                'content'    => $request->get('text'),
                'status'     => 'waiting',
            ]);

            ChatbotDataVector::query()->where('chatbot_data_id', $id)->delete();
        } else {
            ChatBotData::query()->firstOrCreate([
                'chatbot_id' => $chatbot->getAttribute('id'),
                'type'       => 'text',
                'type_value' => $request->get('title'),
            ], [
                'content' => $request->get('text'),
                'status'  => 'waiting',
            ]);
        }

        return response()->json([
            'content' => view('panel.admin.chatbot.particles.text.list', [
                'items' => $chatbot->data()->where('type', 'text')->get(),
            ])->render(),
            'message' => trans('Text uploaded successfully.'),
            'count'   => $chatbot->data()->where('type', 'text')->count(),
        ]);
    }

    public function uploadPdf(Request $request, Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
                'content' => view('panel.admin.chatbot.particles.pdf.list', [
                    'items' => $chatbot->data()->where('type', 'pdf')->get(),
                ])->render(),
            ]);
        }

        $request->validate([
            'file' => 'required|mimes:pdf,xls,xlsx,csv',
        ]);

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();

        $defaultDisk = 'public';

        $path = $file->store('chatbot', ['disk' => $defaultDisk]);

        $name = $file->getClientOriginalName();

        $storagePath = config('filesystems.disks.' . $defaultDisk . '.root') . '/' . $path;

        if ($extension === 'xls' || $extension === 'xlsx' || $extension === 'csv') {
            $parser = app(ParserExcelService::class);

            $parser->setPath($storagePath)->parse();

        } else {
            $parser = app(ParserService::class);

            $parser->setPdfPath($storagePath)->parse();
        }

        ChatBotData::query()->firstOrCreate([
            'chatbot_id' => $chatbot->getAttribute('id'),
            'type'       => 'pdf',
            'type_value' => $name,
        ], [
            'content' => $parser->getText(),
            'status'  => 'waiting',
            'path'    => $path,
        ]);

        return response()->json([
            'content' => view('panel.admin.chatbot.particles.pdf.list', [
                'items' => $chatbot->data()->where('type', 'pdf')->get(),
            ])->render(),
            'message' => trans('Pdf file uploaded successfully.'),
        ]);
    }

    public function getWebSites(Request $request, Chatbot $chatbot)
    {
        return response()->json([
            'content' => view('panel.admin.chatbot.particles.web-site.crawler', [
                'items' => $chatbot->data()->where('type', 'url')->get(),
            ])->render(),
        ]);
    }

    public function postWebSites(Request $request, Chatbot $chatbot)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
                'content' => view('panel.admin.chatbot.particles.web-site.crawler', [
                    'items' => $chatbot->data()->where('type', 'url')->get(),
                ])->render(),
            ]);
        }
        $request->validate([
            'url' => 'required|url',
        ]);

        $single = $request->input('type') == 'single';

        $crawler = new LinkCrawler($request->input('url'));

        $crawler->crawl($single);

        $content = $crawler->getContents();

        if (! mb_check_encoding($content, 'UTF-8')) {
            // Convert the content to UTF-8 encoding if needed
            $content = mb_convert_encoding($content, 'UTF-8');
        }

        foreach ($content as $url => $data) {
            ChatBotData::query()->firstOrCreate([
                'chatbot_id' => $chatbot->getAttribute('id'),
                'type'       => 'url',
                'type_value' => $url,
            ], [
                'content' => $data,
                'status'  => 'waiting',
            ]);
        }

        return response()->json([
            'content' => view('panel.admin.chatbot.particles.web-site.crawler', [
                'items' => $chatbot->data()->where('type', 'url')->get(),
            ])->render(),
            'message' => trans('Web sites added successfully.'),
        ]);
    }

    public function training(
        ChatbotTrainingRequest $request,
        Chatbot $chatbot
    ) {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $selected = $request->input('chatbot_data') ?: [];

        $data = $this->chatbotData($chatbot, $selected)->toArray();

        OpenAiHelper::embeddingData(
            $chatbot->getAttribute('id'),
            $data,
            $chatbot->trainingData()->pluck('id')->toArray()
        );

        $type = $request->get('type');

        $matchView = match ($type) {
            'url'  => 'panel.admin.chatbot.particles.web-site.crawler',
            'pdf'  => 'panel.admin.chatbot.particles.pdf.list',
            'text' => 'panel.admin.chatbot.particles.text.list',
            'qa'   => 'panel.admin.chatbot.particles.qa.list',
        };

        $chatbot->update([
            'status' => 'trained',
        ]);

        return response()->json([
            'content' => view($matchView, [
                'items' => $chatbot->data()->where('type', $request->get('type'))->get(),
            ])->render(),
            'message' => trans('Training Completed Successfully.'),
        ]);
    }

    public function deleteItem(Chatbot $chatbot, $id)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $chatbot->data()->where('id', $id)->delete();

        ChatbotDataVector::query()->where('chatbot_data_id', $id)->delete();

        return response()->json([
            'message' => trans('Item deleted successfully.'),
        ]);
    }

    public function chatbotData(Chatbot $chatbot, ?array $data = null)
    {
        return ChatBotData::query()
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->where('type', request('type'))
            ->when($data, function ($query) use ($data) {
                return $query->whereIn('id', $data);
            })
            ->get()
            ->map(function ($item) {

                $content = $item->getAttribute('type') == 'qa'
                    ? "When you receive the following question or a similar one, answer it like this: '" . $item->getAttribute('content') . "' \n Question: '" . $item->getAttribute('type_value') . "'"
                    : $item->getAttribute('content');

                return [
                    'id'      => $item->getAttribute('id'),
                    'content' => $content,
                ];
            })
            ->pluck('content', 'id');
    }
}
