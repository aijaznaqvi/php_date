<!DOCTYPE html>
<html>
<body>

<?php
function getFirstBusinessDay($month, $year, $day = 1)
{
    $dateData = getdate(mktime(null, null, null, $month, $day, $year));
    if (in_array($dateData['wday'], range(1, 5))) {
        return $dateData['mday'];
    }
    return getFirstBusinessDay($month, $year, ($day + 1));
}

//$test_date='2021-12-31';
//$wday = getFirstBusinessDay(01, 2022);
//$wday = getFirstBusinessDay(date('m',strtotime($test_date)), //date('Y',strtotime($test_date)));
//echo 'getFirstBusinessDay: ' . '$wday: ' . $wday . '<br>';

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

// $wday = getFirstBusinessDate(date('Y-m',strtotime($test_date)) . '-01');
// echo 'getFirstBusinessDate: ' . '$wday: ' . $wday . '<br>';

/**
* Tests with following done:
dayofweek=8,9
date=2022-01-01 to 2022-01-05
date=2021-12-27, current_timestamp=2021-05-02 09:40:00
date=2021-12-31, current_timestamp=strtotime('+1 minutes')
date=2022-02-01, current_timestamp=strtotime('+1 minutes')
date=2021-07-01,2021-07-02,2021-08-01,2021-08-02, current_timestamp=strtotime('+1 minutes')
date=2021-05-01 to 2021-05-04, current_timestamp=2021-05-02
date=2021-06-01, send_at = '09:00:00', current_timestamp='2021-06-01 10:00:00','2021-06-01 08:00:00'
*/
 // date=2021-05-01 to 2021-05-04, current_timestamp=2021-05-02
$dayofweek = 8;
$send_at = '09:00:00';
$send_at = !empty($send_at) ? $send_at : '04:00:00';
$start_date = '2021-05-04';
$init_date = !empty($start_date) ? strtotime("$start_date $send_at") : time();

//do not send exactly on day change, we could have some double sending because server time differences.
if ($send_at == '00:00:00') {
    $send_at = '00:01:00';
}
//some times servers are out of sync, so to prevent rescheduling emails on same day, lets assume that now already happened
$current_timestamp = strtotime('+1 minutes');


echo '$init_date: ' . $init_date . '<br>';
echo 'date(Y-m-d H:i:s, $init_date): ' . date('Y-m-d H:i:s', $init_date) . '<br>';
echo '$current_timestamp: ' . $current_timestamp . '<br>';
echo 'date(Y-m-d H:i:s, $current_timestamp): ' . date('Y-m-d H:i:s', $current_timestamp) . '<br>';


echo '$init_date < $current_timestamp: ' . ($init_date < $current_timestamp) . '<br>';
if (!$init_date || ($init_date && $init_date < $current_timestamp)) {
echo 'Line 7' . '<br>';
    $init_date = $current_timestamp;
    
    /*$queueDate = date('Y-m-d', strtotime("first day of next month"));
    if ($dayofweek == 8) {
        echo 'Line 5' . '<br>';
        $queued_at = $queueDate . " {$send_at}";
    } else {
        echo 'Line 6' . '<br>';
        // $queued_at = getFirstBusinessDate($queueDate) . " {$send_at}";

        $queued_at = date('Y-m-', strtotime($queueDate)) .
            str_repeat('0', 2 - strlen(getFirstBusinessDayInMonth(date('m', strtotime($queueDate)), date('Y', strtotime($queueDate))))) . getFirstBusinessDayInMonth(date('m', strtotime($queueDate)), date('Y', strtotime($queueDate))) . " {$send_at}";
    }*/
} /*else {*/

    if ($dayofweek == 8) {
        // First day of the month

// begin temp1
        if (date('j', $init_date) === '1' && $init_date > $current_timestamp
        ) {
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

$wday = getFirstBusinessDate(date('Y-m',$init_date) . '-01');
echo '2-getFirstBusinessDate: ' . '$wday: ' . $wday . '<br>';

// INCORRECT when input 2022-01-04 then output 2022-01-04:    echo 'getFirstBusinessDate(date(Y-m-d, $init_date)): ' . getFirstBusinessDate(date('Y-m-d', $init_date)) . '<br>';

echo '2-getFirstBusinessDate(date(Y-m,strtotime($init_date)) . -01): ' . getFirstBusinessDate(date('Y-m',$init_date) . '-01') . '<br>';

    echo 'date(j, $init_date): ' . date('j', $init_date) . '<br>';
    echo 'date(Y, $init_date): ' . date('Y', $init_date) . '<br>';
    echo 'date(m, $init_date): ' . date('m', $init_date) . '<br>';
    echo 'date(d, $init_date): ' . date('d', $init_date) . '<br>';
// INCORRECT when 2022-01-02:     echo '1-get_first_day(date(j, $init_date),date(m, $init_date),date(Y, $init_date)): ' . date('j',get_first_day(date('j', $init_date),date('m', $init_date),date('Y', $init_date))) . '<br>';

// CORRECT:    echo '2-date(j,get_first_day(1,date(m, $init_date),date(Y, $init_date))): ' . date('j',get_first_day(1,date('m', $init_date),date('Y', $init_date))) . '<br>';

        echo '2-getFirstBusinessDay(date(m,strtotime($init_date)),date(Y,strtotime($init_date))): ' . getFirstBusinessDay(date('m', $init_date), date('Y', $init_date)) . '<br>';

//    if (date('j', $init_date) <= date('j',get_first_day(1,date('m', $init_date),date('Y', $init_date)))) {

echo '$init_date > $current_timestamp: ' . ($init_date > $current_timestamp) . '<br>';
echo 'date($init_date) > date($current_timestamp): ' . (date('Y-m-d H:i:s',$init_date) > date('Y-m-d H:i:s',$current_timestamp)) . '<br>';
echo '$current_timestamp < $init_date: ' . ($current_timestamp < $init_date) . '<br>';

// CORRECT Temp Comment:        if (date('j', $init_date) <= getFirstBusinessDay(date('m', $init_date), date('Y', $init_date)) && $init_date > $current_timestamp) {

        if (date('Y-m-d',$init_date) <= date('Y-m-d',strtotime(getFirstBusinessDate(date('Y-m',$init_date) . '-01')))
          && strtotime(getFirstBusinessDate(date('Y-m',$init_date) . '-01') . date('H:i:s',$init_date)) > $current_timestamp
        
        ) {

            echo 'Line: 3' . '<br>';
            $startDate = date('Y-m-d', $init_date);
        } else {
            echo 'Line: 4' . '<br>';
            $startDate = (date('n', $init_date) == 12) ? (date('Y', $init_date) + 1) . '-01-01' : date('Y', $init_date) . '-' . str_repeat('0', 2 - strlen(date('n', $init_date) + 1)) . (date('n', $init_date) + 1) . '-01';
        }
// end temp1


// CORRECT Temp Comment:        echo '$startDate: ' . $startDate . '<br>';

//    echo 'date(j, $startDate): ' . date('j', strtotime($startDate)) . '<br>';
//    echo 'date(Y, $startDate): ' . date('Y', strtotime($startDate)) . '<br>';
//    echo 'date(m, $startDate): ' . date('m', strtotime($startDate)) . '<br>';
//    echo 'date(d, $startDate): ' . date('d', strtotime($startDate)) . '<br>';
// INCORRECT when 2022-01-02:  echo '3-date(Y-m-d,get_first_day(date(j, $startDate),date(m, $startDate),date(Y, $startDate))): ' . date('Y-m-d',get_first_day(date('j', strtotime($startDate)),date('m', strtotime($startDate)),date('Y', strtotime($startDate)))) . '<br>';

// INCORRECT: echo '4-date(Y-m-d, strtotime(2021-01-01 first weekday)): ' . date('Y-m-d', strtotime('2021-01-01 first weekday')) . '<br>';

        $queued_at = getFirstBusinessDate($startDate) . " {$send_at}";
// CORRECT Temp Comment:        echo '5-$queued_at: ' . $queued_at . '<br>';

// CORRECT Temp Comment:        $queued_at = date('Y-m-', strtotime($startDate)) . str_repeat('0', 2 - strlen(getFirstBusinessDay(date('m', strtotime($startDate)), date('Y', strtotime($startDate))))) . getFirstBusinessDay(date('m', strtotime($startDate)), date('Y', strtotime($startDate))) . " {$send_at}";
// CORRECT Temp Comment:        echo '6-$queued_at: ' . $queued_at . '<br>';
    }
//}

echo '$startDate: ' . $startDate . '<br>';
echo '6-$queued_at: ' . $queued_at . '<br>';
?>

</body>
</html>
