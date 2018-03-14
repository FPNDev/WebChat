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
<?=json_encode([
    'error' => [
        'type' => $errtype,
        'file' => $error['file'],
        'line' => [
            'number' => $error['line'],
            'contents' => $file_lines[$error['line']]
        ],
        'message' => $error['message']
    ]
])?>