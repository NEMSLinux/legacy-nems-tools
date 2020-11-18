<?php
  if (file_exists('/var/log/gpioe.log')) {
    $log = file_get_contents('/var/log/gpioe.log');
    echo str_replace('. ','.<br /><br />',$log);
  } else {
    echo 'No connection to NEMS Linux...';
  }
?>
