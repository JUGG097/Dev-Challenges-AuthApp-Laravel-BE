<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_signup_happy_flow()
    {
        $this->happySignUpFlow();
    }

    public function test_signup_validation_error_flow()
    {
        $response = $this->postJson('/api/v1/auth/signup', [
            "email" => "user@example.com",
            "password" => "qwerty",
            "providersds" => "LOCAL",
        ]);

        $response->assertStatus(400)
            ->assertJson(
                ["success" => false]
            );
    }

    public function test_login_happy_flow()
    {
        // Sign Up flow
        $this->happySignUpFlow();

        // Login flow
        $this->happyLogInFlow();
    }

    public function test_login_validation_error_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $response = $this->postJson('/api/v1/auth/login', [
            "email" => "user@example.com",
            "passwordss" => "qwerty",
            "provider" => "LOCAL",
        ]);

        $response->assertStatus(400)
            ->assertJson(
                ["success" => false]
            );
    }

    public function test_refresh_token_happy_flow()
    {
        // Sign Up flow
        $this->happySignUpFlow();

        // Login flow
        $tokens = $this->happyLogInFlow();

        // Refresh token flow
        $response = $this->postJson('/api/v1/auth/refreshToken', [
            "refreshToken" => $tokens["refreshToken"]
        ]);

        $response->assertStatus(200)
            ->assertJson(
                ["success" => true]
            );
    }

    public function test_refresh_token_validation_error_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $tokens = $this->happyLogInFlow();

        // Refresh token flow
        $response = $this->postJson('/api/v1/auth/refreshToken', [
            "refreshTokenjjsjs" => $tokens["refreshToken"]
        ]);

        $response->assertStatus(400)
            ->assertJson(
                ["success" => false]
            );
    }

    private function happySignUpFlow()
    {
        // Sign Up Flow
        $response = $this->postJson('/api/v1/auth/signup', [
            "email" => "user@example.com",
            "password" => "qwerty",
            "provider" => "LOCAL",
        ]);

        $response->assertStatus(200)
            ->assertJson(
                ["success" => true]
            );
    }

    private function happyLogInFlow()
    {
        // Login flow
        $response = $this->postJson('/api/v1/auth/login', [
            "email" => "user@example.com",
            "password" => "qwerty",
            "provider" => "LOCAL",
        ]);

        $response->assertStatus(200)
            ->assertJson(
                ["success" => true]
            );

        return ["authToken" => $response["authToken"], "refreshToken" => $response["refreshToken"]];
    }
}
