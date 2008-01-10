<?php

if (! defined('DP_BASE_DIR')) {
	die('This file should not be called directly.');
}

/**
 * Create a tree structure. allow tree traversal, search and update.
 * Mainly for task handling.
 */
class CDpTree
{
	var $base_node;

	/**
	 * Constructor.  This is PHP4 compliant.
	 */
	function CDpTree() {
		$emptynode = null;
		$this->base_node =& new CDpTreeNode(0, $emptynode);
	}

	/**
	 * Add an item that has a parent, an id and some data.
	 * This is ideal for tasks.
	 *
	 * @param integer $parent
	 * @param integer $id
	 * @param mixed $data
	 * @return void
	 */
	function add($parent, $id, &$data) {
		if ($parent == $id) {
			$this->base_node->add($id, $data);
		} else {
			$node =& $this->find_node($parent);
			if ($node) {
				$node->add($id, $data);
			} else {
				// Can't find a parent, pretend it is a base node
				$this->base_node->add($id, $data);
			}
		}
	}

	/**
	 * Return the node whose Id matches
	 */
	function &find_node($parent) {
		return $this->base_node->find($parent);
	}

	/*
	 * Display using a callback function.
	 * Descend the tree applying the callback to each element.
	 * The callback is passed the data block and a depth indication.
	 */
	function display($method) {
		$this->base_node->display($method);
	}
}

class CDpTreeNode
{
	var $data = null;
	var $id = null;
	var $children = array();
	var $depth = 0;
	var $parent = null;

	function CDpTreeNode($id = 0, &$data = null, &$parent = null) {
		$this->id = $id;
		if (isset($data)) {
			$this->data =& $data;
		}
		if (isset($parent)) {
			$this->parent =& $parent;
			$this->depth = $this->parent->depth  + 1;
		}
		$this->children = array();
	}

	function add($id, &$data) {
		$this->children[$id] =& new CDpTreeNode($id, $data, $this);

	}

	function &find($id) {
		$result = false;
		if ($this->id == $id) {
			return $this;
		} else {
			reset ($this->children);
			while (list($newid, $node) = each($this->children)) {
				if ($result =& $node->find($id)) {
					return $result;
				}
			}
		}
		return $result;
	}

	function display($method) {
		reset($this->children);
		while (list($id, $node) = each($this->children)) {
			call_user_func($method, $node->depth, $node->data);
			$node->display($method);
		}
	}

}

?>
