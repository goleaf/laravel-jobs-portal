<?php
// Set unlimited memory for PHP execution
ini_set('memory_limit', '-1');

// Execute the command passed in the arguments
$command = "composer " . implode(' ', array_slice($argv, 1));
echo "Executing with unlimited memory: $command\n";
passthru($command, $return_code);
exit($return_code); 