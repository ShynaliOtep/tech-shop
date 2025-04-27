<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    public function list(Request $request)
    {
        $carousels = Carousel::query()->orderBy('order')->get();

        $result = [];
        foreach ($carousels as $carousel) {
            $result[] = [
                'id' => $carousel->id,
                'title' => $carousel->title,
                'short_text' => $carousel->short_text,
                'image' => $carousel->image,
                'text' => $carousel->text,
            ];
        }
        return response()->json([
            'data' => $result,
            'status' => 'success',
        ]);
    }
}
