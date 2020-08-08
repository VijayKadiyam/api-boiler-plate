<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserTest extends TestCase
{
  use DatabaseTransactions;
  
  public function setUp()
  {
    parent::setUp();

    $this->site = factory(\App\Site::class)->create([
      'name' => 'test'
    ]);
    $this->user->assignSite($this->site->id);
    $this->headers['site-id'] = $this->site->id;

    $this->payload = [ 
      'first_name'  =>  'sangeetha',
      'last_name'   =>  'sangeetha',
      'user_name'   =>  'sangeeta',
      'initials'    =>  'san',
      'phone'       =>  9844778380,
      'email'       =>  'sangeetha@gmail.com',
      'active'      =>  1,
    ];
  }

  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/users')
         ->assertStatus(401);
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/users', [], $this->headers)
         ->assertStatus(422)
         ->assertExactJson([
            "errors"  =>  [
              "first_name"  =>  ["The first name field is required."],
              "last_name"   =>  ["The last name field is required."],
              "user_name"   =>  ["The user name field is required."],
              "initials"    =>  ["The initials field is required."],
              "email"       =>  ["The email field is required."],
              "active"      =>  ["The active field is required."],
            ],
            "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_user()
  {
    $this->disableEH();
    $this->json('post', '/api/users', $this->payload, $this->headers)
     ->assertStatus(201)
     ->assertJson([
        'data'  =>  [
          'first_name'  =>  'sangeetha',
          'last_name'   =>  'sangeetha',
          'user_name'   =>  'sangeeta',
          'initials'    =>  'san',
          'phone'       =>  9844778380,
          'email'       =>  'sangeetha@gmail.com',
          'active'      =>  1,
        ]
      ])
      ->assertJsonStructure([
          'data'  =>  [
            'first_name',
          ]
        ])
      ->assertJsonStructureExact([
          'data'  =>  [
            'first_name',
            'last_name',
            'user_name',
            'initials',
            'phone',
            'email',
            'active',
            'updated_at',
            'created_at',
            'id',
          ]
        ]);
  }

  /** @test */
  public function list_of_users()
  {
    $this->disableEH();
    $user = factory(\App\User::class)->create();
    $user->assignRole(3);
    $user->assignSite($this->site->id);

    $this->json('get', '/api/users?role_id=3', [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => []
        ]);

    $this->assertCount(1, User::whereHas('roles',  function($q) {
                                $q->where('name', '!=', 'Admin');
                                $q->where('name', '!=', 'Super Admin');
                              })->get());
  }

  /** @test */
  function show_single_user_details()
  {
    $this->disableEH();
    $this->json('get', "/api/users/1", [], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data'  =>  [
            'first_name',
            'phone',
            'email' 
          ]
        ]);
  }

  /** @test */
  function update_single_user_details()
  {
    $this->disableEH();
    $payload  = [ 
      'first_name'  =>  'sangeetha 1',
      'last_name'   =>  'sangeetha',
      'user_name'   =>  'sangeeta',
      'initials'    =>  'san',
      'phone'       =>  9844778380,
      'email'       =>  'sangeetha@gmail.com',
      'active'      =>  1,
    ];
    $this->json('patch', '/api/users/1', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    =>  [
            'first_name'  =>  'sangeetha 1',
            'last_name'   =>  'sangeetha',
            'user_name'   =>  'sangeeta',
            'initials'    =>  'san',
            'phone'       =>  9844778380,
            'email'       =>  'sangeetha@gmail.com',
            'active'      =>  1,
          ]
        ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'user_name',
            'initials',
            'email',
            'phone',
            'api_token',
            'active',
            'email_verified_at',
            'created_at',
            'updated_at',
            'roles',
            'sites',
          ],
          'success'
        ]);
  }
}
