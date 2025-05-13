<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display brand companies
     *
     * @OA\Get(
     *      path="/api/brandvoice",
     *      operationId="index",
     *      tags={"Brand Voice"},
     *      summary="List of companies",
     *      description="Returns list of companies",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of company to get. If null, returns all companies",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function index($id = null)
    {
        if ($id != null) {
            $list = Company::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
            $products = Product::where('user_id', auth()->user()->id)->where('company_id', $id)->get();
            $list->products = $products;

            return response()->json($list);
        }

        $list = Company::where('user_id', auth()->user()->id)->orderBy('name', 'asc')->get();
        foreach ($list as $company) {
            $products = Product::where('user_id', auth()->user()->id)->where('company_id', $company->id)->get();
            $company->products = $products;
        }

        return response()->json($list);

        // return view('panel.user.companies.list', compact('list'));
    }

    /**
     * Delete brand company
     *
     * @OA\Delete(
     *      path="/api/brandvoice",
     *      operationId="delete",
     *      tags={"Brand Voice"},
     *      summary="Delete a company",
     *      description="Delete a company by id",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of company to delete",
     *          required=true,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function delete($id = null)
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }
        Product::where('user_id', auth()->user()->id)->where('company_id', $id)->delete();
        Company::where('id', $id)->where('user_id', auth()->user()->id)->delete();

        return response()->json(['message' => __('Deleted Successfully'), 'type' => 'success']);

        // return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function addOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = Company::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        }

        return view('panel.user.companies.form', compact('item'));
    }

    /**
     * Update or create brand companies
     *
     * @OA\Post(
     *      path="/api/brandvoice",
     *      operationId="store",
     *      tags={"Brand Voice"},
     *      summary="Update / Create a company",
     *      description="Update / Create a company",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function store(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }
        if ($request->item_id != 'undefined') {
            $item = Company::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new Company;
        }

        if ($request->hasFile('c_logo')) {
            $path = 'upload/images/companies/';
            $image = $request->file('c_logo');
            $image_name = Str::random(4) . '-' . Str::slug($request->c_name) . '-logo.' . $image->getClientOriginalExtension();

            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $item->logo = $path . $image_name;
        }

        $item->name = $request->c_name;
        $item->website = $request->c_website;
        $item->tagline = $request->c_tagline;
        $item->description = $request->c_description;
        $item->brand_color = $request->c_color;
        $item->industry = $request->c_industry;
        $item->tone_of_voice = $request->tone_of_voice;
        $item->target_audience = $request->target_audience;

        $inputNames = explode(',', $request->input_name);
        $inputFeatures = explode(',', $request->input_features);
        $inputTypes = explode(',', $request->input_type);

        $item->user_id = auth()->user()->id;
        $item->save();

        foreach ($inputNames as $key => $inputName) {
            if ($request->item_id != 'undefined') {
                $product = Product::where('user_id', auth()->user()->id)->where('company_id', $item->id)->where('name', $inputName)->first();
                if ($product == null) {
                    $product = new Product;
                }
            } else {
                $product = new Product;
            }
            $product->name = $inputName;
            $product->key_features = $inputFeatures[$key];
            $product->type = $inputTypes[$key];
            $product->user_id = auth()->user()->id;
            $product->company_id = $item->id;
            $product->save();
        }
        // delete products that are not in the input
        Product::where('user_id', auth()->user()->id)->where('company_id', $item->id)->whereNotIn('name', $inputNames)->delete();

        return response()->json(['message' => __('Saved Successfully'), 'type' => 'success']);
    }

    public function getProducts($id)
    {
        $products = Product::where('user_id', auth()->user()->id)->where('company_id', $id)->get();

        return response()->json($products);
    }
}
