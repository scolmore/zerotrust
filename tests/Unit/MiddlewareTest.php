<?php

test('route protected by middleware cannot be accessed if not authed', function () {
    $this->get('/logged-in')
        ->assertDontSee('logged in')
        ->assertStatus(302);
});

test('routes not protected by middleware can still be accessed', function () {
    $this->get('/')
        ->assertSee('Hello World!')
        ->assertOk();
});
