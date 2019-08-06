<?php

namespace Campus;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
   protected $fillable = [
      'codigo', 'nombre', 'descripcion'
   ];

   public function courses()
   {
      return $this->hasMany(Course::class);
   }
}
