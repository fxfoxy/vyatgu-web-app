<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

use App\Library\Framework\EnumHelperTrait;
use BackedEnum;

/**
 * @method static BackedEnum[] cases()
 */
enum HttpMethodsEnum: string {
    use EnumHelperTrait;

    case GET = 'GET';
    case POST = 'POST';
}
