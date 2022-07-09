<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dummy object.
     *
     * @var array
     */
    private $testData = [
        'name' => 'Test user',
        'email' => 'test@test.com',
        'password' => 'secret',
        'document' => '999.999.999-99'
    ];

    public function test_show_a_user()
    {
        $user = User::factory()->create();

        $url = '/api/users/' . $user->id;

        $this->get($url)
            ->assertStatus(200)
            ->assertExactJson($user->toArray());
    }

    public function test_create_a_user()
    {
        $this->post('/api/users', $this->testData)
            ->assertStatus(201)
            ->assertJson(['status' => 'created']);
    }

    public function test_create_a_logist_user()
    {
        $logistUser = $this->testData;
        $logistUser['logist'] = true;

        $this->post('/api/users', $logistUser)
            ->assertStatus(201)
            ->assertJson(['status' => 'created']);
    }

    public function test_create_an_empty_user()
    {
        $this->post('/api/users', [])
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'document' => ['The document field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    public function test_create_a_user_with_repeated_email()
    {
        User::factory()->create(['email' => $this->testData['email']]);

        $this->post('/api/users', $this->testData)
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed',
                'errors' => ['email' => ['The email has already been taken.']],
            ]);
    }

    public function test_create_a_user_with_invalid_document()
    {
        $this->testData['document'] = 'test';  // invalid document

        $this->post('/api/users', $this->testData)
            ->assertStatus(403)
            ->assertExactJson([
                'status' => 'failed',
                'errors' => ['document' => ['The CPF or CNPF is invalid.']],
            ]);
    }

    public function test_update_a_user_with_a_new_email()
    {
        $user = User::factory()->create();
        $user->email = 'other@email.com';

        $url = '/api/users/' . $user->id;

        $this->put($url, ['email' => 'other@email.com'])
            ->assertStatus(200)
            ->assertExactJson([
                'status' => 'updated',
                'data' => $user->toArray(),
            ]);
    }

    public function test_update_a_user_with_invalid_email()
    {
        $user = User::factory()->create();
        $user->email = 'other@email.com';

        $url = '/api/users/' . $user->id;

        $this->put($url, ['email' => 'test'])
            ->assertStatus(403)
            ->assertJson([
                'status' => 'failed',
                'errors' => ['email' => ['The email must be a valid email address.']],
            ]);
    }

    public function test_delete_an_existing_user()
    {
        $user = User::factory()->create()->toArray();
        $url = '/api/users/' . $user['id'];

        $this->delete($url)
            ->assertStatus(200)
            ->assertJson(['status' => 'deleted']);

        $this->get($url)->assertStatus(404);
    }

    public function test_delete_a_non_existing_user()
    {
        $this->delete('/api/users/1')
            ->assertStatus(404);
    }
}
