<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';
//    public function emailsType()
//    {
//        $this->belongsTo(EmailsType::class, 'type_id', 'id');
//    }
    public function users()
    {
        $this->belongsTo(User::class);
    }
}
