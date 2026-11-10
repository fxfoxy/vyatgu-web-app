<?php declare(strict_types=1);

namespace App\Library\Api\View;

use App\Library\Framework\Routing\ViewAbstract;

class JsonView extends ViewAbstract {
    public function __construct(
        private array $jsonData,
    ) {
        $this->setHeader('Content-Type', 'application/json');
    }

    public function replaceJsonData(array $jsonData): void {
        $this->jsonData = $jsonData;
    }

    private function getApiData (): array {
        return [
            'is_successful' => $this->httpResponseCode < 400,
            'time'          => date('c'),
        ];
    }

    public function render(): void {
        echo json_encode(array_merge($this->getApiData(), $this->jsonData), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
