<?php

echo "a";
ob_start();
echo "b";
$buffer = ob_get_contents();
echo "c";
ob_end_clean();
echo "d";
echo $buffer;
