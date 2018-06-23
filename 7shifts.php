<?php

//JSON REQUEST
$url_users = 'https://shiftstestapi.firebaseio.com/users.json'; 
$url_timepunch = 'https://shiftstestapi.firebaseio.com/timePunches.json';
$url_location = 'https://shiftstestapi.firebaseio.com/locations.json';
$json_users     = json_decode(file_get_contents($url_users), true);
$json_timepunch = json_decode(file_get_contents($url_timepunch)); 
$json_location  = json_decode(file_get_contents($url_location)); 

//VARS 
$location_index = array();
$user_index = array();
$timepunch_index = array();

$i = 0;
$i2 = 0;
$user_list = array();
$sum_week = 0;
$week_past = -1;
$week_actual = -1;
$total_sum  = array(array());

foreach ($json_location as $location) {
    $location_index[$i] = $location->id;
    $max_hours_day  = 8;
    $max_hours_week = 30;
    foreach ($json_users[$location_index[$i]] as $user)
    {
        $user_index[$i2] = $user['id'];    
        $user_list[$user_index[$i2]] = $user['firstName'].' '.$user['lastName'];
        $i2++;
    } 
    $i++;
}

$userid_before = -1;

$total_sum_day = array();

echo "<h3> NUMBER OF OVER TIME HOURS PER DAY </H3>";
$min_week = 0;
$max_week = 0;

foreach ($json_timepunch as $timepunch) 
{
    $userid_now = $timepunch->userId;
    
    if ($userid_now != $userid_before)
    {
        $userid_before = $userid_now;
    }     
    $interval = (strtotime($timepunch->clockedOut) - strtotime($timepunch->clockedIn));
    $interval = number_format(($interval/3600), 1, '.', '');
    if ($interval > $max_hours_day)
    $ot_day = $interval - $max_hours_day ;
    $sum_week += $ot_day;
    //$total_sum_day[$userid_now] .= $timepunch->clockedIn.' '.$timepunch->clockedOut.' - OT D : '.$ot_day.' <br>';
    
    $date = strtotime($timepunch->clockedIn);
    $week_actual = idate('W', $date);
    $total_sum[$timepunch->userId][$week_actual] = $sum_week;
    
    if (($min_week == 0) or ($min_week > $week_actual))
    {
        $min_week = $week_actual;
    }
    if (($min_week == 0) or ($max_week < $week_actual))
    {
        $max_week = $week_actual;
    }
    if ($week_actual != $week_past)    {
        $week_past = $week_actual;
        $sum_week = 0;
    }
    echo $timepunch->userId.' - '.$timepunch->clockedIn.' - '.$timepunch->clockedOut.'     OT/D : '.($ot_day).' hours'; 
    echo '<br>';
}

$num_user = count($user_index);

echo $min_week;
echo ' '.$max_week;


?>


