<?php
function dplink_init()
{
	pnModSetVar('dplink', 'url',  '/dotproject');
	pnModSetVar('dplink', 'use_window', 0);
	pnModSetVar('dplink', 'use_postwrap', 0);
	return true;
}

function dplink_upgrade($oldversion)
{
	switch ($oldversion) {
		case '1.0':
			break;
		case '1.01':
			break;
	}
	return true;
}

function dplink_delete()
{
	pnModDelVar('dplink', 'url');
	pnModDelVar('dplink', 'use_window');
	pnModDelVar('dplink', 'use_postwrap');
	return true;
}
?>
