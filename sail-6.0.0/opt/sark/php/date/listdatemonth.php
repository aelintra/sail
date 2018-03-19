<?php

  require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass");

  $helper->logit("I'm sending days of the month ",3 );
  
  echo "{" . PHP_EOL;
  echo "'*':'*'," . PHP_EOL;
  for ($i=1;$i<32;$i++) {

	echo "'" . $i . "':'" . $i . "'," . PHP_EOL;
  }
  echo "}" . PHP_EOL; 
?>
