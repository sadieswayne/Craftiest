<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Classes\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Models\UserOpenai;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class DocumentsApiController extends Controller
{
    protected $settings;

    protected $settings_two;

    public function __construct()
    {
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();

        ApiHelper::setOpenAiKey();
        set_time_limit(120);
    }

    /**
     * Get the last 10 recent documents for the authenticated user.
     *
     * @OA\Get(
     *      path="/api/documents/recent",
     *      operationId="getRecentDocs",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Get the last 10 recent documents",
     *      description="Get the last 10 recent documents for the authenticated user, excluding documents of type 'image'.",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *  )
     */
    public function getRecentDocs(Request $request)
    {
        $documents = $request->user()
            ->openai()
            // ->whereHas('generator', function ($query) {    // Disabled with AI Image module
            //     $query->where('type', '!=', 'image');
            // })
            ->latest('created_at')
            ->take(10)
            ->get();

        return response()->json($documents, 200);
    }

    /**
     * Gets all AI Generators
     *
     * @OA\Get(
     *      path="/api/documents/all-openai",
     *      operationId="getOpenAIList",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Gets all AI Generators",
     *      description="Gets all AI Generators",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *  )
     */
    public function getOpenAIList(Request $request)
    {
        $aiList = OpenAIGenerator::all();

        return response()->json($aiList, 200);
    }

    /**
     * Gets all AI Filters
     *
     * @OA\Get(
     *      path="/api/documents/openai-filters",
     *      operationId="getOpenAIFilters",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Gets all AI Filters",
     *      description="Gets all AI Filters",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *  )
     */
    public function getOpenAIFilters(Request $request)
    {
        $List = OpenaiGeneratorFilter::orderBy('name', 'asc')->get();

        return response()->json($List, 200);
    }

    /**
     * Gets documents for the authenticated user.
     *
     * @OA\Get(
     *      path="/api/documents/",
     *      operationId="getDocs",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Gets documents",
     *      description="Gets documents for the authenticated user, excluding documents of type 'image'.",
     *
     *      @OA\Parameter(
     *          name="search",
     *          description="Search string.",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Parameter(
     *          name="filter",
     *          description="Filter string defined in the 'name' column of the 'openai_filters' table.",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(type="string", default="all"),
     *      ),
     *
     *      @OA\Parameter(
     *          name="sort",
     *          description="Sort string. Values: 'newest', 'oldest', 'az', 'za'",
     *          in="query",
     *          required=false,
     *
     *          @OA\Schema(type="string", default="newest"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     *  )
     */
    public function getDocs(Request $request)
    {
        $filter = 'all';
        $sort = 'newest';
        $search = '';

        if ($request->search != null) {
            $search = $request->search;
        }
        if ($request->filter != null) {
            $filter = $request->filter;
        }
        if ($request->sort != null) {
            if (in_array($request->sort, ['newest', 'oldest', 'az', 'za']) == false) {
                return response()->json(['error' => __('Invalid sort parameter. Must be one of newest, oldest, az, za')], 412);
            }
            $sort = $request->sort;
        }

        $query = [
            ['user_openai.user_id', '=', $request->user()->id],
            // ['openai.type', '!=', 'image'], // Disabled with AI Image module
        ];

        if ($filter != 'all' && $filter != 'All') {
            $query[] = ['openai.filters', 'LIKE', '%' . $filter . '%'];
        }

        if ($search != '') {
            $query[] = ['output', 'LIKE', '%' . $search . '%'];
        }

        $sortColumn = 'created_at';
        $sortDirection = 'desc';

        if ($sort == 'oldest') {
            $sortDirection = 'asc';
        } elseif ($sort == 'az') {
            $sortColumn = 'openai.title';
            $sortDirection = 'asc';
        } elseif ($sort == 'za') {
            $sortColumn = 'openai.title';
            $sortDirection = 'desc';
        }

        $documents = UserOpenai::select('user_openai.*', 'openai.title as ai_title', 'openai.image as ai_image', 'openai.color as ai_color')
            ->join('openai', 'openai.id', '=', 'user_openai.openai_id')
            ->where($query)->orderBy($sortColumn, $sortDirection)->paginate($request->input('per_page', 10));

        return response()->json($documents, 200);
    }

    /**
     * Gets single document
     *
     * @OA\Get(
     *      path="/api/documents/doc/{id}",
     *      operationId="getDoc",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Gets single document",
     *      description="Gets document by id, excluding documents of type 'image'.",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the document to get.",
     *          in="path",
     *          required=true,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Document Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     *  )
     */
    public function getDoc(Request $request)
    {
        if ($request->id == null) {
            return response()->json(['error' => __('ID is required')], 412);
        }

        $document = UserOpenai::select('user_openai.*', 'openai.title as ai_title', 'openai.image as ai_image', 'openai.color as ai_color')
            ->join('openai', 'openai.id', '=', 'user_openai.openai_id')
            ->where('user_openai.id', $request->id)->first();

        if ($document == null) {
            return response()->json(['error' => __('Document not found')], 404);
        }

        return response()->json($document, 200);

    }

    /**
     * Updates single document
     *
     * @OA\Post(
     *      path="/api/documents/doc/{id}",
     *      operationId="saveDoc",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Updates single document",
     *      description="Updates document by id, excluding documents of type 'image'.",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the document to get.",
     *          in="path",
     *          required=true,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Parameter(
     *          name="output",
     *          description="Html Output of the document.",
     *          in="query",
     *          required=true,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Parameter(
     *          name="title",
     *          description="Title of the document.",
     *          in="query",
     *          required=true,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Document Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     *  )
     */
    public function saveDoc(Request $request)
    {
        if ($request->id == null) {
            return response()->json(['error' => __('ID is required')], 412);
        }
        if ($request->output == null) {
            return response()->json(['error' => __('Html Output is required')], 412);
        }
        if ($request->title == null) {
            return response()->json(['error' => __('Title is required')], 412);
        }

        $document = UserOpenai::select('user_openai.*', 'openai.title as ai_title', 'openai.image as ai_image', 'openai.color as ai_color')
            ->join('openai', 'openai.id', '=', 'user_openai.openai_id')
            ->where('user_openai.id', $request->id)->first();

        if ($document == null) {
            return response()->json(['error' => __('Document not found')], 404);
        }

        $document->output = $request->output;
        $document->title = $request->title;
        $document->save();

        return response()->json($document, 200);

    }

    /**
     * Deletes single document
     *
     * @OA\Delete(
     *      path="/api/documents/doc/{id}",
     *      operationId="deleteDoc",
     *      tags={"Documents"},
     *      security={{ "passport": {} }},
     *      summary="Deletes single document",
     *      description="Deletes document by id, excluding documents of type 'image'.",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the document to delete.",
     *          in="path",
     *          required=true,
     *
     *          @OA\Schema(type="string", default=""),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *
     *              @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: User not authenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Document Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     *  )
     */
    public function deleteDoc(Request $request)
    {
        if ($request->id == null) {
            return response()->json(['error' => __('ID is required')], 412);
        }

        $document = UserOpenai::select('user_openai.*', 'openai.title as ai_title', 'openai.image as ai_image', 'openai.color as ai_color')
            ->join('openai', 'openai.id', '=', 'user_openai.openai_id')
            ->where('user_openai.id', $request->id)->first();

        if ($document == null) {
            return response()->json(['error' => __('Document not found')], 404);
        }

        $document->delete();

        return response()->json(['success' => 'Document deleted successfully'], 200);

    }
}
