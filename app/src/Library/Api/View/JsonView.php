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

    public function render(): void {
        echo json_encode($this->jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
