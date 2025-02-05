<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use League\Flysystem\FilesystemException;
use Orchid\Attachment\File;

class ProfileController extends Controller
{
    public function viewProfile(Request $request)
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $client->order_amount = Order::query()
            ->where('client_id', '=', $client->id)
            ->whereNotIn('status', ['cancelled', 'waiting'])
            ->count();
        $client->total_spent = Order::query()
            ->where('client_id', '=', $client->id)
            ->whereNotIn('status', ['cancelled', 'waiting'])
            ->sum('amount_paid');
        $client->order_item_amount = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('client_id', '=', $client->id)
            ->whereNotIn('order_items.status', ['cancelled', 'waiting'])
            ->count();

        return view('profile.main', compact('client'));
    }

    public function editProfile(Request $request): Factory|\Illuminate\Foundation\Application|View|Application
    {
        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        return view('profile.edit', compact('client'));
    }

    /**
     * @throws FilesystemException
     * @throws ValidationException
     */
    public function updateProfile(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|Application|\Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:15',
                Rule::unique('clients')->ignore(Auth::guard('clients')->id()),
            ],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('clients')->ignore(Auth::guard('clients')->id()),
            ],
            'instagram' => [
                'required',
                'string',
                Rule::unique('clients')->ignore(Auth::guard('clients')->id()),
            ],
            'files' => 'required|array|min:1|max:2',
            'signature' => 'required|extensions:pdf|file',
        ]);

        $client = Client::query()->find(Auth::guard('clients')->id());

        if (is_null($client)) {
            \Log::info($request->path());
return redirect(route('logout'));
        }

        $client->update(
            [
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'instagram' => $request->input('instagram'),
                'updated_at' => now()->tz('Asia/Almaty'),
            ]
        );

        $attachments = $client->attachment();
        $attachments->detach();

        $attachmentIds = [];

        foreach ($request->file('files') as $fileData) {
            $file = new File($fileData);
            $attachment = $file->path('idCards')->load();
            $attachment->group = 'idCards';
            $attachment->save();
            $attachmentIds[] = $attachment->id;
        }

        $signature = new File($request->file('signature'));
        $attachment = $signature->path('signatures')->load();
        $attachment->group = 'signatures';
        $attachment->save();

        $attachmentIds[] = $attachment->id;

        $client->attachment()->syncWithoutDetaching($attachmentIds);

        return redirect(route('viewProfile'));
    }
}
