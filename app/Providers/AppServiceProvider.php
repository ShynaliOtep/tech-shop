<?php

namespace App\Providers;

use App\Models\Good;
use App\Models\GoodType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $goodOptions = $this->getGoodOptionsWithAttachments();
        View::share('cartCount', $this->countCartCookie());
        View::share('goodTypes', GoodType::all());
        View::share('goodOptions', $goodOptions);
    }

    private function countCartCookie(): int
    {
        $cookie = Cookie::get('cart');
        if (! is_null($cookie)) {
            $decryptedCookie = Crypt::decryptString($cookie);
            $parts = explode('|', $decryptedCookie);
            $amount = count(json_decode($parts[1], true));

            return $amount;
        }

        return 0;
    }

    private function getGoodOptionsWithAttachments(): Collection
    {
        return Good::with('attachment')->get()->map(function ($good) {
            return [
                'name' => $good['name_'.session()->get('locale', 'ru')],
                'url' => optional($good->attachment->first())->url,
                'id' => $good->id,
            ];
        });
    }
}
