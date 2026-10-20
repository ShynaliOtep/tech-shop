<?php

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Orchid\Attachment\File;

if (! function_exists('sendTelegramMessage')) {
    function sendTelegramMessage($text, int $cityId = 1): PromiseInterface|Response
    {
        if ($cityId === 1) {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');
        } else  {
            $botToken = env('TELEGRAM_BOT_TOKEN_ASTANA');
            $chatId = env('TELEGRAM_CHAT_ID_ASTANA');
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(sprintf(env('TELEGRAM_API_ENDPOINT'), $botToken), [
            'chat_id' => $chatId,
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

if (! function_exists('format_price')) {
    /**
     * «5000» → «5 000 ₸». Неразрывный пробел, чтобы цена не переносилась по разрядам.
     */
    function format_price($amount): string
    {
        return number_format((float) $amount, 0, '', "\u{00A0}") . "\u{00A0}₸";
    }
}

if (! function_exists('format_day_price')) {
    /**
     * «5000» → «5 000 ₸/сутки» (или «/day» на английском).
     */
    function format_day_price($amount): string
    {
        return format_price($amount) . '/' . __('translations.per day');
    }
}
