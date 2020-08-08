<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Site;

class SiteTest extends TestCase
{
  use DatabaseTransactions;

  protected $site;
  
  public function setUp()
  {
    parent::setUp();

    $this->site = factory(\App\Site::class)->create([
      'name' => 'test'
    ]);

    $this->user->assignRole(1);
    $this->user->assignSite($this->site->id);

    $this->payload = [
      'name'    =>  'AAIBUZZ',
      'phone'   =>  345765433,
      'email'   =>  'email@gmail.com',
      'address' =>  '606, Vardhaman Plaza',
    ];
  }

  /** @test */
  function user_must_be_logged_in_before_accessing_the_controller()
  {
    $this->json('post', '/api/sites')
      ->assertStatus(401); 
  }

  /** @test */
  function it_requires_following_details()
  {
    $this->json('post', '/api/sites', [], $this->headers)
      ->assertStatus(422)
      ->assertExactJson([
          "errors"  =>  [
            "name"    =>  ["The name field is required."],
            "email"   =>  ["The email field is required."],
            "phone"   =>  ["The phone field is required."],
            "address" =>  ["The address field is required."],
          ],
          "message" =>  "The given data was invalid."
        ]);
  }

  /** @test */
  function add_new_organization()
  {
    $this->disableEH();
    $this->json('post', '/api/sites', $this->payload, $this->headers)
      ->assertStatus(201)
      ->assertJson([
          'data'   =>[
            'name' => 'AAIBUZZ'
          ]
        ])
      ->assertJsonStructureExact([
          'data'   => [
            'name',
            'phone',
            'email',
            'address',
            'updated_at',
            'created_at',
            'id'
          ]
        ]);
  }

  /** @test */
  function list_of_sites()
  {
    $this->disableEH();
    $this->json('GET', '/api/sites',[], $this->headers)
      ->assertStatus(200)
      ->assertJsonStructure([
          'data' => [
            0=>[
              'name'
            ] 
          ]
        ]);
      $this->assertCount(2, Site::all());
  }

  /** @test */
  function show_single_site()
  {

    $this->json('get', "/api/sites/2", [], $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'  => [
            'name'=> 'test',
          ]
        ]);
  }

  /** @test */
  function update_single_site()
  {
    $payload = [ 
      'name'  =>  'AAIBUZZZ'
    ];

    $this->json('patch', '/api/sites/2', $payload, $this->headers)
      ->assertStatus(200)
      ->assertJson([
          'data'    => [
            'name'  =>  'AAIBUZZZ',
          ]
       ])
      ->assertJsonStructureExact([
          'data'  => [
            'id',
            'name',
            'email',
            'phone',
            'address',
            'logo_path',
            'contact_person',
            'created_at',
            'updated_at',
          ]
      ]);
  }

}
