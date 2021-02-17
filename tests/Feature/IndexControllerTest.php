<?php

use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    public function testIndexControllerHealthCheck(): void
    {
        $response = $this->get("/api/healthcheck");

        $expectedJson = json_encode([
            "message" => "Success!",
        ]);

        $this->assertJson($response->getContent(), $expectedJson);
    }
}