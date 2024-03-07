<?php

test('domain selection screen is shown when multiple directories are available', function () {
    config()->set('zerotrust.directories', [
        [
            'name' => 'Test One',
            'tenant_id' => '123',
            'client_id' => '456',
        ],
        [
            'name' => 'Test Two',
            'tenant_id' => '789',
            'client_id' => '101',
        ],
    ]);

    $this->get('/logged-in')
        ->assertSee('Sign in')
        ->assertSee('Azure AD')
        ->assertSee('Test One')
        ->assertSee('Test Two');
});

test('error screen is shown on invalid request', function () {
    $this->get('/zero-trust/callback?code=123&state=456')
        ->assertSee('Invalid request, please try again.');
});

test('finish screen is shown on logout', function () {
    $this->withSession(['zero-trust.azure-directory' => 0]);

    $this->post('/zero-trust/logout', ['site' => false])
        ->assertStatus(302);

    $this->get('/zero-trust/session-finished')
        ->assertSee('your session is finished');
});
