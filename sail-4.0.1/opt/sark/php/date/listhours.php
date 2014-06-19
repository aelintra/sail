<?php
  require_once "../srkDbClass";
  s$helper->logit("I'm sending days of the month " );
  
  $array = array(); 

$hour = 0; 
$min = 15; // Lets start at "00:15" 

$length = 24 * 4; // The number of times we need to run the loop 

for ($i=0;$i<$length;++$i) 
{ 
  $array[] = str_pad($hour, 2, "0", STR_PAD_LEFT) .':'. str_pad($min, 2, "0", STR_PAD_LEFT); 
  if ($min < 45) { $min = $min + 15; } else { $min = 0; ++$hour; } 
} 


echo "{" . PHP_EOL;
echo "'*':'*'," . PHP_EOL;

foreach ($array as $time) {
	echo "'" . $time . "':'" . $time . "'," . PHP_EOL;
}
echo "'00:00':'00:00'" . PHP_EOL;
  echo "}" . PHP_EOL; 
?>

