<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registering()
    {
        Event::fake();

        $response = $this->postJson(route('register'), [
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['token']);

        $this->assertEquals(1, User::count());
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'user@test.com',
        ]);
        $this->assertEquals(1, Sanctum::$personalAccessTokenModel::count());
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => 'App\User',
            'name' => 'Test Device',
        ]);

        Event::assertDispatched(Registered::class);
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     * @dataProvider emailInputValidation
     * @dataProvider passwordInputValidation
     * @dataProvider passwordConfirmationInputValidation
     * @dataProvider deviceNameInputValidation
     */
    public function test_register_validation($formInput, $formInputValue)
    {
        $user = factory(User::class)->create(['email' => 'user@test.com']);

        $response = $this->postJson(route('register'), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function nameInputValidation()
    {
        return [
            'Name is required' => ['name', ''],
            'Name is a string' => ['name', 123],
            'Name is no longer than 250 characters' => ['name', Str::random(251)],
        ];
    }

    public function emailInputValidation()
    {
        return [
            'Email is required' => ['email', ''],
            'Email is a string' => ['email', 123],
            'Email is a valid email address' => ['email', 'String'],
            'Email is no longer than 250 characters' => ['email', Str::random(50).'@'.Str::random(200).'.com'],
            'Email is missing from users table' => ['email', 'user@test.com'],
        ];
    }

    public function passwordInputValidation()
    {
        return [
            'Password is required' => ['password', ''],
            'Password is a string' => ['password', 123],
            'Password is 8 characters or more' => ['password', 'abc'],
            'Password matches password confirmation' => ['password', 'password'],
        ];
    }

    public function passwordConfirmationInputValidation()
    {
        return [
            'Password confirmation is required' => ['password_confirmation', ''],
            'Password confirmation is a string' => ['password_confirmation', 123],
            'Password confirmation is 8 characters or more' => ['password_confirmation', 'abc'],
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
