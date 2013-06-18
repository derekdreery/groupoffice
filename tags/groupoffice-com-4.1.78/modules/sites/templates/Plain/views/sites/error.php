<?php
$this->setPageTitle("Error");
?>


<p style="color:red;"><?php echo $error->getMessage(); ?></p>

<?php if(GO::config()->debug): ?>


<pre><?php echo (string) $error; ?></pre>

<?php echo "<h1>".get_class($error)."</h1>";
//A lot of crap for pretty stack traces
function highlightSource($fileName, $lineNumber, $showLines)
	    {
	        $lines = file_get_contents($fileName);
	        $lines = highlight_string($lines, true);
	        $lines = explode("<br />", $lines);
	 
	        $offset = max(0, $lineNumber - ceil($showLines / 2));
	 
	        $lines = array_slice($lines, $offset, $showLines);
	 
	        $html = '';
	        foreach ($lines as $line) {
	            $offset++;
	            $line = '<em class="lineno">' . sprintf('%4d', $offset) . ' </em>' . $line . '<br/>';
	            if ($offset == $lineNumber) {
	                $html .= '<div style="background: #ffc">' . $line . '</div>';
	            } else {
	                $html .= $line;
	            }
	        }
	 
	        return $html;
	    } 
		$html  = '<style type="text/css">'
						. '.stacktrace p { margin: 0; padding: 0; }'
						. '.source { border: 1px solid #000; overflow: auto; background: #fff;'
						. ' font-family: monospace; font-size: 12px; margin: 0 0 25px 0 }'
						. '.lineno { color: #333; }'
						. '</style>'
						. '<div class="stacktrace">';
		foreach ($error->getTrace() as $trace)
		{
				$html .= '<p>File: ' . $trace['file'] . ' Line: ' . $trace['line'] . '</p>'
							. '<div class="source">'
							. highlightSource($trace['file'], $trace['line'], 5)
							. '</div>';
		}
		$html .= '</div>'; 
		echo $html; ?>

<?php endif; ?>
