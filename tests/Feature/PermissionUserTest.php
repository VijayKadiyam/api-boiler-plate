<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Permission;

class PermissionUserTest extends TestCase
{
  
  /** @test */
  function user_must_be_logged_in()
  {
    $this->json('post', '/api/permission_user')
      ->assertStatus(401);
  }

  /** @test */
  function user_requires_following_details()
  {
    $this->json('post', '/api/permission_user', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"     =>  [
            "user_id"  =>  ["The user id field is required."],
            "permission_id"  =>  ["The permission id field is required."]
          ],
          "message"    =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function assign_permission()
  {
    $user  = factory(\App\User::class)->create();
    $user->assignPermission(1);
    $check = $user->hasPermission(1);
    $this->assertTrue($check);
    $this->assertCount(1, $user->permissions);
  }

  /** @test */
  function assign_permission_to_user()
  {
    $this->disableEH();
    $user  = factory(\App\User::class)->create();
    $permission = Permission::find(1);
    $this->payload = [ 
      'permission_id' => 1,
      'user_id'       => $user->id
    ];
    $this->json('post', '/api/permission_user?op=assign', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'  =>  [
            'first_name'  =>  $user->first_name,
            'permissions' =>  [
              0 =>  [
                'name'  =>  $permission->name
              ]
            ]
          ]
        ])
      ->assertJsonStructureExact([
        'data'  =>  [
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
          'permissions'
        ]
      ]);
  }

  /** @test */
  function unassign_permission()
  {
    $user  = factory(\App\User::class)->create();
    $user->assignPermission(1);
    $check = $user->hasPermission(1);
    $this->assertTrue($check);
    $this->assertCount(1, $user->permissions);
    $user->unassignpermission(1);
    $this->assertCount(0, $user->permissions);
  }

  /** @test */
  function unassign_permission_from_user()
  {
    $this->disableEH();
    $user  = factory(\App\User::class)->create();
    $permission = Permission::find(1);
    $user->assignPermission(1);
    $check = $user->hasPermission(1);
    $this->assertCount(1, $user->permissions);

    $this->payload        = [ 
      'user_id'         => $user->id,
      'permission_id'   => 1
    ];

    $this->json('post', '/api/permission_user?op=unassign', $this->payload , $this->headers)
         ->assertStatus(201);

    $user->refresh();
    $this->assertCount(0, $user->permissions);
  }
}
