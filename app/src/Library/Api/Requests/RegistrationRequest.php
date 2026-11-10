<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\EmailParameter;
use App\Library\Api\Parameters\StringParameter;
use App\Library\Auth\UserAuth;
use App\Library\Database\User;
use App\Library\Exceptions\RequestApiException;

final class RegistrationRequest extends RequestAbstract {
    public function validate(): void {
        if ($this->user) {
            throw new RequestApiException('Authorized user');
        }
        if (User::where('email', $this->validatedData['email'])->count()) {
            throw new RequestApiException('User already exists');
        }
    }

    public function execute(): void {
        $newUser                = new User();
        $newUser->email         = $this->validatedData['email'];
        $newUser->password_hash = password_hash($this->validatedData['password'], PASSWORD_ARGON2ID);
        $newUser->name          = $this->validatedData['name'];
        $newUser->save();

        $this->user = UserAuth::authorize($this->validatedData['email'], $this->validatedData['password']);
        if (!$this->user) {
            throw new RequestApiException('Can`t authorize new user');
        }
    }

    protected function getParameters(): array {
        return [
            'email'     => (new EmailParameter()),
            'name'      => (new StringParameter())->setMinLength(2)->setMaxLength(255),
            'password'  => (new StringParameter())->setMinLength(8)->setMaxLength(64),
        ];
    }

    public function getUser(): User {
        if (!$this->user) {
            throw new RequestApiException('User not found');
        }
        return $this->user;
    }
}
