<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ConfirmationMail;
use App\Mail\RestorePasswordMail;
use App\Models\Client;
use App\Models\Wanted;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Orchid\Attachment\File;

class AuthController extends Controller
{
    public function login(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->only('phone', 'password');
        $client = Client::query()->where('phone', '=', $credentials['phone'])->first();
        if (is_null($client)) {
            return redirect()->back()->withErrors(['phone' => 'Неверно введены данные']);
        }

        $isConfirmed = $client->email_confirmed;
        $isBlocked = $client->blocked;

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            Auth::guard('clients')->logout();

            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }

        if ($isBlocked) {
            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }
        if (! $isConfirmed) {
            return redirect()->back()->withErrors(['authentication' => 'Сперва нужно подтвердить свой почтовый адрес']);
        }
        if (Auth::guard('clients')->attempt($credentials)) {
            return redirect()->intended('');
        }

        return redirect()->back()->withErrors(['phone' => 'Неверно введены данные']);
    }

    public function register(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('auth.register');
    }

    public function needConfirmation()
    {
        return view('auth.neededConfirmation');
    }

    public function storeUser(Request $request): \Illuminate\Foundation\Application|Redirector|RedirectResponse|Application
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:clients',
            'email' => 'required|string|email|unique:clients',
            'iin' => 'required|string|digits:12',
            'instagram' => 'required|string|unique:clients',
            'password' => 'required|string|min:8|confirmed',
            'files' => 'required|array|min:1|max:2',
        ]);

        $client = Client::query()->make([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'iin' => $request->input('iin'),
            'instagram' => $request->input('instagram'),
            'password' => Hash::make($request->input('password')),
            'confirmation_code' => Str::random(10),
        ]);

        $wanted = Wanted::query()
            ->orWhere('iin', '=', $client->iin)
            ->first();

        if ($wanted) {
            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }

        $attachmentIds = [];

        $client->save();

        foreach ($request->file('files') as $fileData) {
            $file = new File($fileData);
            $attachment = $file->path('idCards')->load();
            $attachment->group = 'idCards';
            $attachment->save();
            $attachmentIds[] = $attachment->id;
        }

        $client->attachment()->syncWithoutDetaching($attachmentIds);
        Mail::to($client->email)->send(new ConfirmationMail($client->email, $client->confirmation_code));

        return redirect(route('needConfirmation'));
    }

    public function logout(Request $request)
    {
        Auth::guard('clients')->logout();
        $request->session()->invalidate();

        return redirect('/auth/login');
    }

    public function confirmEmail(Request $request, string $confirmationString)
    {
        try {
            $confirmationStringSplit = explode('pixelrental', $confirmationString);
            $clientEmail = $confirmationStringSplit[0];
            $clientCode = $confirmationStringSplit[1];
            $client = Client::query()->where('email', '=', $clientEmail)->firstOrFail();
            if ($client->confirmation_code === $clientCode) {
                $client->email_confirmed = true;
                $client->confirmation_code = '@@@@@@';
                $client->save();

                return view('auth.emailConfirmed');
            }

            return view('auth.invalidConfirmationLink');
        } catch (\Exception) {
            return view('auth.invalidConfirmationLink');
        }
    }

    public function forgotPassword(): Factory|\Illuminate\Foundation\Application|View|Application
    {
        return view('auth.forgotPassword');
    }

    public function forgotPasswordPost(ForgotPasswordRequest $request)
    {
        $iin = $request->input('iin');
        $client = Client::query()->where('iin', '=', $iin)->where('blocked', '=',false)->first();
        if (!$client) {
            return redirect()->back()->withErrors(['authentication' => __('translations.No user iin exists')]);
        }

        if (Wanted::query()->where('iin', '=', $iin)->exists()) {
            return redirect()->back()->withErrors(['authentication' => 'Профиль был заблокирован']);
        }

        $token = Str::random() . $client->email;
        Cache::put(Client::RESET_PASSWORD_CACHE_KEY . $token, $iin, now()->addMinutes(20));
        Mail::to($client->email)->send(new RestorePasswordMail($client->iin, $token));
        return redirect()->back()->with(['message' => __('translations.We sent a link to reset password')]);
    }

    public function resetPassword(Request $request, string $token)
    {
        $confirmationStringSplit = explode('pixelrental', $token);
        $iin = Cache::pull(Client::RESET_PASSWORD_CACHE_KEY . $confirmationStringSplit[1]);
        if ($iin !== $confirmationStringSplit[0] || !$iin) {
            return view('auth.invalidConfirmationLink');
        }
        $client = Client::query()->where('iin', '=', $iin)->first();
        if (!$client || $client->blocked) {
            return view('auth.userNotFound');
        }

        return view('auth.resetPassword', ['email' => $client->email]);
    }

    public function resetPasswordPost(ResetPasswordRequest $request)
    {
        $client = Client::query()->where('email', '=', $request->input('email'))->first();
        $client->password = Hash::make($request->input('password'));
        $client->save();

        return redirect()->route('login');
    }
}
