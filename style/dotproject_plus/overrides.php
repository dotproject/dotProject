<?php 
class CTitleBlock extends CTitleBlock_core {
	function show() {
		global $AppUI;
		$CR = "\n";
		$CT = "\n\t";
		$s = $CR . '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
		$s .= $CR . '<tr>';
		$s .= ($CR . '<td align="left" width="100%" nowrap="nowrap"><h1>' 
		       . $AppUI->_($this->title) . '</h1></td>');
		foreach ($this->cells1 as $c) {
			$s .= $c[2] ? $CR . $c[2] : '';
			$s .= $CR . '<td align="right" nowrap="nowrap"' . ($c[0] ? (' '.$c[0]): '') . '>';
			$s .= $c[1] ? $CT . $c[1] : '&nbsp;';
			$s .= $CR . '</td>';
			$s .= $c[3] ? $CR . $c[3] : '';
		}
		if ($this->showhelp) {
			$s .= '<td nowrap="nowrap" width="20" align="right">';
			$s .= ("\n\t" . '<a href="#' . $this->helpref 
			       . '" onClick="javascript:window.open(\'?m=help&amp;dialog=1&amp;hid=' 
				   . $this->helpref 
				   . "', 'contexthelp', 'width=400,height=400,left=50,top=50,scrollbars=yes," 
			       . 'resizable=yes\')" title="' . $AppUI->_('Help') . '">');
			$s .= "\n\t\t" . dPshowImage('./images/icons/stock_help-16.png', '16', '16', 
			                             $AppUI->_('Help'));
			$s .= "\n\t" . '</a>';
			$s .= "\n</td>";
		}
		$s .= "\n</tr>";
		$s .= "\n</table>";
		if (count($this->crumbs) || count($this->cells2)) {
			$crumbs = array();
			foreach ($this->crumbs as $k => $v) {
				$t = (($v[1]) ? ('<img src="' . dPfindImage($v[1], $this->module) 
				                 . '" border="" alt="" />&nbsp;') : '');
				$t .= $AppUI->_($v[0]);
				$crumbs[] = ('<a href="'. $k .'">' . $t . '</a>');
			}
			$s .= "\n" . '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
			$s .= "\n<tr>";
			$s .= "\n\t" . '<td nowrap="nowrap">';
			$s .= "\n\t\t" . '<strong>' . implode(' : ', $crumbs) . '</strong>';
			$s .= "\n\t" . '</td>';
			
			foreach ($this->cells2 as $c) {
				$s .= $c[2] ? "\n$c[2]" : '';
				$s .= "\n\t" . '<td align="right" nowrap="nowrap"' . ($c[0] ? " $c[0]" : '') . '>';
				$s .= $c[1] ? "\n\t$c[1]" : '&nbsp;';
				$s .= "\n\t" . '</td>';
				$s .= $c[3] ? "\n\t$c[3]" : '';
			}
			$s .= "\n</tr>\n</table>";
		}
		echo "$s";
	}
}
class CTabBox extends CTabBox_core {
	function show( $extra='', $js_tabs = false ) {
		GLOBAL $AppUI, $dPconfig, $currentTabId, $currentTabName;
		$uistyle = $AppUI->getPref( 'UISTYLE' ) ? $AppUI->getPref( 'UISTYLE' ) : $dPconfig['host_style'];
		if (! $uistyle)
		  $uistyle = 'default';
		reset( $this->tabs );
		$s = '';
		if (@$AppUI->getPref( 'TABVIEW' ) == 0) {
			$s .= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$s .= "<tr>\n";
			$s .= "<td nowrap=\"nowrap\">";
			$s .= "<a href=\"".$this->baseHRef."tab=0\">".$AppUI->_('tabbed')."</a> : ";
			$s .= "<a href=\"".$this->baseHRef."tab=-1\">".$AppUI->_('flat')."</a>";
			$s .= "</td>\n".$extra."\n</tr>\n</table>\n";
			echo $s;
		} else {
			if ($extra) {
				echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n<tr>\n".$extra."</tr>\n</table>\n";
			} else {
				echo "<img src=\"./images/shim.gif\" height=\"10\" width=\"1\" alt=\"\" />";
			}
		}
		if ($this->active < 0 || @$AppUI->getPref( 'TABVIEW' ) == 2 ) {
			echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			foreach ($this->tabs as $k => $v) {
				echo "<tr><td><strong>".($v[2] ? $v[1] : $AppUI->_($v[1]))."</strong></td></tr>\n";
				echo "<tr><td>";
				$currentTabId = $k;
				$currentTabName = $v[1];
				include $this->baseInc.$v[0].".php";
				echo "</td></tr>\n";
			}
			echo "</table>\n";
		} else {
			$s = '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
			$s .= '<tr><td><table border="0" cellpadding="0" cellspacing="0">';
			if ( count($this->tabs)-1 < $this->active ) {
				$this->active = 0;
			}
			foreach( $this->tabs as $k => $v ) {
				$class = ($k == $this->active) ? 'tabon' : 'taboff';
				$sel = ($k == $this->active) ? 'Selected' : '';
				$s .= '<td id="toptab_'.$k.'" valign="middle" nowrap="nowrap"';
				$s .= " class=\"$class\"";
				$s .= '>&nbsp;<a href="';
				if ($this->javascript)
					$s .= "javascript:" . $this->javascript . "({$this->active}, $k)";
				else if  ($js_tabs)
					$s .= 'javascript:show_tab(' . $k . ')';
				else
					$s .= $this->baseHRef.'tab='.$k;
				$s .='">'.($v[2] ? $v[1] : $AppUI->_($v[1])).'</a>&nbsp;</td>';
				$s .= '<td width="1" class="tabsp"><img src="./images/shim.gif" height="1" width="1" /></td>';
			}
			$s .= '</table></td></tr>';
			$s .= '<tr><td width="100%" colspan="'.(count($this->tabs)*4 + 1).'" class="tabox">';
			echo $s;
			if ( $this->tabs[$this->active][0] != "" ) {
				$currentTabId = $this->active;
				$currentTabName = $this->tabs[$this->active][1];
				if (!$js_tabs)
					require $this->baseInc.$this->tabs[$this->active][0].'.php';
			}
			if ($js_tabs) {
				foreach( $this->tabs as $k => $v ) {
					echo '<div class="tab" id="tab_'.$k.'">';
					$currentTabId = $k;
					$currentTabName = $v[1];
					require $this->baseInc.$v[0].'.php';
					echo '</div>';
					echo '<script language="JavaScript" type="text/javascript">
<!--
show_tab('.$this->active.');
//-->
</script>';

				}
			}
			echo '</td></tr></table>';
		}
	}
}
?>
