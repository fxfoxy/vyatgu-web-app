<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

abstract class ViewAbstract {
    protected int $httpResponseCode = 200;

    private array $httpResponseHeaders = [];

    /**
     * Тут нам надо сделать echo содержимого страницы или просто include html-файла.
     */
    abstract public function render(): void;

    final public function setResponseCode(int $httpResponseCode): void {
        $this->httpResponseCode = $httpResponseCode;
    }

    final public function setHeader(string $name, string $value): void {
        $this->httpResponseHeaders[$name] = $value;
    }

    final public function sendHeadersAndRender(): void {
        http_response_code($this->httpResponseCode);

        foreach ($this->httpResponseHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        try {
            ob_start();
            $this->render();
        } finally {
            ob_end_flush();
        }
    }
}
