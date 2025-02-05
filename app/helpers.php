<?php

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Orchid\Attachment\File;

if (! function_exists('sendTelegramMessage')) {
    function sendTelegramMessage($text): PromiseInterface|Response
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(sprintf(env('TELEGRAM_API_ENDPOINT'), env('TELEGRAM_BOT_TOKEN')), [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'parse_mode' => 'MarkdownV2',
            'text' => escapeCharacters($text),
        ]);
        Log::info('TELEGRAM_API_RESPONSE: '.$response);

        return $response;
    }

    function escapeCharacters($text): string
    {
        $includedChars = ['_', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];

        foreach ($includedChars as $char) {
            $text = str_replace($char, '\\'.$char, $text);
        }

        return $text;
    }
}
if (! function_exists('makeOrderAgreement')) {
    function makeOrderAgreement(\Illuminate\Database\Eloquent\Model $order)
    {
        $aggremeentName = 'aggreement_'.$order->id.'.pdf';

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdfs.aggreement', compact('order'));

        $pdf->save($aggremeentName, 'public');
        $file = new UploadedFile(storage_path('app/public').'/'.$aggremeentName, $aggremeentName);
        $attachment = (new File($file))->load();

        return $attachment;
    }
}
