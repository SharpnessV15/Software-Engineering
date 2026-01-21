<?php
function calculateBill($units_used, $connection_type) {
    if ($units_used < 0) return ['total_amount' => 0, 'segments' => []];

    $rates_map = [
        'home' => [2, 4, 6],
        'corporate' => [3, 6, 9],
        'industrial' => [4, 8, 12],
        'staff' => [1, 2, 3]
    ];
    
    $rates = $rates_map[$connection_type] ?? $rates_map['home'];
    
    $bill_amount = 0;
    $segments = [];

    $tier1_units = min($units_used, 50);
    if ($tier1_units > 0) {
        $cost = $tier1_units * $rates[0];
        $bill_amount += $cost;
        $segments[] = [
            'units' => $tier1_units,
            'rate' => $rates[0],
            'cost' => $cost,
            'desc' => "First $tier1_units units @ {$rates[0]} Rs/unit"
        ];
    }

    if ($units_used > 50) {
        $tier2_units = min($units_used - 50, 50);
        if ($tier2_units > 0) {
            $cost = $tier2_units * $rates[1];
            $bill_amount += $cost;
            $segments[] = [
                'units' => $tier2_units,
                'rate' => $rates[1],
                'cost' => $cost,
                'desc' => "Next $tier2_units units @ {$rates[1]} Rs/unit"
            ];
        }
    }

    if ($units_used > 100) {
        $tier3_units = $units_used - 100;
        if ($tier3_units > 0) {
            $cost = $tier3_units * $rates[2];
            $bill_amount += $cost;
            $segments[] = [
                'units' => $tier3_units,
                'rate' => $rates[2],
                'cost' => $cost,
                'desc' => "Remaining $tier3_units units @ {$rates[2]} Rs/unit"
            ];
        }
    }

    return [
        'total_amount' => $bill_amount,
        'segments' => $segments,
        'rates' => $rates
    ];
}
?>