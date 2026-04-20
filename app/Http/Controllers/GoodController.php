<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Good;
use App\Models\GoodType;
use App\Models\Set;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    private $cityId;
    public function __construct()
    {
        $this->cityId = City::getSiteCity();

    }

    public function index(Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $cityId = session()->get('select_city');
        $cityId = $cityId ?: City::DEFAULT;
        $viewedGoodTypes = GoodType::query()
            ->with([
                'goods' => function ($query) use ($cityId) {
                    $query->whereHas('items', function ($itemQuery) use ($cityId) {
                        $itemQuery->where('status', 'available')
                            ->where('city_id', $cityId);
                    });
                    $query->where('is_set', '=', 0);
                },
                'goods.attachment'
            ])
            ->get();

        $news = Good::query()
            ->whereHas('items', function ($itemQuery) use ($cityId) {
                $itemQuery->where('status', 'available')
                    ->where('city_id', $cityId);
            })->with('attachment')
            ->where('is_set', '=', 0)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        $carousel = true;

        $sets = Set::whereHas('goods.items', function ($query) use ($cityId) {
            $query->where('city_id', $cityId)
                ->where('status', 'available');
        })
            ->get();


        return view('_v2.pages.main', compact('viewedGoodTypes', 'news', 'carousel', 'sets'));
    }

    public function view(Good $good)
    {
        $good->with('relatedGoods');

        return view('_v2.pages.good.goodView', compact('good'));
    }

    public function goodList(string $goodTypeCode, Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $cityId = session()->get('select_city');
        $cityId = $cityId ?: City::DEFAULT;

        $goodType = GoodType::where('code', $goodTypeCode)->first();

        $typeIds = $request->types;
        if ($typeIds) {
            $types = GoodType::query()
                ->whereIn('id', $typeIds)
                ->pluck('id');
        } else {
            $types = GoodType::query()
                ->where('id', $goodType->id)
                ->orWhere('parent_id', $goodType->id)
                ->pluck('id');
        }
        $viewedGoodTypes = GoodType::query()
            ->where('code', $goodTypeCode)
            ->get();

//        $goods = Good::query()
//            ->whereHas('items', function ($q) use ($cityId) {
//                $q->where('status', 'available')
//                    ->where('city_id', $cityId);
//            })
//            ->when(!empty($types), function ($q) use ($types) {
//                $q->whereIn('good_type_id', $types);
//            })
//            ->with('attachment')
//
//            ->get();

        $sort = $request->get('sort');

        $goods = Good::query()
            ->whereHas('items', function ($q) use ($cityId) {
                $q->where('status', 'available')
                    ->where('city_id', $cityId);
            })
            ->when(!empty($types), function ($q) use ($types) {
                $q->whereIn('good_type_id', $types);
            })
            ->when($sort === 'cheap', function ($q) {
                $q->orderBy('cost', 'asc');
            })
            ->when($sort === 'expensive', function ($q) {
                $q->orderBy('cost', 'desc');
            })
            ->when($sort === 'popular', function ($q) {
                $q->orderBy('order_count', 'desc'); // или нужное поле
            })
            ->when($sort === null, function ($q) {
                $q->orderBy('created_at', 'desc'); // или нужное поле
            })
            ->with('attachment')
            ->get();

//        $viewedGoodTypes = GoodType::query()->where('code', '=', $goodTypeCode)
//            ->with([
//                'goods' => function ($query) use ($cityId, $types) {
//                    $query->whereHas('items', function ($itemQuery) use ($cityId) {
//                        $itemQuery->where('status', 'available')
//                            ->where('city_id', $cityId);
//                    })
//                    ->whereIn('good_type_id', $types);
//                },
//                'goods.attachment'
//            ])
//            ->get();

        $subTypes= GoodType::where('parent_id', $goodType->id)->get();

        return view('_v2.pages.good.goods', compact('viewedGoodTypes', 'goods', 'subTypes', 'typeIds'));
    }

    public function autofill(string $goodName)
    {
        $good = Good::query()->where('name_ru', '=', $goodName)->first();

        return redirect(route('viewGood', ['good' => $good]));
    }

    public function getAvailableItems(Request $request, int $id)
    {
        $good = Good::query()->find($id);

        $items = $good->items()->with('good')->get();

        foreach ($items as $item){
            $item->good->name = $item->good['name_'.session()->get('locale', 'ru')];
        }

        return response()
            ->json(['available_items' => $items]);
    }

    public function categories(Request $request)
    {
        return view('_v2.pages.categories.categories');
    }
}
