<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;
use App\Services\DeFi\DeFiNewsService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class DeFiNewsController extends Controller
{
    public function __construct(
        public DeFiNewsService $service
    ) {}

    public function __invoke(Request $request)
    {
        $data = $this->service->get();

        if ($data) {
            $data = $this->paginate(
                $data,
                8,
                null,
                ['path' => $request->url(), 'query' => $request->query()]);

            return view('panel.user.defi.news.index', [
                'data' => $data,
            ]);
        }
    }

    public function show(string $slug)
    {
        $item = $this->service->find($slug);

        if ($item) {
            return view('panel.user.defi.news.single', [
                'title' => 'News Title',
                'item'  => $item,
            ]);
        }
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
