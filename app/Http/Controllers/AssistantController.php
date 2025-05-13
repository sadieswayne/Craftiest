<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Services\Assistant\AssistantService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AssistantController extends Controller
{
    public function __construct(protected AssistantService $service) {}

    /**
     * Display a listing of the resource.
     *
     * @throws GuzzleException
     */
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $response = $this->service->listAssistant();
        $assistants = $response['data'] ?? $response ?? [];
        $assistantsCollection = collect($assistants);

        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $assistantsCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $assistantsCollection->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
        ]);

        return view('panel.admin.assistant.index', ['assistants' => $paginatedItems]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws GuzzleException
     */
    public function create(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $assistant = [];
        $models = $this->service->listModels();

        return view('panel.admin.assistant.create', compact('models', 'assistant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws GuzzleException
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'name'         => 'required',
            'model'        => 'required',
            'instructions' => 'required',
            'files.*'      => 'mimes:pdf,doc,docx',
        ]);

        if ($request->get('file_search') === '1') {
            $tools[] = ['type' => 'file_search'];
        }

        if ($request->get('code_interpreter') === '1') {
            $tools[] = ['type' => 'code_interpreter'];
        }

        if ($request->has('files')) {

            $fileIds = [];

            foreach ($request->file('files') as $file) {
                $file = $this->service->createFile($file);
                if (isset($file['id'])) {
                    $fileIds[] = $file['id'];
                }
            }

            $vectorId = $this->service->createVectorStore()['id'];

            foreach ($fileIds as $fileId) {
                $this->service->createVectorStoreFiles($vectorId, $fileId);
            }

            $resources = ['file_search' => ['vector_store_ids' => [$vectorId]]];
        }

        $response = $this->service->createAssistant(
            $request->get('instructions'),
            $request->get('name'),
            $request->get('model'),
            $resources ?? null,
            $tools ?? null,
            $request->get('description'),
            $request->get('temperature'),
            $request->get('top_p'),
        );

        if (isset($response['id'])) {
            return redirect()->route('dashboard.admin.ai-assistant.index')
                ->with(['message' => __('Assistant Created Successfully'), 'type' => 'success']);
        } else {
            return redirect()->back()->with(['message' => $response['message'], 'type' => 'error']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws GuzzleException
     */
    public function edit(string $id): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $models = $this->service->listModels();
        $assistant = $this->service->showAssistant($id);

        $existingFiles = [];
        if (isset($assistant['tool_resources']['file_search']['vector_store_ids'][0])) {
            $vectorId = $assistant['tool_resources']['file_search']['vector_store_ids'][0];
            $vector = $this->service->listVectorStoreFiles($vectorId);

            if (isset($vector['data'])) {
                $vectorFiles = $vector['data'];

                foreach ($vectorFiles as $vectorFile) {
                    $existingFiles[] = $this->service->showFiles($vectorFile['id']);
                }
            }
        }

        return view('panel.admin.assistant.edit',
            ['assistant' => $assistant, 'models' => $models, 'existingFiles' => $existingFiles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws GuzzleException
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $request->validate([
            'name'         => 'sometimes|required',
            'model'        => 'sometimes|required',
            'instructions' => 'sometimes|required',
        ]);

        $tools = [];

        if ($request->get('file_search') === '1') {
            $tools[] = ['type' => 'file_search'];
        }

        if ($request->get('code_interpreter') === '1') {
            $tools[] = ['type' => 'code_interpreter'];
        }

        $assistant = $this->service->showAssistant($id);

        $vectorId = $assistant['tool_resources']['file_search']['vector_store_ids'][0] ?? null;
        $resources = [];

        if ($request->has('existingfiles') && $vectorId) {
            $this->syncExistingFiles($vectorId, $request->get('existingfiles'));
            $resources = ['file_search' => ['vector_store_ids' => [$vectorId]]];
        } else {
            $this->clearVectorStoreFiles($vectorId);
            if ($vectorId) {
                $resources = ['file_search' => ['vector_store_ids' => [$vectorId]]];
            }
        }

        if ($request->has('files')) {
            $vectorId = $vectorId ?? $this->service->createVectorStore()['id'];
            $this->uploadNewFiles($request->file('files'), $vectorId);
            $resources = ['file_search' => ['vector_store_ids' => [$vectorId]]];
        }

        $response = $this->service->updateAssistant(
            $assistant['id'],
            $request->get('instructions'),
            $request->get('name'),
            $request->get('model'),
            $resources,
            $tools,
            $request->get('description'),
            $request->get('temperature'),
            $request->get('top_p')
        );

        if (isset($response['id'])) {
            return redirect()->back()->with(['message' => __('Assistant Trained Successfully'), 'type' => 'success']);
        } else {
            return redirect()->back()->with(['message' => $response['message'], 'type' => 'error']);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function syncExistingFiles(string $vectorId, array $existingFiles): void
    {
        $vectors = $this->service->listVectorStoreFiles($vectorId)['data'];
        $vectorFiles = collect($vectors)->pluck('id')->toArray();
        $removedFiles = array_diff($vectorFiles, $existingFiles);

        foreach ($removedFiles as $removedFile) {
            $this->service->deleteVectorStoreFiles($vectorId, $removedFile);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function clearVectorStoreFiles(?string $vectorId): void
    {
        if ($vectorId) {
            $vectors = $this->service->listVectorStoreFiles($vectorId)['data'];
            foreach ($vectors as $vector) {
                $this->service->deleteVectorStoreFiles($vectorId, $vector['id']);
            }
        }
    }

    /**
     * @throws GuzzleException
     */
    private function uploadNewFiles(array $files, string $vectorId): void
    {
        $fileIds = [];

        foreach ($files as $file) {
            $file = $this->service->createFile($file);
            if (isset($file['id'])) {
                $fileIds[] = $file['id'];
            }
        }

        foreach ($fileIds as $fileId) {
            $this->service->createVectorStoreFiles($vectorId, $fileId);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws GuzzleException
     */
    public function destroy(string $id): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $this->service->deleteAssistant($id);

        return response()->json([
            'message'    => trans('Assistant Deleted Successfully'),
            'reload'     => true,
            'setTimeOut' => 1000,
        ]);

    }
}
