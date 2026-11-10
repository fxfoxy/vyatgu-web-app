<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

use App\Library\Exceptions\RequestMethodApiException;
use App\Library\Framework\Utils;
use App\Library\Super;
use JsonException;
use Uri\WhatWg\Url;

class HttpRequest {
    public readonly ?string $feature;
    public readonly ?string $controller;
    public readonly string $action;

    private array $unverifiedData;
    private string $requestMethod;

    private function __construct(?string $feature, ?string $controller, ?string $action) {
        // Папки и классы именуем с заглавной
        $this->feature    = ucfirst($feature);
        $this->controller = ucfirst($controller) . 'Controller';
        $this->action     = ($action ?? 'index') . 'Action';

        $this->requestMethod = $_SERVER['REQUEST_METHOD'];

        $this->unverifiedData = $_GET;
        if ($this->requestMethod === HttpMethodsEnum::POST->value) {
            $this->unverifiedData = $this->getJsonPostData();
        }
    }

    public static function createFromGlobals(): static {
        $urlPath     = Url::parse($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?->getPath();
        $urlPathData = [];

        if ($urlPath) {
            $urlPathData = explode('/', trim($urlPath, '/'));

            // URI-path принимаем в kebab-кейсе, а классы и методы у нас в camel-кейсе
            // Пример: /feature-name/controller-name/action-name/
            foreach ($urlPathData as &$pathPart) {
                $pathPart = Utils::kebab2camel($pathPart);
            }
        }

        return new static(
            $urlPathData[0] ?? null,
            $urlPathData[1] ?? null,
            $urlPathData[2] ?? null,
        );
    }

    /**
     * Получить данные по запросу как есть, без всяких проверок
     */
    public function getUnverifiedData(): array {
        return $this->unverifiedData;
    }

    public function getUnverifiedParameter(string $parameterName): mixed {
        return $this->unverifiedData[$parameterName] ?? null;
    }

    public function requireHttpMethod(HttpMethodsEnum $httpMethod): void {
        if ($this->requestMethod !== $httpMethod->value) {
            throw new RequestMethodApiException("Expecting {$httpMethod->value} request method");
        }
    }

    /**
     * Получение JSON-а с поддержкой сжатия, вернее разжатия, из тела запроса
     */
    private function getJsonPostData(): array {
        $contentEncoding = isset($_SERVER['HTTP_CONTENT_ENCODING'])
            ? strtolower(trim($_SERVER['HTTP_CONTENT_ENCODING']))
            : '';

        $rawBody = file_get_contents('php://input');

        switch ($contentEncoding) {
            case 'gzip':
                $uncompressedBody = gzdecode($rawBody);
                break;

            case 'deflate':
                $uncompressedBody = gzinflate($rawBody);
                break;

            default:
                // Если заголовка нет или он пустой, данные идут как обычный текст
                $uncompressedBody = $rawBody;
                break;
        }

        $data = [];
        if (!$uncompressedBody) {
            Super::log()->warning("Empty JSON body or invalid JSON encoding: {$contentEncoding}");
            return $data;
        }

        try {
            $data = json_decode($uncompressedBody, true, 8,JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Super::log()->warning("Invalid JSON body: " . $e->getMessage());
        }

        return $data;
    }
}
