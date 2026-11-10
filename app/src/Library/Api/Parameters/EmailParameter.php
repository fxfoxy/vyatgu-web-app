<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class EmailParameter extends StringParameter {
    public function __construct() {
        $this
            ->setMinLength(6)
            ->setMaxLength(128);
    }

    public function validateInternal(string $parameterName, mixed $unverifiedParameterValue, array &$validatedData, array &$errors): void {
        parent::validateInternal($parameterName, $unverifiedParameterValue, $validatedData, $errors);
        if ($errors) {
            return;
        }

        $email = $validatedData[$parameterName];
        if (empty($email) || strrpos($email, '@') === false) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_EMAIL;
            return;
        }

        [$localPart, $domain] = explode('@', $email, 2);

        // Переводим домены в национальной кодировке во что-то приличное.
        $domainInPunyCode = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        $emailInPunyCode = $localPart . '@' . $domainInPunyCode;
        // Супер-пупер регулярка с избыточным экранированием ради простоты чтения
        // Комментарии на примере почты username.with.dots@domain.with.dots
        $availableCharacters = 'A-Za-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~';
        $regexp              = '/^' .
            "[\.{$availableCharacters}]+" .    // username.with.dots
            "@" .                              // @
            "[{$availableCharacters}]+" .      // domain
            "(\.[{$availableCharacters}]+)+" . // .with.dots
            '$/';
        if (!preg_match($regexp, $emailInPunyCode)) {
            $errors[$parameterName][] = ApiParameterErrors::INVALID_EMAIL;
            return;
        }

        $validatedData[$parameterName] = $emailInPunyCode;
    }
}
