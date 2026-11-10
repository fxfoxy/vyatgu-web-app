<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\EmailParameter;
use App\Library\Api\Parameters\StringParameter;
use App\Library\Auth\UserAuth;
use App\Library\Database\User;
use App\Library\Exceptions\RequestApiException;
use App\Library\Super;

final class AuthRequest extends RequestAbstract {
    public function validate(): void {
        if ($this->user) {
            return;
        }

        $userEmail  = $this->validatedData['email'];
        $usersCount = User::where('email', $userEmail)->count();
        if ($usersCount === 0) {
            Super::log()->warning("User with email '$userEmail' is not registered");
        } elseif ($usersCount > 1) {
            Super::log()->warning("Too much users with email '$userEmail': {$usersCount}");
        }

        if ($usersCount != 1) {
            throw new RequestApiException('User authorization error');
        }
    }

    public function execute(): void {
        if ($this->user) {
            return;
        }

        $this->user = UserAuth::authorize($this->validatedData['email'], $this->validatedData['password']);
        if (!$this->user) {
            throw new RequestApiException('Can`t authorize user');
        }
    }

    protected function getParameters(): array {
        return [
            'email'    => (new EmailParameter()),
            'password' => (new StringParameter())->setMinLength(8)->setMaxLength(64),
        ];
    }

    public function getUser(): User {
        if (!$this->user) {
            throw new RequestApiException('User not found');
        }
        return $this->user;
    }
}
