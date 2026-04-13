<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use RuntimeException;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        try {
            $authenticated = Auth::attempt($this->only('email', 'password'), $this->boolean('remember'));
        } catch (RuntimeException $exception) {
            $authenticated = $this->attemptLegacyPasswordLogin();
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function attemptLegacyPasswordLogin(): bool
    {
        $user = User::where('email', $this->string('email'))->first();

        if (! $user || ! is_string($user->password) || $user->password === '') {
            return false;
        }

        $candidateHashes = [$user->password];

        foreach (['$2a$', '$2x$', '$2b$'] as $prefix) {
            if (str_starts_with($user->password, $prefix)) {
                $candidateHashes[] = '$2y$'.substr($user->password, 4);
            }
        }

        foreach (array_unique($candidateHashes) as $candidateHash) {
            if (! password_verify((string) $this->input('password'), $candidateHash)) {
                continue;
            }

            $user->forceFill([
                'password' => (string) $this->input('password'),
            ])->save();

            Auth::login($user, $this->boolean('remember'));

            return true;
        }

        return false;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
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
