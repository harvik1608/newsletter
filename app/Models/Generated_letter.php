<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Generated_letter extends Model
{
    use SoftDeletes;
    protected $fillable = [];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
