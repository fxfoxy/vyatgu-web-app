<?php declare(strict_types=1);

namespace App\Library\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string     $hashed_id
 * @property string     $data
 * @property string     $expiration_datetime
 */
class Session extends Model {
    public $incrementing = false;
    public $timestamps   = false;

    protected $primaryKey = 'hashed_id';
    protected $keyType    = 'string';
}
