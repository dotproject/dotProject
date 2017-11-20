<?php
// $Id$

/**
 * Event handling queue class.
 *
 * The event queue uses the table event_queue to manage
 * event notifications and other timed events, as well as
 * outgoing emails.
 *
 * Copyright 2005, the dotProject team.
 */

if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

class EventQueue {

	protected $table = 'event_queue';
	protected $update_list = array();
	protected $delete_list = array();
	protected $batch_list = array();
	protected $event_count = 0;
	private static $_singleton = null;

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if (! isset(self::$_singleton)) {
			self::$_singleton = new self();
		}
		return self::$_singleton;
	}

	/**
	 * Add an event to the queue.
	 *
	 * The callback can either be the name of a global function or the
	 * name of a class
	 * @param mixed $callback function to call when this event is due.
	 * @param mixed $args Arguments to pass to the callback
	 * @param string $module module, or originator of the event
	 * @param string $type type of event (to allow searching)
	 * @param integer $id id of originating event.
	 * @param integer $date Seconds since 1970 to trigger event.
	 * @param integer $repeat_interval seconds to repeat
	 * @param integer $repeat_count number of times to repeat
	 * @return integer queue id
	 */
	public function add($caller, &$args, $method='execute', $opts = array())
	{
		global $AppUI;

		if (! isset($AppUI)) {
			$user_id = 0;
		} else {
			$user_id = $AppUI->user_id;
		}

		/* Simple expedient, caller should be the $this pointer of the
		   calling class, and the class should provide the method getModuleName.
		   This also is only a short-term solution, in reality we should align classes
		   and modules so we can autoload */
		$class = get_class($caller);
		$full_method = 'EventQueue_' . $method;

		// Set some default values that are overriden with $opts
		$date = 0;
		$repeat_interval = 0;
		$repeat_count = 1;
		$id = 0;
		$type = '';
		$batch = false;
		extract ($opts);

		/* Check that we have a callable string */
		if ($batch) {
			if (! is_callable(array($class, $full_method . '_batched')) &&
			 ! is_callable(array($class, $full_method))) {
				return false;
			 }
		} else {
			if (! is_callable(array($class, $full_method . '_immediate')) &&
			! is_callable(array($class, $full_method))) {
				return false;
			}
		}

		$q = new DBQuery;
		$q->addTable($this->table);
		$q->addInsert('queue_owner', $user_id);
		$q->addInsert('queue_start', $date);
		$q->addInsert('queue_callback', $class . '::' . $method);
		$q->addInsert('queue_data', serialize($args));
		$q->addInsert('queue_repeat_interval', $repeat_interval);
		$q->addInsert('queue_repeat_count', $repeat_count);
		$q->addInsert('queue_module', $caller->getModuleName());
		$q->addInsert('queue_type', $type);
		$q->addInsert('queue_origin_id', $id);
		$q->addInsert('queue_batched', $batch ? 1 : 0);
		if ($q->exec()) {
			$return =  db_insert_id();
		} else {
			$return =  false;
		}
		$q->clear();
		return $return;
	}

	/**
	 * Remove the event from the queue. 
	 * 
	 */
	public function remove($id)
	{
		$q = new DBQuery;
		$q->setDelete($this->table);
		$q->addWhere("queue_id = '$id'");
		$q->exec();
		$q->clear();
	}

	/**
	 * Find a queue record (or records) based upon the
	 * 
	 */
	public function find($module, $type = null, $id = null, $batched = null)
	{
		$q = new DBQuery;
		$q->addTable($this->table);
		$q->addWhere("queue_module = '$module'");
		if (isset($type)) {
			$q->addWhere("queue_type = '$type'");
		}
		if (isset($id)) {
			$q->addWhere("queue_origin_id = '$id'");
		}
		if (isset($batched)) {
			$q->addWhere('queue_batched = ' . ($batched ? '1' : '0'));
		}
		return $q->loadHashList('queue_id');
	}

	/**
	 * Execute a queue entry.  This involves resolving the
	 * method to execute and passing the arguments to it.
	 */
	protected function execute(&$fields)
	{
		global $AppUI;

		if (! isset($this->batch_list[$fields['queue_callback']]) ) {
			$modfile = $AppUI->getModuleClass($fields['queue_module']);
			if (!file_exists($modfile)) {
				$modfile = $AppUI->getSystemClass($fields['queue_module']);
			}
		} else {
			$modfile = $this->batch_list[$fields['queue_callback']]['modfile'];
		}
		include_once $modfile;

		if (strpos($fields['queue_callback'], '::') !== false) {
			list($class, $method) = explode('::', $fields['queue_callback']);
			if (!class_exists($class)) {
				dprint(__FILE__, __LINE__, 2, "Cannot process event: Class $class does not exist");
				return false;
			}
			if (! isset($this->batch_list[$fields['queue_callback']])) {
				$this->batch_list[$fields['queue_callback']] = array(
					'object' => new $class,
					'class' => $class,
					'method' => $method,
					'modfile' => $modfile,
				);
			}
			$object =& $this->batch_list[$fields['queue_callback']]['object'];
			$real_method = 'EventQueue_' . $method;
			if ($fields['queue_batched'] && method_exists($object, $real_method . '_batched')) {
				$real_method .= '_batched';
			} else if ($fields['queue_batched'] == FALSE && method_exists($object, $real_method . '_immediate')) {
				$real_method .= '_immediate';
			} else if (!method_exists($object, $real_method)) {
				dprint(__FILE__, __LINE__, 2, "Cannot process event: Method $class::$method does not exist");
				return false;
			}
			return $object->$real_method($fields);
		}  else {
			return false;
		}
	}

	/**
	 * Utility function to separate the scans into batched
	 * and immediate.  This means we can provide some measure
	 * of control for modules to plan to handle batchable items.
	 */
	public function scan() {
		$this->event_count = 0;
		$this->_scan(false);
		$this->_scan(true);
	}

	public function scanImmediate() {
		$this->event_count = 0;
		$this->_scan(false);
	}

	public function scanBatched() {
		$this->event_count = 0;
		$this->_scan(true);
	}

	/**
	 * Scans the queue for entries that are older than current date.
	 * If it finds one it tries to execute the attached function.
	 * If successful, the entry is removed from the queue, or if
	 * it is a repeatable event the repeat time is added to the
	 * start time and the repeat count (if set) is decremented.
	 */
	protected function _scan($batched = false)
	{
		$q = new DBQuery;
		$q->addTable($this->table);
		$now = time();
		$q->addWhere('queue_start < ' . $now);
		$q->addWhere('queue_batched = ' . ($batched ? '1' : '0'));
		$rid = $q->exec();

		for ($rid; ! $rid->EOF; $rid->moveNext()) {
			if ($this->execute($rid->fields)) {
				$this->update_event($rid->fields);
				$this->event_count++;
			}
		}
		$q->clear();

		$this->commit_updates($batched);
	}

	protected function update_event(&$fields)
	{
		if ($fields['queue_repeat_interval'] > 0 && $fields['queue_repeat_count'] > 0) {
			$fields['queue_start'] += $fields['queue_repeat_interval'];
			$fields['queue_repeat_count']--;
			$this->update_list[] = $fields;
		} else {
			$this->delete_list[] = $fields['queue_id'];
		}
	}

	protected function commit_updates($batched = false)
	{
		$q = new DBQuery;
		if (count($this->delete_list)) {
			$q->setDelete($this->table);
			$q->addWhere("queue_id in (" . implode(',', $this->delete_list) . ")");
			$q->exec();
			$q->clear();
		}
		$this->delete_list = array();

		foreach ($this->update_list as $fields) {
			$q->addTable($this->table);
			$q->addUpdate('queue_repeat_count', $fields['queue_repeat_count']);
			$q->addUpdate('queue_start', $fields['queue_start']);
			$q->addWhere('queue_id = ' . $fields['queue_id']);
			$q->exec();
			$q->clear();
		}
		$this->update_list = array();

		/**
		 * Finally notify the batch handlers that the batch has been terminated.
		 * This is done by calling the method EventQueue_<method_name>_batchTerminate.
		 * Note that at this stage we will have all of the classes loaded, as we will
		 * have executed the class methods to handle the batch requests.
		 */
		if ($batched) {
			foreach ($this->batch_list as $batcher) {
				$method = 'EventQueue_' . $batcher['method'] . '_terminateBatch';
				if (method_exists($batcher['class'], $method)) {
					$batcher['object']->$method();
				}
			}
		}
		$this->batch_list = array();
	}

	public function eventCount() {
		return $this->event_count;
	}

}


