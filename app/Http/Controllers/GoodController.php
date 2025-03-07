<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Good;
use App\Models\GoodType;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class GoodController extends Controller
{
    private $cityId;
    public function __construct()
    {
        $cityId = Session::get('selected_city', Cookie::get('selected_city'));
        $this->cityId = $cityId ?: City::DEFAULT;
    }

    public function index(Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $cityId = $this->cityId;
        $viewedGoodTypes = GoodType::query()
            ->with([
                'goods' => function ($query) use ($cityId) {
                    $query->whereHas('items', function ($itemQuery) use ($cityId) {
                        $itemQuery->where('status', 'available')
                            ->where('city_id', $cityId);
                    });
                },
                'goods.attachment'
            ])
            ->get();

        return view('good', compact('viewedGoodTypes'));
    }

    public function view(Good $good)
    {
        $good->with('relatedGoods');

        return view('goodView', compact('good'));
    }

    public function goodList(string $goodTypeCode, Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $cityId = $this->cityId;
        $viewedGoodTypes = GoodType::query()->where('code', '=', $goodTypeCode)
            ->with([
                'goods' => function ($query) use ($cityId) {
                    $query->whereHas('items', function ($itemQuery) use ($cityId) {
                        $itemQuery->where('status', 'available')
                            ->where('city_id', $cityId);
                    });
                },
                'goods.attachment'
            ])
            ->get();

        return view('good', compact('viewedGoodTypes'));
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
        return view('categories');
    }
}
