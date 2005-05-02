<?php //$Id$

$perms =& $AppUI->acl();
if (! $perms->checkModule('tasks', 'view'))
	$AppUI->redirect("m=public&a=access_denied");

$proj = $_GET['project'];
$sql = 'SELECT task_id, task_name
        FROM tasks';
if ($proj != 0)
  $sql .= ' WHERE task_project = ' . $proj;
$tasks = db_loadList($sql);
?>

<script language="JavaScript">
function loadTasks()
{
  var tasks = new Array();
  var sel = parent.document.forms['form'].new_task;
  while ( sel.options.length )
    sel.options[0] = null;
    
  sel.options[0] = new Option('[top task]', 0);
  <?php
    $i = 0;
    foreach($tasks as $task)
    {
      ++$i;
    ?>
  sel.options[<?php echo $i; ?>] = new Option('<?php echo $task['task_name']; ?>', <?php echo $task['task_id']; ?>);
    <?php
    }
    ?>
  }
  
  loadTasks();
</script>
