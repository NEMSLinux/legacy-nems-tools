<?php
  if (file_exists('/var/log/gpioe.log')) {
    $log = file_get_contents('/var/log/gpioe.log');
    echo str_replace(array('"','. ','Pin ','Iterat','Omzlo'),array('','.<br />','<br />Pin ','<br />Iterat','<br />Omzlo'),$log);
  } else {
    echo 'No connection to NEMS Linux...';
  }
?>
