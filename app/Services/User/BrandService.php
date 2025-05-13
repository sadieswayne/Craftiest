<?php

namespace App\Services\User;

use App\Http\Requests\User\Brand\BrandRequest;
use App\Models\Company;
use App\Models\Product;

class BrandService
{
    public function create(BrandRequest $request): Company
    {
        $company = Company::query()->create($this->data($request));

        $this->image($company, $request);

        $this->product($company, $request);

        return $company;
    }

    public function update(BrandRequest $request, Company $company): Company
    {
        $company->update($this->data($request));

        $this->product($company, $request);

        $this->image($company, $request);

        return $company;
    }

    public function image(Company $company, BrandRequest $request): void
    {
        if ($request->hasFile('c_logo')) {
            $request->validate(['c_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096']);

            $logo = $request->file('c_logo')->store('', ['disk' => 'public']);

            $company->update(['logo' => $logo]);
        }
    }

    public function product(Company $company, BrandRequest $request): void
    {
        $inputNames = array_filter($request['inputNames']);
        $inputFeatures = $request['inputFeatures'];
        $inputTypes = $request['inputTypes'];

        foreach ($inputNames as $key => $inputName) {
            Product::query()
                ->updateOrCreate([
                    'name'       => $inputName,
                    'user_id'    => $company->user_id,
                    'company_id' => $company->id,
                ], [
                    'type'         => $inputTypes[$key] ?? 3,
                    'key_features' => $inputFeatures[$key] ?? null,
                ]);
        }

        Product::query()
            ->where('user_id', $company->user_id)
            ->where('company_id', $company->id)
            ->whereNotIn('name', $inputNames)
            ->delete();
    }

    public function data(BrandRequest $request)
    {
        return $request->only([
            'name',
            'industry',
            'description',
            'website',
            'tagline',
            'brand_color',
            'user_id',
            'tone_of_voice',
            'target_audience',
        ]);
    }
}
