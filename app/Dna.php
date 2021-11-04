<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dna extends Model
{
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'dna';
    protected $primaryKey = 'id';
    protected $guarded = [];

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dna_code', 'type'
    ];
}
