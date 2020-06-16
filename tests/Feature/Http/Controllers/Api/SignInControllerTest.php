<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function signing_in()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        $response = $this->postJson(route('sign-in'), [
            'email' => 'user@test.com',
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertEquals(1, Sanctum::$personalAccessTokenModel::count());
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => 'App\User',
            'tokenable_id' => $user->id,
            'name' => 'Test Device',
        ]);
    }

    /** @test */
    public function signing_in_with_the_incorrect_password_returns_a_422()
    {
        $user = factory(User::class)->create([
            'email' => 'user@test.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        $response = $this->postJson(route('sign-in'), [
            'email' => 'user@test.com',
            'password' => 'incorrect',
            'device_name' => 'Test Device',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /**
     * @test
     * @dataProvider emailInputValidation
     * @dataProvider passwordInputValidation
     * @dataProvider deviceNameInputValidation
     */
    public function test_sign_in_validation($formInput, $formInputValue)
    {
        $response = $this->postJson(route('sign-in'), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function emailInputValidation()
    {
        return [
            'Email is required' => ['email', ''],
            'Email is a string' => ['email', 123],
            'Email is a valid email address' => ['email', 'String'],
            'Email exists in users table' => ['email', 'unknown@test.com'],
        ];
    }

    public function passwordInputValidation()
    {
        return [
            'Password is required' => ['password', ''],
            'Password is a string' => ['password', 123],
        ];
    }

    public function deviceNameInputValidation()
    {
        return [
            'Device name is required' => ['device_name', ''],
            'Device name is a string' => ['device_name', 123],
            'Device name is no longer than 250 characters' => ['device_name', Str::random(251)],
        ];
    }
}
