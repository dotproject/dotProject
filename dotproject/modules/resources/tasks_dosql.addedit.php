<?php

// Set the pre and post save functions
global $pre_save, $post_save, $other_resources;

$pre_save[] = "resource_presave";
$post_save[] = "resource_postsave";
$other_resources = null;

/**
 * presave functions are called before the session storage of tab data
 * is destroyed.  It can be used to save this data to be used later in
 * the postsave function.
 */
function resource_presave()
{
  global $other_resources;
  // check to see if we are in the post save list or if we need to
  // interrogate the session.
  $other_resources = setItem('hresource_assign');
	dprint(__FILE__, __LINE__, 5, "setting other resources to $other_resources");
}

/**
 * postsave functions are only called after a succesful save.  They are
 * used to perform database operations after the event.
 */
function resource_postsave()
{
  global $other_resources;
  global $obj;
  $task_id = $obj->task_id;
	dprint(__FILE__, __LINE__, 5, "saving resources, $other_resources");
  if (isset($other_resources)) {
    $value = array();
    $reslist = explode(';', $other_resources);
    foreach ($reslist as $res) {
      if ($res) {
				list ($resource, $perc) = explode('=', $res);
				$value[] = array( $task_id, $resource, $perc );
      }
    }
		// first delete any elements already there, then replace with this
		// list.
		$q = new DBQuery;
		$q->setDelete('resource_tasks');
		$q->addWhere('task_id = ' . $obj->task_id);
		$q->exec(); 
		$q->clear();
    if (count($value)) {
			foreach($value as $v)
			{
				$q->addTable('resource_tasks');
				$q->addInsert('task_id,resource_id,percent_allocated', $v, true);
				$q->exec();
				$q->clear();
			}
    }
  }
}
?>
