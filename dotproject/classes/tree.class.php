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
	private $base_node;

	/**
	 * Constructor. 
	 */
	public function __construct($sort_key = null) {
		$emptynode = null;
		$this->base_node = new CDpTreeNode(0, $emptynode);
		if ($sort_key) {
			$this->base_node->set_sort_key($sort_key);
		}
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
	public function add($parent, $id, &$data) {
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
	public function &find_node($parent) {
		return $this->base_node->find($parent);
	}

	/*
	 * Display using a callback function.
	 * Descend the tree applying the callback to each element.
	 * The callback is passed the data block and a depth indication.
	 */
	public function display($method) {
		$this->base_node->display($method, $this->sort_key);
	}
}

class CDpTreeNode
{
	private $data = null;
	private $id = null;
	private $children = array();
	private $depth = 0;
	private $parent = null;
	private $sort_key = null;
	private $sorted = false;

	public function __construct($id = 0, &$data = null, &$parent = null) {
		$this->id = $id;
		if (isset($data)) {
			$this->data =& $data;
		}
		if (isset($parent)) {
			$this->parent =& $parent;
			$this->depth = $this->parent->depth  + 1;
			$this->sort_key = $parent->sort_key;
		}
		$this->children = array();
	}

	public function set_sort_key($key = null) {
		$this->sort_key = $key;
	}

	public function add($id, &$data) {
		$this->children[] = new CDpTreeNode($id, $data, $this);
	}

	public function &find($id) {
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

	public function display($method) {
		reset($this->children);
		if ($this->sort_key && ! $this->sorted) {
			// Sort based on the supplied sort key
			usort($this->children, array($this, 'do_sort'));
		}
		while (list($id, $node) = each($this->children)) {
			call_user_func($method, $node->depth, $node->data);
			$node->display($method);
		}
	}

	protected function do_sort($a, $b) {
		$key = (array)$this->sort_key;
		foreach ($key as $part) {
			if ($a->data[$part] < $b->data[$part]) {
				return -1;
			}
			elseif ($a->data[$part] > $b->data[$part]) {
				return 1;
			}
		}
		return 0;
	}

}

?>
