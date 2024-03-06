<?php

declare(strict_types=1);

namespace Scolmore\ZeroTrust;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use RuntimeException;

class ZeroTrust
{
    protected array $directory = [];

    public string $sessionKey;

    public function __construct()
    {
        $this->sessionKey = config('zerotrust.session_key');
    }

    public function handle(Request $request, Closure $next)
    {
        $azureUser = session("{$this->sessionKey}.azure-user");

        // Check to see if the user is already authenticated against Azure AD.
        // If they are, we can continue with the request.
        if (isset($azureUser['id'])) {
            session()->forget(["{$this->sessionKey}.azure-redirect", "{$this->sessionKey}.azure-user-state"]);

            return $next($request);
        }

        // Store the current URL, so we can redirect the user back to it after they have authenticated.
        session(["{$this->sessionKey}.azure-redirect" => $request->fullUrl()]);

        // Check to see if there are multiple directories configured.
        // If there are, we need to show the user a screen to select which directory they want to authenticate against.
        if (count($directories = config('zerotrust.directories')) > 1) {
            return response()->view('zero-trust::login', compact('directories'));
        }

        // Only one directory added, so we can just get the first one.
        return $this->selectedDirectory(directory: 0);
    }

    public function selectedDirectory(int $directory): RedirectResponse
    {
        // Generate a random state value and store it in the session to be sent to Microsoft.
        session(["{$this->sessionKey}.azure-user-state" => $state = Str::random(40)]);
        session(["{$this->sessionKey}.azure-directory" => $directory]);

        $this->setDirectory($directory);

        return redirect($this->azureLoginUrl($state));
    }

    protected function setDirectory(int $directoryKey): void
    {
        $directory = config('zerotrust.directories')[$directoryKey] ?? [];

        if (! $directory) {
            throw new RuntimeException('Directory not found');
        }

        $this->directory = $directory;
    }

    protected function azureLoginUrl(string $state): string
    {
        return (new Azure)->loginUrl($this->directory, $state);
    }

    public function callback(): Factory|View|Response|RedirectResponse|Application
    {
        $code = request('code');
        $state = request('state');
        $userState = session("{$this->sessionKey}.azure-user-state");

        if (! $code || ! $state || ! $userState || $state !== $userState) {
            return $this->error('Invalid request, please try again.');
        }

        $this->setDirectory(session("{$this->sessionKey}.azure-directory"));

        $accessToken = $this->getToken($code);

        $userDetails = $this->getUser($accessToken);

        if (! $userDetails) {
            return $this->error('Unable to retrieve user details from Azure. Please try again.');
        }

        $allowed = $this->checkRestrictions($userDetails);

        if (! $allowed) {
            $this->completed(success: false, user: $userDetails);

            return $this->error(
                message: 'You are not allowed to access this application.',
                url: $this->getLogoutUrl());
        }

        session(["{$this->sessionKey}.azure-user" => $userDetails]);

        if (config('zerotrust.auto_login')) {
            $user = app(config('zerotrust.model'))
                ->where(config('zerotrust.email_column'), $userDetails['mail'])
                ->first();

            if ($user) {
                auth()->login($user);
            }
        }

        $this->completed(success: true, user: $userDetails);

        return redirect()->to(session("{$this->sessionKey}.azure-redirect", '/'));
    }

    protected function getToken(string $code)
    {
        $response = (new Azure)->getUserToken(
            code: $code,
            directory: $this->directory
        );

        if ($response->failed()) {
            $this->error('Failed to authenticate with Azure: '.$response->json()['error_description'] ?? 'Unknown error');
        }

        return $response->json('access_token');
    }

    protected function getUser(string $accessToken): ?array
    {
        $response = (new Azure)->getUserDetails(accessToken: $accessToken);

        return $response->failed() ? null : $response->json();
    }

    protected function checkRestrictions(array $user): bool
    {
        $domain = str($user['mail'])->after('@')->toString();

        if (! $this->directory['allowed_domains']) {
            return true;
        }

        return in_array($domain, $this->directory['allowed_domains'], true);
    }

    public function logout(bool $site = true): RedirectResponse
    {
        $this->setDirectory(session("{$this->sessionKey}.azure-directory"));

        if ($site) {
            auth()->logout();
        }

        session()->forget('zero-trust');

        if ($this->directory) {
            return redirect($this->getLogoutUrl());
        }

        return redirect()->to('/');
    }

    protected function getLogoutUrl(): string
    {
        return (new Azure)->logoutUrl(directory: $this->directory);
    }

    public function completed(bool $success, array $user): void
    {
        //
    }

    public function finished(): Application|Factory|View|Response
    {
        return response()->view('zero-trust::finished');
    }

    protected function error(string $message, ?string $url = null): Application|Factory|View|Response
    {
        return response()->view(
            view: 'zero-trust::error',
            data: compact('message', 'url')
        );
    }
}
