<?php 
	function __getFileLines__($file, $line_start, $line_end) {
		$line_counter = 0;
		$fh = fopen($file,'r');
		$lines = [];
		while ((! feof($fh))) {
		    if ($s = fgets($fh,1048576)) {
		        $line_counter++;
		        if($line_counter >= $line_start) {
		        	$lines[$line_counter] = $s;
		        	if($line_counter == $line_end) break;
		        }
		    }
		}
		fclose($fh);
		return $lines;
	}

	$errors = [
		1 => 'Error',
		'ParseError' => 'Parse Error',
		16 => 'Core Error',
		32 => 'Core Warning',
		64 => 'Compile Error',
		128 => 'Compile Warning',
		2 => 'Warning',
		8 => 'Notice',
		2048 => 'Strict Error'
	];

	$errtype = $errors[$error['code']] ?? $error['code'];
?>
<?php $file_lines = __getFileLines__($error['file'], $error['line'], $error['line']); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\r\n"; ?>
<error>
    <type><?=htmlspecialchars($errtype)?></type>
    <file><?=htmlspecialchars($error['file'])?></file>
    <line>
        <number><?=$error['line']?></number>
        <contents><?=htmlspecialchars($file_lines[$error['line']])?></contents>
    </line>
    <message><?=htmlspecialchars($error['message'])?></message>
</error>