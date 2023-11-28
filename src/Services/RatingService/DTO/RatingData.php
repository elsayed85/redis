<?php

namespace Elsayed85\LmsRedis\Services\RatingService\DTO;

class RatingData
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $rating,
        public readonly float $averageRating,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            rating: $data['rating'],
            averageRating: $data['averageRating'],
        );
    }
}
