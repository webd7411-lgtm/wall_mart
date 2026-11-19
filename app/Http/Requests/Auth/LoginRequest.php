<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim($this->input('email'));
        $password = $this->input('password');
        $credentialEmail = $loginInput;

        // If input is not a direct valid email format, look up rider phone or username
        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $loginInput);

            // 1. Look up Rider by phone number or stripped digits
            if (!empty($cleanDigits)) {
                $rider = \App\Models\Rider::where('phone', $loginInput)
                    ->orWhereRaw("REPLACE(REPLACE(phone, '-', ''), ' ', '') = ?", [$cleanDigits])
                    ->first();

                if ($rider && $rider->user) {
                    $credentialEmail = $rider->user->email;
                } else {
                    // Try rider email convention rider_{digits}@munch.com
                    $possibleUser = \App\Models\User::where('email', "rider_{$cleanDigits}@munch.com")->first();
                    if ($possibleUser) {
                        $credentialEmail = $possibleUser->email;
                    }
                }
            }

            // 2. Look up by user name if still unresolved
            if ($credentialEmail === $loginInput) {
                $userByName = \App\Models\User::where('name', $loginInput)->first();
                if ($userByName) {
                    $credentialEmail = $userByName->email;
                }
            }
        }

        if (! Auth::attempt(['email' => $credentialEmail, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Check if user's employee profile is active
        $user = Auth::user();
        if (! $user->isEmployeeActive()) {
            Auth::logout();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact HR department.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
