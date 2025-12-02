<?php

class HLD_Discount
{
    // Discount data must be static to use inside a static method
    private static $discounts = [
        "Semaglutide injection" => [
            "first_month" => 42,
            "three_month" => 20
        ],
        "Tirzepatide injection" => [
            "first_month" => 34,
            "three_month" => 11
        ],
        "NAD Injections" => [
            "first_month" => 21,
            "three_month" => 14
        ],
        "NAD+ Nasal Spray" => [
            "first_month" => 21,
            "three_month" => 10
        ],
        "NAD+ Patches (6 week for monthly)" => [
            "first_month" => 10,
            "three_month" => 10
        ],
        "MIC+B12" => [
            "first_month" => 8,
            "three_month" => 6
        ],
        "Tirzepatide drops" => [
            "first_month" => 18,
            "three_month" => 11
        ],
    ];

    /**
     * Get discount percentage for a product and duration.
     *
     * @param string $productName
     * @param int $duration 1 = first month, 3 = three_month
     * @return int|null
     */
    public static function getDiscount($productName, $duration = 1)
    {
        $durationKey = $duration === 1 ? 'first_month' : 'three_month';

        if (isset(self::$discounts[$productName][$durationKey])) {
            return self::$discounts[$productName][$durationKey];
        }

        return null;
    }
}

// Usage example
// echo HLD_Discount::getDiscount("Semaglutide injection", 1); // 42
// echo HLD_Discount::getDiscount("NAD+ Nasal Spray", 3); // 10
