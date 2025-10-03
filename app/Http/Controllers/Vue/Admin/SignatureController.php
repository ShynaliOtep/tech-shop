<?php

namespace App\Http\Controllers\Vue\Admin;

use App\Models\Order;
use Illuminate\Http\Request;

class SignatureController
{
    public function index()
    {
        return view('_v2.pages.admin.signature');
    }

    public function agreement(Request  $request)
    {
        $order = Order::findOrFail($request->get('id'));

        if (!$order->attachment()->first()) {

            $aggreementFile = makeOrderAgreement($order->fresh(['orderItems', 'owner']));

            $order->attachment()->syncWithoutDetaching($aggreementFile->id);
            $order->agreement_id = $aggreementFile->id;

            $order->save();
        }
        return view('_v2.pages.admin.agreement');
    }
}
