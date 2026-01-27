<?php
function calculateBill($units_used, $connection_type) {
    $base_rates = [1.5, 2.5, 3.5]; 
    
    $tier4_map = [
        'home' => 4.5,
        'corporate' => 5.5,
        'industrial' => 6.5,
        'staff' => 3.0
    ];

    if ($connection_type == 'staff') {
        $rates = [1.0, 2.0, 3.0, 3.0];
    } else {
        $tier4 = $tier4_map[$connection_type] ?? 4.5;
        $rates = [$base_rates[0], $base_rates[1], $base_rates[2], $tier4];
    }

    if ($units_used < 0) return ['total_amount' => 0, 'segments' => [], 'rates' => $rates];

    if ($units_used == 0) {
        return [
            'total_amount' => 25.00,
            'segments' => [['units' => 0, 'rate' => 0, 'cost' => 25, 'desc' => 'Minimum Charge']],
            'rates' => $rates
        ];
    }
    
    $bill_amount = 0;
    $segments = [];
    $remaining_units = $units_used;

    $tier1_units = min($remaining_units, 50);
    if ($tier1_units > 0) {
        $cost = $tier1_units * $rates[0];
        $bill_amount += $cost;
        $segments[] = ['units' => $tier1_units, 'rate' => $rates[0], 'cost' => $cost, 'desc' => "First $tier1_units x {$rates[0]}"];
        $remaining_units -= $tier1_units;
    }

    $tier2_units = min($remaining_units, 50);
    if ($tier2_units > 0) {
        $cost = $tier2_units * $rates[1];
        $bill_amount += $cost;
        $segments[] = ['units' => $tier2_units, 'rate' => $rates[1], 'cost' => $cost, 'desc' => "Next $tier2_units x {$rates[1]}"];
        $remaining_units -= $tier2_units;
    }

    $tier3_units = min($remaining_units, 50);
    if ($tier3_units > 0) {
        $cost = $tier3_units * $rates[2];
        $bill_amount += $cost;
        $segments[] = ['units' => $tier3_units, 'rate' => $rates[2], 'cost' => $cost, 'desc' => "Next $tier3_units x {$rates[2]}"];
        $remaining_units -= $tier3_units;
    }

    if ($remaining_units > 0) {
        $cost = $remaining_units * $rates[3];
        $bill_amount += $cost;
        $segments[] = ['units' => $remaining_units, 'rate' => $rates[3], 'cost' => $cost, 'desc' => "Remaining $remaining_units x {$rates[3]}"];
    }

    if ($bill_amount < 25) {
        $bill_amount = 25;
        $segments[] = ['units' => 0, 'rate' => 0, 'cost' => 0, 'desc' => "Minimum Floor Adjustment to 25"];
    }

    return [
        'total_amount' => number_format($bill_amount, 2, '.', ''),
        'segments' => $segments,
        'rates' => $rates
    ];
}

function calculateFine($bill_date, $due_date) {
    if (empty($due_date) || empty($bill_date)) {
        return ['amount' => 0, 'months' => 0];
    }

    $bill_date_obj = new DateTime($bill_date);
    $current_date_obj = new DateTime();
    $due_date_obj = new DateTime($due_date);
    
    $is_late = ($current_date_obj > $due_date_obj);
    
    $fine = 0;
    $months_late = 0;
    
    if ($is_late) {
        $bill_year = (int)$bill_date_obj->format('Y');
        $bill_month = (int)$bill_date_obj->format('m');
        $curr_year = (int)$current_date_obj->format('Y');
        $curr_month = (int)$current_date_obj->format('m');
        
        $months_diff = (($curr_year - $bill_year) * 12) + ($curr_month - $bill_month);
        $months_late = max(1, $months_diff); 
        $fine = $months_late * 150;
    }
    
    return ['amount' => $fine, 'months' => $months_late];
}
?>