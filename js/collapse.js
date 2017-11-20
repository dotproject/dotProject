/**
 * Support for collapsible views. 
 *
 * Rows are marked with an ID.  Rows are deleted by ID, or added by ID.
 * The user
 */

var saved_rows = new Comparable;

function toggle_collapse(item, collapse)
{
	var item_image = document.getElementById('image_' + item);
	if (! item_image) {
		return false;
	}
	// Grab the row that belongs to the icon
	var item_elem = document.getElementById('r_' + item);
	var parent = item_elem.parentNode;
	// Check to see if the item is toggled.
	// This braindead method is required because IE does not
	// implement substr correctly and you cannot use negative
	// offsets. Why anyone would use such a crappy browser is
	// beyond me.
	var bottom = item_image.name.substr(item_image.name.length-2,2);
	if (bottom == '_0') {
		// Item is collapsed, expand it.
		if (collapse) {
			return false;
		}
		var orig = saved_rows.find(item);
		if (orig) {
			// Find the next sibling and insert the node before it.
			var next = item_elem.nextSibling;
			for (var j = 0; j < orig.length; j++)
				parent.insertBefore(orig[j], next);
			item_image.name = item_image.id + '_1';
			item_image.src = './images/arrow-down.gif';
		} 
	} else {
		// Item is expanded, collapse it.
		item_image.name = item_image.id + '_0';
		item_image.src = './images/arrow-right.gif';
		var row_array = new Array();
		var rid = 0;
		var sib = item_elem.nextSibling;
		var level_item = document.getElementById('rl_' + item);
		var level = level_item.value;
		while (sib) {
			if (! sib.id) {
				sib = sib.nextSibling;
				continue;
			}
			var sib_id = sib.id.substr(2);
			var sublevel = document.getElementById('rl_' + sib_id).value;
			if (sublevel <= level)
				break;
			var nxt = sib.nextSibling;
			// Now delete the row
			row_array[rid++] = parent.removeChild(sib);
			sib = nxt;
		}
		saved_rows.add(item, row_array);
	}
	return true;
}

function collapse_all(parent)
{
	var parent_elem = document.getElementById(parent);
	for (var i = 0; i < parent_elem.childNodes.length; i++) {
		if (parent_elem.childNodes[i].tagName == 'TR' && parent_elem.childNodes[i].id) {
			toggle_collapse(parent_elem.childNodes[i].id.substr(2), true);
		}
	}
}

var show_tab_function = null;
var hide_tab_function = null;

function show_tab(i)
{
	hide_tabs();
	if (show_tab_function) {
		show_tab_function(i);
		return;
	}
	var tab = document.getElementById('tab_' + i);
	tab.style.display = 'block';
	tab = document.getElementById('toptab_' + i);
	tab.className = 'tabon';
}

function hide_tabs()
{
	if (hide_tab_function) {
		hide_tab_function();
		return;
	}
	var tabs = document.getElementsByTagName('td');
	var i;
	for(i = 0; i < tabs.length; i++)
		if (tabs[i].className == 'tabon')
			tabs[i].className = 'taboff';

	tabs = document.getElementsByTagName('div');
	for(i = 0; i < tabs.length; i++)
		if (tabs[i].className == 'tab')
			tabs[i].style.display = 'none';
} 
