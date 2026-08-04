<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductVariantTest extends TestCase
{
    public function test_variant_rows_are_normalized_into_structured_payload():
    {
        $variants = [
            ['size' => 'Small', 'color' => 'Red', 'quantity' => '5'],
            ['size' => 'Medium', 'color' => 'Blue', 'quantity' => '3'],
        ];

        $normalized = Product::normalizeVariants($variants);

        $this->assertSame([
            ['size' => 'Small', 'color' => 'Red', 'quantity' => 5],
            ['size' => 'Medium', 'color' => 'Blue', 'quantity' => 3],
        ], $normalized);
    }
}
