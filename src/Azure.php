<?php

declare(strict_types=1);

namespace Scolmore\ZeroTrust;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Azure
{
    protected string $baseUrl = 'https://login.microsoftonline.com/';

    protected string $endpoint = '/oauth2/authorize';

    protected string $graphApi = 'https://graph.microsoft.com/';

    public function loginUrl(array $directory, string $state): string
    {
        $parameters = http_build_query([
            'client_id' => $directory['client_id'],
            'response_type' => 'code',
            'redirect_uri' => route('zero-trust.callback'),
            'scope' => 'openid',
            'resource' => $this->graphApi,
            'state' => $state,
        ]);

        return "{$this->baseUrl}{$directory['tenant_id']}{$this->endpoint}?{$parameters}";
    }

    public function logoutUrl(array $directory): string
    {
        $parameters = http_build_query([
            'post_logout_redirect_uri' => route('zero-trust.finished'),
        ]);

        return "{$this->baseUrl}{$directory['tenant_id']}/oauth2/v2.0/logout?{$parameters}";
    }

    public function getUserToken(string $code, array $directory): PromiseInterface|Response
    {
        return Http::asForm()
            ->post(
                url: "{$this->baseUrl}{$directory['tenant_id']}/oauth2/token",
                data: [
                    'client_id' => $directory['client_id'],
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_secret' => $directory['secret'],
                    'redirect_uri' => route('zero-trust.callback'),
                ]);
    }

    public function getUserDetails(string $accessToken): PromiseInterface|Response
    {
        return Http::withToken($accessToken)
            ->get("{$this->graphApi}v1.0/me");
    }
}
