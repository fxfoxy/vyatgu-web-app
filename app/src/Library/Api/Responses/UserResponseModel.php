<?php declare(strict_types=1);

namespace App\Library\Api\Responses;

use App\Library\Database\User;

final class UserResponseModel {
    public static function getInfo(User $user): array {
        return [
            'user_id'          => $user->user_id,
            'email'            => $user->email,
            'name'             => $user->name,
            'created_datetime' => $user->created_datetime->format('c'),
            'updated_datetime' => $user->updated_datetime?->format('c'),
        ];
    }
}
