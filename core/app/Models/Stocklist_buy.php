<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocklist_buy extends Model
{
    use HasFactory;
    public function Stockist(){
        return $this->belongsTo(Stockist::class,'stockists_id','id');
    }
    public function User(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
