<!DOCTYPE html>
<html>
<body>

<?php
function getFirstBusinessDayInMonth($month, $year, $day = 1)
{
    $dateData = getdate(mktime(null, null, null, $month, $day, $year));
    if (in_array($dateData['wday'], range(1, 5))) {
        return $dateData['mday'];
    }
    return getFirstBusinessDayInMonth($month, $year, ($day + 1));
}

//$wday = getFirstBusinessDayInMonth(01, 2022);
//echo '$wday: ' . $wday . '<br>';

// begin temp1
function get_first_day($day_number = 1, $month = false, $year = false)
{
    $month = ($month === false) ? strftime("%m") : $month;
    $year = ($year === false) ? strftime("%Y") : $year;

    $week_day = date("w", mktime(0, 0, 0, $month, 1, $year));
    if ($week_day > 0 && $week_day < 6) return mktime(0, 0, 0, $month, 1, $year);

    $first_day = 1 + ((7 + $day_number - strftime("%w", mktime(0, 0, 0, $month, 1, $year))) % 7);
    return mktime(0, 0, 0, $month, $first_day, $year);
}

// end temp1

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
$start_date = '2021-06-01';
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
        echo 'Line 5' . '<br>';
        $queued_at = $queueDate . " {$send_at}";
    } else {
        echo 'Line 6' . '<br>';
        // $queued_at = getFirstBusinessDate($queueDate) . " {$send_at}";

        $queued_at = date('Y-m-', strtotime($queueDate)) .
            str_repeat('0', 2 - strlen(getFirstBusinessDayInMonth(date('m', strtotime($queueDate)), date('Y', strtotime($queueDate))))) . getFirstBusinessDayInMonth(date('m', strtotime($queueDate)), date('Y', strtotime($queueDate))) . " {$send_at}";
    }
} else {

    if ($dayofweek == 8) {
        // First day of the month

// begin temp1
        if (date('j', $init_date) === '1') {
            echo 'Line: 1' . '<br>';
            $startDate = date('Y-m-d', $init_date);
        } else {
            echo 'Line: 2' . '<br>';
            $startDate = (date('n', $init_date) == 12) ? (date('Y', $init_date) + 1) . '-01-01' : date('Y', $init_date) . '-' . str_repeat('0', 2 - strlen(date('n', $init_date) + 1)) . (date('n', $init_date) + 1) . '-01';
        }
// end temp1

        $queued_at = "{$startDate} {$send_at}";
    } else {
        // first business day of the month

// begin temp1

// INCORRECT when input 2022-01-04 then output 2022-01-04:    echo 'getFirstBusinessDate(date(Y-m-d, $init_date)): ' . getFirstBusinessDate(date('Y-m-d', $init_date)) . '<br>';

//    echo 'date(j, $init_date): ' . date('j', $init_date) . '<br>';
//    echo 'date(Y, $init_date): ' . date('Y', $init_date) . '<br>';
//    echo 'date(m, $init_date): ' . date('m', $init_date) . '<br>';
//    echo 'date(d, $init_date): ' . date('d', $init_date) . '<br>';
// INCORRECT when 2022-01-02:     echo '1-get_first_day(date(j, $init_date),date(m, $init_date),date(Y, $init_date)): ' . date('j',get_first_day(date('j', $init_date),date('m', $init_date),date('Y', $init_date))) . '<br>';

// CORRECT:    echo '2-date(j,get_first_day(1,date(m, $init_date),date(Y, $init_date))): ' . date('j',get_first_day(1,date('m', $init_date),date('Y', $init_date))) . '<br>';

        echo '2-getFirstBusinessDayInMonth(date(m,strtotime($init_date)),date(Y,strtotime($init_date))): ' . getFirstBusinessDayInMonth(date('m', $init_date), date('Y', $init_date)) . '<br>';

//    if (date('j', $init_date) <= date('j',get_first_day(1,date('m', $init_date),date('Y', $init_date)))) {

        if (date('j', $init_date) <= getFirstBusinessDayInMonth(date('m', $init_date), date('Y', $init_date))) {

            echo 'Line: 3' . '<br>';
            $startDate = date('Y-m-d', $init_date);
        } else {
            echo 'Line: 4' . '<br>';
            $startDate = (date('n', $init_date) == 12) ? (date('Y', $init_date) + 1) . '-01-01' : date('Y', $init_date) . '-' . str_repeat('0', 2 - strlen(date('n', $init_date) + 1)) . (date('n', $init_date) + 1) . '-01';
        }
// end temp1


        echo '$startDate: ' . $startDate . '<br>';

//    echo 'date(j, $startDate): ' . date('j', strtotime($startDate)) . '<br>';
//    echo 'date(Y, $startDate): ' . date('Y', strtotime($startDate)) . '<br>';
//    echo 'date(m, $startDate): ' . date('m', strtotime($startDate)) . '<br>';
//    echo 'date(d, $startDate): ' . date('d', strtotime($startDate)) . '<br>';
// INCORRECT when 2022-01-02:  echo '3-date(Y-m-d,get_first_day(date(j, $startDate),date(m, $startDate),date(Y, $startDate))): ' . date('Y-m-d',get_first_day(date('j', strtotime($startDate)),date('m', strtotime($startDate)),date('Y', strtotime($startDate)))) . '<br>';

// INCORRECT: echo '4-date(Y-m-d, strtotime(2021-01-01 first weekday)): ' . date('Y-m-d', strtotime('2021-01-01 first weekday')) . '<br>';

        $queued_at = getFirstBusinessDate($startDate) . " {$send_at}";
        echo '5-$queued_at: ' . $queued_at . '<br>';

        $queued_at = date('Y-m-', strtotime($startDate)) .
            str_repeat('0', 2 - strlen(getFirstBusinessDayInMonth(date('m', strtotime($startDate)), date('Y', strtotime($startDate))))) . getFirstBusinessDayInMonth(date('m', strtotime($startDate)), date('Y', strtotime($startDate))) . " {$send_at}";
        echo '6-$queued_at: ' . $queued_at . '<br>';
    }
}

echo '$startDate: ' . $startDate . '<br>';
echo '6-$queued_at: ' . $queued_at . '<br>';
?>

</body>
</html>
