<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
  protected $fillable = [
    'name', 'email', 'phone', 'address', 'logo_path', 'contact_person'
  ];

  public function users()
  {
    return $this->belongsToMany(User::class)
      ->with('roles', 'sites',);
  }
}
