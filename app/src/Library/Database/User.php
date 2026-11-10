<?php declare(strict_types=1);

namespace App\Library\Database;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $user_id
 * @property string   $email
 * @property string   $password_hash
 * @property string   $name
 * @property Carbon   $created_datetime
 * @property ?Carbon  $updated_datetime
 */
class User extends Model {
    /**
     * @var string
     */
    public const CREATED_AT = 'created_datetime';

    /**
     * @var string|null
     */
    public const UPDATED_AT = 'updated_datetime';

    /**
     * @var string
     */
    protected $primaryKey = 'user_id';
}
