<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneCountryCode;
use App\Models\Policy;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $phoneCodes = PhoneCountryCode::orderBy('name')->get();

        return view('auth.register', compact('phoneCodes'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_code' => ['required', 'string', 'max:10'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        $phone = '+'.$request->phone_code.$request->phone_number;

        $user = User::create([
            'name' => trim($request->first_name.' '.$request->last_name),
            'email' => $request->email,
            'phone' => $phone,
            'whatsapp_number' => $phone,
            'password' => Hash::make($request->password),
        ]);

        // Record acceptance for all policies required at registration
        $registrationPolicies = Policy::forRegistration()->where('require_acceptance', true)->get();
        foreach ($registrationPolicies as $policy) {
            $policy->recordAcceptance($user, 'registration', $request->ip(), $request->userAgent());
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
