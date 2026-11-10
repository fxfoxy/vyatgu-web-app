<?php declare(strict_types=1);

namespace App\Library\Auth;

use App\Library\Database\User;
use App\Library\Exceptions\LogoutException;
use App\Library\Exceptions\UnauthorizedApiException;
use App\Library\Super;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class UserAuth {
    private static ?User $currentUser = null;

    public static function authorizeBySession(): ?User {
        if (self::$currentUser) {
            return self::$currentUser;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            Super::log()->warning('Authorization failed, session is not started');
            return self::$currentUser;
        }

        if (!empty($_SESSION['user_id']) and !empty($_SESSION['is_authorized'])) {
            try {
                self::$currentUser = User::findOrFail($_SESSION['user_id']);
            } catch (ModelNotFoundException $e) {
                Super::log()->warning($e->getMessage());
                self::$currentUser = null;
            }
        }

        return self::$currentUser;
    }

    public static function authorize(string $login, string $password): ?User {
        $currentUser = null;
        try {
            $currentUser = User::where('email', $login)->sole();
        } catch (ModelNotFoundException $e) {
            Super::log()->warning($e->getMessage());
        }

        if ($currentUser && password_verify($password, $currentUser->password_hash)) {
            self::$currentUser         = $currentUser;
            $_SESSION['user_id']       = $currentUser->user_id;
            $_SESSION['is_authorized'] = true;
            session_write_close();
        }

        return self::$currentUser;
    }

    public static function requireAuthUser(): User {
        if (!self::authorizeBySession()) {
            throw new UnauthorizedApiException('Unauthorized user');
        }

        return self::$currentUser;
    }

    public static function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new LogoutException('Can`t save logout, session is not started');
        }
        self::$currentUser         = null;
        $_SESSION['is_authorized'] = false;
        session_write_close();
    }
}
