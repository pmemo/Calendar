<?php

require 'libs/Calendar.php';

$calendar = new Calendar('cal.ics');
$events = $calendar->getEvents('2021-01-01');

foreach($events as $event) {
    echo $event['date']['start'].' - '.$event['date']['end'];
    echo '<br>';
    echo $event['title'];
    echo '<br>';
    echo $event['description'];
    echo '<br>';
