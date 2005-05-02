<?php
require_once( "$baseDir/classes/ui.class.php" );
require_once ($AppUI->getSystemClass('date'));
$df = $AppUI->getPref( 'SHDATEFORMAT' );;
$date = $_GET['date'];
$field = $_GET['field'];
$this_day = new CDate($date);
$formatted_date = $this_day->format( $df );
?>
<script language="JavaScript" type="text/javascript">
<!--
	window.parent.document.<?php echo $field; ?>.value = '<?php echo $formatted_date; ?>';
//-->
</script>