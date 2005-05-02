<?php
$debug_file = "{$dPconfig['root_dir']}/files/debug.log";

function writeDebug( $s, $t='', $f='?', $l='?' ) {
	GLOBAL $debug, $debug_file;
	if ( $debug && ($fp = fopen( $debug_file, "at" ))) {
		fputs( $fp, "Debug message from file [$f], line [$l], at: ".strftime( "%H:%S" ) );
		if ($t) {
			fputs( $fp, "\n * * $t * *\n" );
		}
		fputs( $fp, "\n$s\n\n" );
		fclose( $fp );
	}
}
?>
