<?php

use Illuminate\Database\Seeder;
use App\Site;

class SiteSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Site::create([
      'name'    => 'Om Crane Pvt. Ltd.', 
      'email'   =>  'email@rms.com', 
      'address' =>  'Thane',
      'phone'   =>  '9820704909'
    ]);
  }
}
