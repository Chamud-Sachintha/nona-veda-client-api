<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'email',
        'birthday',
        'gender',
        'create_time'
    ];

    public function add_log($info) {
        $map['first_name'] = $info['firstName'];
        $map['email'] = $info['email'];
        $map['birthday'] = $info['birthday'];
        $map['gender'] = $info['gender'];
        $map['create_time'] = $info['createTime'];

        return $this->create($map);
    }
}
