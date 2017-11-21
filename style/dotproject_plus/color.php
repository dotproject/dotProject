<?php
/* green
$style_color_1 = "#9dc44b"; // tab on
$style_color_2 = "#62821a"; // tab off
$style_color_3 = "#d5eda4"; // borders
$style_color_4 = "#d8eab7"; // heads
*/

/*cyan
$style_color_1 = "#78CDAD"; // tab on
$style_color_2 = "#2B7156"; // tab off
$style_color_3 = "#B0E1CF"; // borders
$style_color_4 = "#DCF1EA"; // heads
*/
 
/*yellow
$style_color_1 = "#b8b95e"; // tab on
$style_color_2 = "#71712b"; // tab off
$style_color_3 = "#e0e1b0"; // borders
$style_color_4 = "#f0f2c5"; // heads
*/

//gold/orange

$style_color_1 = "#BDBDBD";//#E6B800"; // tab on
$style_color_2 = "#CCA300"; // tab off
$style_color_3 = "#665200"; // borders (tabs font color)
$style_color_4 = "#e6b800";//"#FFD700;#ffcc00"; // heads

/*
//gray/black
$style_color_1 = "#cccccc"; // tab on
$style_color_2 = "#999999"; // tab off
$style_color_3 = "#ffffff"; // borders (tabs font color)
$style_color_4 = "#c0c0c0";//"#FFE680"; // heads (tabs)
*/


/* blue */
/*
$style_color_1 = "#97adac"; // tab on
$style_color_2 = "#455756"; // tab off
$style_color_3 = "#c2cfce"; // borders
$style_color_4 = "#e4e9e9"; // heads
 */
 
?>
<style type="text/css">
	#container_header .open {
		color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	#container_header .open:hover {
		color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	#container_login strong {
		color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	#container_company_name a:hover {
		color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	#container_footer a {
		color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	td.tabon {
		background-color:<?php echo $style_color_1 ?>; /* theme color 1 */
		border-top:5px solid <?php echo $style_color_1 ?>; /* theme color 1 */
	}
	td.taboff {
		background-color:<?php echo $style_color_2 ?>; /* theme color 2 */
	}
	td.tabox {
		background-color:<?php echo $style_color_1 ?>; /* theme color 1 */
		border:10px solid <?php echo $style_color_1 ?>; /* theme color 1 */
	}
	td.tabon a:hover {
		color:<?php echo $style_color_2 ?>; /* theme color 2 */
	}
	td.taboff a {
		color:<?php echo $style_color_3 ?>; /* theme color 3 */
	}
	table.tbl th {
		background-color:<?php echo $style_color_4 ?>; /* theme color 4 */
	}
	table.tbl a:hover {
		color:<?php echo $style_color_2 ?>; /* theme color 2 */
	}
	table.tbl {
		background-color:<?php echo $style_color_3 ?>; /* theme color 3 */
		border:3px solid <?php echo $style_color_2 ?>; /* theme color 2 */
	}
	table.std strong {
		color:<?php echo $style_color_2 ?>; /* theme color 2 */
	}
	table.std table td {
		color:<?php echo $style_color_2 ?>; /* theme color 2 */
	}
	table.mocal td.today {
		background-color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
	table.minical td.today {
		background-color:<?php echo $style_color_1 ?>; /* theme color 1 */
	}
</style>