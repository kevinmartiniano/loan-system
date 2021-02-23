<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexControllerHealthCheck(): void
    {
        $response = $this->get("/api/healthcheck");

        $expectedJson = json_encode([
            "message" => "Success!",
        ]);

        $this->assertJson($response->getContent(), $expectedJson);
    }
}
