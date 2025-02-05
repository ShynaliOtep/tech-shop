<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\GoodType;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class GoodController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $viewedGoodTypes = GoodType::query()
            ->with('goods.attachment')->get();

        return view('good', compact('viewedGoodTypes'));
    }

    public function view(Good $good)
    {
        $good->with('relatedGoods');

        return view('goodView', compact('good'));
    }

    public function goodList(string $goodTypeCode, Request $request): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        $viewedGoodTypes = GoodType::query()->where('code', '=', $goodTypeCode)
            ->with('goods.attachment')->get();

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
}
