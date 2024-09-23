<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'result',
        'create_time'
    ];

    public function add_log($info) {
        $map['client_id'] = $info['clientId'];
        $map['result'] = $info['result'];
        $map['create_time'] = $info['createTime'];

        return $this->create($map);
    }
}
