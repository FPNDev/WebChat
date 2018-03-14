<?php 
	function __getFileLines__($file, $line_start, $line_end) {
		$line_counter = 0;
		$fh = fopen($file,'r');
		$lines = [];
		while ((! feof($fh))) {
		    if ($s = fgets($fh,1048576)) {
		        $line_counter++;
		        if($line_counter >= $line_start) {
		        	$s = highlight_string('<?php'.$s.'?>', true);
		        	if(preg_match('/&lt;\?(<\/span><span style=.color: #[0-9A-Fa-f]{6,6}.>)php/', $s)) {
		        		$s = preg_replace('/&lt;\?(<\/span><span style=.color: #[0-9A-Fa-f]{6,6}.>)php/', '$1', $s, 1);
		        	} else {
		        		$s = preg_replace('/&lt;\?php/', '', $s, 1);
		        	}
		        	$s = preg_replace('/(.*)\?&gt;/', '$1', $s, 1);
		        	$lines[$line_counter] = $s;
		        	if($line_counter == $line_end) break;
		        }
		    }
		}
		fclose($fh);
		return $lines;
	}

	$errors = [
		1 => ['Error', '100%', 'red'],
		'ParseError' => ['Parse Error', '100%', 'red'],
		16 => ['Core Error', '100%', 'red'],
		32 => ['Core Warning', '66%', '#f90'],
		64 => ['Compile Error', '100%', 'red'],
		128 => ['Compile Warning', '66%', '#f90'],
		2 => ['Warning', '66%', '#f90'],
		8 => ['Notice', '33%', '#7db'],
		2048 => ['Strict Error', '33%', '#7db'],
	];

	$errtype = $errors[$error['code']] ?? [$error['code'], '100%', 'red'];
?>
<body style="background: #aaa; padding: 0 60px">
	<div style="background: white; padding: 20px; font-family: sans-serif;">
		<?php $file_lines = __getFileLines__($error['file'], $error['line'] - 2, $error['line'] + 2); ?>
		<div style="float: right; overflow: hidden;">
			<div style="color: white; background: #bbb; width: 120px; text-align: center; text-transform: uppercase; font-size: 11px; padding: 5px 0; position: relative">
				<div style="position: absolute; height: 100%; top: 0; width: <?=$errtype[1]?>; background: <?=$errtype[2]?>"></div>
				<div style="position: relative; z-index: 1;"><?=$errtype[0]?></div>
			</div>
		</div>
		<div style="font-size: 12px; color: #c66;"><?=$error['message']?></div>
		<div style="font-size: 10px; color: #c66; margin-left: 15px; line-height: 1.2">
			in <span style="font-style: italic"><?=$error['file']?></span> on line <?=$error['line']?>
		</div>
		<div style="margin-top: 30px;">
			<?php foreach($file_lines as $k => $line): ?>
				<div style="background: <?=$k == $error['line'] ? '#eee' : '#fff'?>; padding: 10px; font-size: 12px">
					<div style="display: inline-block; vertical-align: middle; width: 40px; font-size: 12px; font-family: sans-serif; color: #999;"><?=$k?></div>
					<div style="display: inline-block; vertical-align: middle; width: calc(100% - 50px); word-break: break-all;"><?=$line?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php $file_lines = __getFileLines__($error['file'], $error['line'] - 2, $error['line'] + 2); ?>
		<?php foreach($backtrace as $error): ?>
			<?php $file_lines = __getFileLines__($error['file'], $error['line'] - 2, $error['line'] + 2); ?>
			<div style="font-size: 10px; color: #c66; margin-left: 15px; line-height: 1.2; margin-top: 30px">
				in <span style="font-style: italic"><?=$error['file']?></span> on line <?=$error['line']?>
			</div>
			<div style="margin-top: 30px;">
				<?php foreach($file_lines as $k => $line): ?>
					<div style="background: <?=$k == $error['line'] ? '#eee' : '#fff'?>; padding: 10px; font-size: 12px">
						<div style="display: inline-block; vertical-align: middle; width: 40px; font-size: 12px; font-family: sans-serif; color: #999;"><?=$k?></div>
						<div style="display: inline-block; vertical-align: middle; width: calc(100% - 50px); word-break: break-all;"><?=$line?></div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</body>
<?php exit; ?>