<?php
$string = "INCAP
INVAL
KDC
KDCACC
";

$string = preg_replace('/([\w ]+)[\n\r\t]/m', "'$1'," , $string);

$order = array("\r\n", "\n", "\r");
$string = "=> [" . str_replace($order, " ", $string) . "],";

echo "<pre>".$string."</pre>";

