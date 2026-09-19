<?php
function calculateBookingTotal(float $pricePerPerson, int $persons): array
{
    $subtotal = $pricePerPerson * $persons;

    $discountRate = 0;
    if ($persons >= 10 && $persons <= 15) {
        $discountRate = 20;
    } elseif ($persons >= 5 && $persons < 10) {
        $discountRate = 10;
    }

    $discountAmount = round($subtotal * ($discountRate / 100), 2);
    $totalAmount = round($subtotal - $discountAmount, 2);

    return [
        'subtotal' => round($subtotal, 2),
        'discount_rate' => $discountRate,
        'discount_amount' => $discountAmount,
        'total_amount' => $totalAmount,
    ];
}

function calculateDepositAmount(float $fullTotal): float
{
    return round($fullTotal * 0.10, 2);
}
