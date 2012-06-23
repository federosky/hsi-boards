<?php
$actions = array('FWD','RWD','PWR','RUN','END');
$key = array_rand($actions);

echo($actions[$key]);
