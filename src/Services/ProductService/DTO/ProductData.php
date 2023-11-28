<?php

namespace Elsayed85\LmsRedis\Services\ProductService\DTO;

class ProductData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'],
            price: $data['price'],
        );
    }
}
