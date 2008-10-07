<?php
require_once($GO_MODULES->modules['tasks']['class_path'].'tasks.class.inc.php');
$tasks = new tasks();

$tasklist = $tasks->get_tasklist();
?>
<script type="text/javascript">
GO.tasks.defaultTasklist = {id: <?php echo $tasklist['id']; ?>, name: '<?php echo $tasklist['name']; ?>'};
</script>