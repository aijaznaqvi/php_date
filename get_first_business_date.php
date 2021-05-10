<!DOCTYPE html>
<html>
<body>

<?php

function getFirstBusinessDate($queueDate)
{
    $dayOfWeek = date('l', strtotime($queueDate));
    if (in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
        $addDay = 0;
        switch ($dayOfWeek) {
            case 'Saturday':
                $addDay = '2';
                break;

            case 'Sunday':
                $addDay = '1';
                break;
        }
        $queueDate = date('Y-m-d', strtotime("+" . $addDay . " day", strtotime($queueDate)));
    }

    return $queueDate;
}

$dayofweek = 9;
$send_at = '09:00:00';
$start_date = '2021-05-01';
$init_date = !empty($start_date) ? strtotime($start_date) : strtotime('today');

$send_at = !empty($send_at) ? $send_at : '04:00:00';
//do not send exactly on day change, we could have some double sending because server time differences.
if ($send_at == '00:00:00') {
    $send_at = '00:01:00';
}
//some times servers are out of sync, so to prevent rescheduling emails on same day, lets assume that now already happened
$current_timestamp = strtotime('+1 minutes');


echo '$init_date: ' . $init_date . '<br>';
echo 'date(Y-m-d, $init_date): ' . date('Y-m-d', $init_date) . '<br>';
echo '$current_timestamp: ' . $current_timestamp . '<br>';
echo 'date(Y-m-d, $current_timestamp): ' . date('Y-m-d', $current_timestamp) . '<br>';


if (!$init_date || ($init_date && (date('Y-m-d', $init_date) == date('Y-m-d', $current_timestamp) || $init_date < $current_timestamp))) {
    $queueDate = date('Y-m-d', strtotime("first day of next month"));
    if ($dayofweek == 8) {
        $queued_at = $queueDate . " {$send_at}";
    } else {
        $queued_at = getFirstBusinessDate($queueDate) . " {$send_at}";
    }
} else {

    if (date('j', $init_date) == 1) {
        $startDate = date('Y-m-d', $init_date);
    } else {
        $startDate = (date('n', $init_date) == 12) ? (date('Y', $init_date) + 1) . '-01-01' : date('Y', $init_date) . '-' . str_repeat('0', 2 - strlen(date('n', $init_date) + 1)) . (date('n', $init_date) + 1) . '-01';
    }

    if ($dayofweek == 8) {
        $queued_at = "{$startDate} {$send_at}";
    } else {
        $queued_at = getFirstBusinessDate($startDate) . " {$send_at}";
    }
}

echo '$startDate: ' . $startDate . '<br>';
echo '$queued_at: ' . $queued_at . '<br>';
?>

</body>
</html>
