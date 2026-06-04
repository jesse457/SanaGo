<?php

declare(strict_types=1);

it('returns a successful response for the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});
