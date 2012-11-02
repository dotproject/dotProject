<?php /* PUBLIC $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$callback = dPgetCleanParam($_GET,'callback','');
?>
<script language="javascript">
	function setClose(color) {
		window.opener.<?php echo $callback;?>(color);
		window.close();
	}
</script>
<?php
	$colors = dPgetSysVal('ProjectColors');
	if ($dPconfig['restrict_color_selection']) {
?>
<table border="0" cellpadding="1" cellspacing="2" width="292" align="center">
	<tr>
		<td valign="top" colspan="2">
			<strong><?php echo $AppUI->_('Color Selector');?></strong>
		</td>
	</tr>
	<?php
		foreach ($colors as $key=>$value) {
	?>
	<tr>
		<td style="background-color:#<?php echo $value?>; border: 1px solid black;cursor: pointer;" width="30" onClick="setClose('<?php echo $value?>')">&nbsp;</td>
		<td width="300"><a href="javascript:setClose('<?php echo $value?>')"><?php echo $key?></a></td>
	</tr>
	<?php
		}
	?>
</table>
<?php
	} else {
?>
<table border="0" cellpadding="1" cellspacing="0" width="292" align="center">
	<tr>
		<td valign="top">
			<strong><?php echo $AppUI->_('Color Selector');?></strong>
		</td>
	<form>
		<td align="right" valign="bottom">
			<select name="" class="text" onchange="javascript:setClose(this.options[this.selectedIndex].value)">
				<option value="0">- - <?php echo $AppUI->_('Preset');?> - -</option>
<?php
				foreach ($colors as $key=>$value) {
					echo '<option value="' . $value . '">' . $key . "</option>\n";
				}
?>
			</select>
		</td>
	</form>
	</tr>
	<tr>
		<td colspan="2">
			<a href="webpal.map">
				<img src="./images/colorchart.gif" width="292" height="196" border="0" alt="color chart" usemap="#map_webpal" ismap />
			</a>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="left"><font size="1" face="trebuchetms,verdana,arial">
			<?php echo $AppUI->_('colorLegend');?></p>
		</td>
	</tr>
</table>
<map name="map_webpal">
	<area coords="2,2,18,18"  href="javascript:setClose('330000')">
	<area coords="18,2,34,18" href="javascript:setClose('333300')">
	<area coords="34,2,50,18" href="javascript:setClose('336600')">
	<area coords="50,2,66,18" href="javascript:setClose('339900')">
	<area coords="66,2,82,18" href="javascript:setClose('33CC00')">
	<area coords="82,2,98,18" href="javascript:setClose('33FF00')">

	<area coords="98,2,114,18"  href="javascript:setClose('66FF00')">
	<area coords="114,2,130,18" href="javascript:setClose('66CC00')">
	<area coords="130,2,146,18" href="javascript:setClose('669900')">
	<area coords="146,2,162,18" href="javascript:setClose('666600')">
	<area coords="162,2,178,18" href="javascript:setClose('663300')">
	<area coords="178,2,194,18" href="javascript:setClose('660000')">

	<area coords="194,2,210,18" href="javascript:setClose('FF0000')">
	<area coords="210,2,226,18" href="javascript:setClose('FF3300')">
	<area coords="226,2,242,18" href="javascript:setClose('FF6600')">
	<area coords="242,2,258,18" href="javascript:setClose('FF9900')">
	<area coords="258,2,274,18" href="javascript:setClose('FFCC00')">
	<area coords="274,2,290,18" href="javascript:setClose('FFFF00')">


	<area coords="2,18,18,34"  href="javascript:setClose('330033')">
	<area coords="18,18,34,34" href="javascript:setClose('333333')">
	<area coords="34,18,50,34" href="javascript:setClose('336633')">
	<area coords="50,18,66,34" href="javascript:setClose('339933')">
	<area coords="66,18,82,34" href="javascript:setClose('33CC33')">
	<area coords="82,18,98,34" href="javascript:setClose('33FF33')">

	<area coords="98,18,114,34"  href="javascript:setClose('66FF33')">
	<area coords="114,18,130,34" href="javascript:setClose('66CC33')">
	<area coords="130,18,146,34" href="javascript:setClose('669933')">
	<area coords="146,18,162,34" href="javascript:setClose('666633')">
	<area coords="162,18,178,34" href="javascript:setClose('663333')">
	<area coords="178,18,194,34" href="javascript:setClose('660033')">

	<area coords="194,18,210,34" href="javascript:setClose('FF0033')">
	<area coords="210,18,226,34" href="javascript:setClose('FF3333')">
	<area coords="226,18,242,34" href="javascript:setClose('FF6633')">
	<area coords="242,18,258,34" href="javascript:setClose('FF9933')">
	<area coords="258,18,274,34" href="javascript:setClose('FFCC33')">
	<area coords="274,18,290,34" href="javascript:setClose('FFFF33')">


	<area coords="2,34,18,50"  href="javascript:setClose('330066')">
	<area coords="18,34,34,50" href="javascript:setClose('333366')">
	<area coords="34,34,50,50" href="javascript:setClose('336666')">
	<area coords="50,34,66,50" href="javascript:setClose('339966')">
	<area coords="66,34,82,50" href="javascript:setClose('33CC66')">
	<area coords="82,34,98,50" href="javascript:setClose('33FF66')">

	<area coords="98,34,114,50"  href="javascript:setClose('66FF66')">
	<area coords="114,34,130,50" href="javascript:setClose('66CC66')">
	<area coords="130,34,146,50" href="javascript:setClose('669966')">
	<area coords="146,34,162,50" href="javascript:setClose('666666')">
	<area coords="162,34,178,50" href="javascript:setClose('663366')">
	<area coords="178,34,194,50" href="javascript:setClose('660066')">

	<area coords="194,34,210,50" href="javascript:setClose('FF0066')">
	<area coords="210,34,226,50" href="javascript:setClose('FF3366')">
	<area coords="226,34,242,50" href="javascript:setClose('FF6666')">
	<area coords="242,34,258,50" href="javascript:setClose('FF9966')">
	<area coords="258,34,274,50" href="javascript:setClose('FFCC66')">
	<area coords="274,34,290,50" href="javascript:setClose('FFFF66')">


	<area coords="2,50,18,66"  href="javascript:setClose('330099')">
	<area coords="18,50,34,66" href="javascript:setClose('333399')">
	<area coords="34,50,50,66" href="javascript:setClose('336699')">
	<area coords="50,50,66,66" href="javascript:setClose('339999')">
	<area coords="66,50,82,66" href="javascript:setClose('33CC99')">
	<area coords="82,50,98,66" href="javascript:setClose('33FF99')">

	<area coords="98,50,114,66"  href="javascript:setClose('66FF99')">
	<area coords="114,50,130,66" href="javascript:setClose('66CC99')">
	<area coords="130,50,146,66" href="javascript:setClose('669999')">
	<area coords="146,50,162,66" href="javascript:setClose('666699')">
	<area coords="162,50,178,66" href="javascript:setClose('663399')">
	<area coords="178,50,194,66" href="javascript:setClose('660099')">

	<area coords="194,50,210,66" href="javascript:setClose('FF0099')">
	<area coords="210,50,226,66" href="javascript:setClose('FF3399')">
	<area coords="226,50,242,66" href="javascript:setClose('FF6699')">
	<area coords="242,50,258,66" href="javascript:setClose('FF9999')">
	<area coords="258,50,274,66" href="javascript:setClose('FFCC99')">
	<area coords="274,50,290,66" href="javascript:setClose('FFFF99')">


	<area coords="2,66,18,82"  href="javascript:setClose('3300CC')">
	<area coords="18,66,34,82" href="javascript:setClose('3333CC')">
	<area coords="34,66,50,82" href="javascript:setClose('3366CC')">
	<area coords="50,66,66,82" href="javascript:setClose('3399CC')">
	<area coords="66,66,82,82" href="javascript:setClose('33CCCC')">
	<area coords="82,66,98,82" href="javascript:setClose('33FFCC')">

	<area coords="98,66,114,82"  href="javascript:setClose('66FFCC')">
	<area coords="114,66,130,82" href="javascript:setClose('66CCCC')">
	<area coords="130,66,146,82" href="javascript:setClose('6699CC')">
	<area coords="146,66,162,82" href="javascript:setClose('6666CC')">
	<area coords="162,66,178,82" href="javascript:setClose('6633CC')">
	<area coords="178,66,194,82" href="javascript:setClose('6600CC')">

	<area coords="194,66,210,82" href="javascript:setClose('FF00CC')">
	<area coords="210,66,226,82" href="javascript:setClose('FF33CC')">
	<area coords="226,66,242,82" href="javascript:setClose('FF66CC')">
	<area coords="242,66,258,82" href="javascript:setClose('FF99CC')">
	<area coords="258,66,274,82" href="javascript:setClose('FFCCCC')">
	<area coords="274,66,290,82" href="javascript:setClose('FFFFCC')">


	<area coords="2,82,18,98"  href="javascript:setClose('3300FF')">
	<area coords="18,82,34,98" href="javascript:setClose('3333FF')">
	<area coords="34,82,50,98" href="javascript:setClose('3366FF')">
	<area coords="50,82,66,98" href="javascript:setClose('3399FF')">
	<area coords="66,82,82,98" href="javascript:setClose('33CCFF')">
	<area coords="82,82,98,98" href="javascript:setClose('33FFFF')">

	<area coords="98,82,114,98"  href="javascript:setClose('66FFFF')">
	<area coords="114,82,130,98" href="javascript:setClose('66CCFF')">
	<area coords="130,82,146,98" href="javascript:setClose('6699FF')">
	<area coords="146,82,162,98" href="javascript:setClose('6666FF')">
	<area coords="162,82,178,98" href="javascript:setClose('6633FF')">
	<area coords="178,82,194,98" href="javascript:setClose('6600FF')">

	<area coords="194,82,210,98" href="javascript:setClose('FF00FF')">
	<area coords="210,82,226,98" href="javascript:setClose('FF33FF')">
	<area coords="226,82,242,98" href="javascript:setClose('FF66FF')">
	<area coords="242,82,258,98" href="javascript:setClose('FF99FF')">
	<area coords="258,82,274,98" href="javascript:setClose('FFCCFF')">
	<area coords="274,82,290,98" href="javascript:setClose('FFFFFF')">


	<area coords="2,98,18,114"  href="javascript:setClose('0000FF')">
	<area coords="18,98,34,114" href="javascript:setClose('0033FF')">
	<area coords="34,98,50,114" href="javascript:setClose('0066FF')">
	<area coords="50,98,66,114" href="javascript:setClose('0099FF')">
	<area coords="66,98,82,114" href="javascript:setClose('00CCFF')">
	<area coords="82,98,98,114" href="javascript:setClose('00FFFF')">

	<area coords="98,98,114,114"  href="javascript:setClose('99FFFF')">
	<area coords="114,98,130,114" href="javascript:setClose('99CCFF')">
	<area coords="130,98,146,114" href="javascript:setClose('9999FF')">
	<area coords="146,98,162,114" href="javascript:setClose('9966FF')">
	<area coords="162,98,178,114" href="javascript:setClose('9933FF')">
	<area coords="178,98,194,114" href="javascript:setClose('9900FF')">

	<area coords="194,98,210,114" href="javascript:setClose('CC00FF')">
	<area coords="210,98,226,114" href="javascript:setClose('CC33FF')">
	<area coords="226,98,242,114" href="javascript:setClose('CC66FF')">
	<area coords="242,98,258,114" href="javascript:setClose('CC99FF')">
	<area coords="258,98,274,114" href="javascript:setClose('CCCCFF')">
	<area coords="274,98,290,114" href="javascript:setClose('CCFFFF')">


	<area coords="2,114,18,130"  href="javascript:setClose('0000CC')">
	<area coords="18,114,34,130" href="javascript:setClose('0033CC')">
	<area coords="34,114,50,130" href="javascript:setClose('0066CC')">
	<area coords="50,114,66,130" href="javascript:setClose('0099CC')">
	<area coords="66,114,82,130" href="javascript:setClose('00CCCC')">
	<area coords="82,114,98,130" href="javascript:setClose('00FFCC')">

	<area coords="98,114,114,130"  href="javascript:setClose('99FFCC')">
	<area coords="114,114,130,130" href="javascript:setClose('99CCCC')">
	<area coords="130,114,146,130" href="javascript:setClose('9999CC')">
	<area coords="146,114,162,130" href="javascript:setClose('9966CC')">
	<area coords="162,114,178,130" href="javascript:setClose('9933CC')">
	<area coords="178,114,194,130" href="javascript:setClose('9900CC')">

	<area coords="194,114,210,130" href="javascript:setClose('CC00CC')">
	<area coords="210,114,226,130" href="javascript:setClose('CC33CC')">
	<area coords="226,114,242,130" href="javascript:setClose('CC66CC')">
	<area coords="242,114,258,130" href="javascript:setClose('CC99CC')">
	<area coords="258,114,274,130" href="javascript:setClose('CCCCCC')">
	<area coords="274,114,290,130" href="javascript:setClose('CCFFCC')">


	<area coords="2,130,18,146"  href="javascript:setClose('000099')">
	<area coords="18,130,34,146" href="javascript:setClose('003399')">
	<area coords="34,130,50,146" href="javascript:setClose('006699')">
	<area coords="50,130,66,146" href="javascript:setClose('009999')">
	<area coords="66,130,82,146" href="javascript:setClose('00CC99')">
	<area coords="82,130,98,146" href="javascript:setClose('00FF99')">

	<area coords="98,130,114,146"  href="javascript:setClose('99FF99')">
	<area coords="114,130,130,146" href="javascript:setClose('99CC99')">
	<area coords="130,130,146,146" href="javascript:setClose('999999')">
	<area coords="146,130,162,146" href="javascript:setClose('996699')">
	<area coords="162,130,178,146" href="javascript:setClose('993399')">
	<area coords="178,130,194,146" href="javascript:setClose('990099')">

	<area coords="194,130,210,146" href="javascript:setClose('CC0099')">
	<area coords="210,130,226,146" href="javascript:setClose('CC3399')">
	<area coords="226,130,242,146" href="javascript:setClose('CC6699')">
	<area coords="242,130,258,146" href="javascript:setClose('CC9999')">
	<area coords="258,130,274,146" href="javascript:setClose('CCCC99')">
	<area coords="274,130,290,146" href="javascript:setClose('CCFF99')">


	<area coords="2,146,18,162"  href="javascript:setClose('000066')">
	<area coords="18,146,34,162" href="javascript:setClose('003366')">
	<area coords="34,146,50,162" href="javascript:setClose('006666')">
	<area coords="50,146,66,162" href="javascript:setClose('009966')">
	<area coords="66,146,82,162" href="javascript:setClose('00CC66')">
	<area coords="82,146,98,162" href="javascript:setClose('00FF66')">

	<area coords="98,146,114,162"  href="javascript:setClose('99FF66')">
	<area coords="114,146,130,162" href="javascript:setClose('99CC66')">
	<area coords="130,146,146,162" href="javascript:setClose('999966')">
	<area coords="146,146,162,162" href="javascript:setClose('996666')">
	<area coords="162,146,178,162" href="javascript:setClose('993366')">
	<area coords="178,146,194,162" href="javascript:setClose('990066')">

	<area coords="194,146,210,162" href="javascript:setClose('CC0066')">
	<area coords="210,146,226,162" href="javascript:setClose('CC3366')">
	<area coords="226,146,242,162" href="javascript:setClose('CC6666')">
	<area coords="242,146,258,162" href="javascript:setClose('CC9966')">
	<area coords="258,146,274,162" href="javascript:setClose('CCCC66')">
	<area coords="274,146,290,162" href="javascript:setClose('CCFF66')">


	<area coords="2,162,18,178"  href="javascript:setClose('000033')">
	<area coords="18,162,34,178" href="javascript:setClose('003333')">
	<area coords="34,162,50,178" href="javascript:setClose('006633')">
	<area coords="50,162,66,178" href="javascript:setClose('009933')">
	<area coords="66,162,82,178" href="javascript:setClose('00CC33')">
	<area coords="82,162,98,178" href="javascript:setClose('00FF33')">

	<area coords="98,162,114,178"  href="javascript:setClose('99FF33')">
	<area coords="114,162,130,178" href="javascript:setClose('99CC33')">
	<area coords="130,162,146,178" href="javascript:setClose('999933')">
	<area coords="146,162,162,178" href="javascript:setClose('996633')">
	<area coords="162,162,178,178" href="javascript:setClose('993333')">
	<area coords="178,162,194,178" href="javascript:setClose('990033')">

	<area coords="194,162,210,178" href="javascript:setClose('CC0033')">
	<area coords="210,162,226,178" href="javascript:setClose('CC3333')">
	<area coords="226,162,242,178" href="javascript:setClose('CC6633')">
	<area coords="242,162,258,178" href="javascript:setClose('CC9933')">
	<area coords="258,162,274,178" href="javascript:setClose('CCCC33')">
	<area coords="274,162,290,178" href="javascript:setClose('CCFF33')">


	<area coords="2,178,18,194"  href="javascript:setClose('000000')">
	<area coords="18,178,34,194" href="javascript:setClose('003300')">
	<area coords="34,178,50,194" href="javascript:setClose('006600')">
	<area coords="50,178,66,194" href="javascript:setClose('009900')">
	<area coords="66,178,82,194" href="javascript:setClose('00CC00')">
	<area coords="82,178,98,194" href="javascript:setClose('00FF00')">

	<area coords="98,178,114,194"  href="javascript:setClose('99FF00')">
	<area coords="114,178,130,194" href="javascript:setClose('99CC00')">
	<area coords="130,178,146,194" href="javascript:setClose('999900')">
	<area coords="146,178,162,194" href="javascript:setClose('996600')">
	<area coords="162,178,178,194" href="javascript:setClose('993300')">
	<area coords="178,178,194,194" href="javascript:setClose('990000')">

	<area coords="194,178,210,194" href="javascript:setClose('CC0000')">
	<area coords="210,178,226,194" href="javascript:setClose('CC3300')">
	<area coords="226,178,242,194" href="javascript:setClose('CC6600')">
	<area coords="242,178,258,194" href="javascript:setClose('CC9900')">
	<area coords="258,178,274,194" href="javascript:setClose('CCCC00')">
	<area coords="274,178,290,194" href="javascript:setClose('CCFF00')">
</map>
<?php
	}
?>
