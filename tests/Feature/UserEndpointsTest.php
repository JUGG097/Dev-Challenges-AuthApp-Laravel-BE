<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEndpointsTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_fetch_user_details_happy_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $tokens = $this->happyLogInFlow();

        // auth()->logout();

        // Fetch user details
        $response = $this->getJson('/api/v1/user/profile');

        $response->assertStatus(200)
            ->assertJson(
                ["success" => true]
            );
    }

    public function test_fetch_user_details_credentials_error_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $this->happyLogInFlow();

        auth()->logout();

        // Fetch user details
        $response = $this->getJson('/api/v1/user/profile');

        $response->assertStatus(401)
            ->assertJson(
                ["success" => false]
            );
    }

    public function test_update_user_details_happy_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $tokens = $this->happyLogInFlow();

        // auth()->logout();

        // Update user details
        $response = $this->putJson('/api/v1/user/editProfile', [
            'name' => "White Fish",
            'bio'  => "White Fish",
            'image' => "White Fish",
            'phoneNumber' => "White Fish",
        ]);

        $response->assertStatus(200)
            ->assertJson(
                ["success" => true]
            );
    }

    public function test_update_user_details_validation_error_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $tokens = $this->happyLogInFlow();

        // auth()->logout();

        // Update user details
        $response = $this->putJson('/api/v1/user/editProfile', [
            'name' => "White Fish",
            'bio'  => "White Fish",
            'imagesdsds' => "White Fish",
            'phoneNumber' => "White Fish",
        ]);

        $response->assertStatus(400)
            ->assertJson(
                ["success" => false]
            );
    }

    public function test_update_user_details_credentials_error_flow()
    {
        // Sign Up Flow
        $this->happySignUpFlow();

        // Login flow
        $this->happyLogInFlow();

        auth()->logout();

        // Update user details
        $response = $this->putJson('/api/v1/user/editProfile', [
            'name' => "White Fish",
            'bio'  => "White Fish",
            'image' => "White Fish",
            'phoneNumber' => "White Fish",
        ]);

        $response->assertStatus(401)
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
