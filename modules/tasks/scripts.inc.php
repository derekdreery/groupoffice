<?php
require_once($GO_MODULES->modules['tasks']['class_path'].'tasks.class.inc.php');
$tasks = new tasks();


$settings = $tasks->get_settings($GO_SECURITY->user_id);
$tasklist = $tasks->get_tasklist($settings['default_tasklist_id']);
?>
<script type="text/javascript">
GO.tasks.defaultTasklist = {id: <?php echo $tasklist['id']; ?>, name: "<?php echo $tasklist['name']; ?>"};
GO.tasks.showInactive=<?php if($GO_CONFIG->get_setting('tasks_show_inactive', $GO_SECURITY->user_id)=='1') echo 'true'; else echo 'false'; ?>;
GO.tasks.showCompleted=<?php if($GO_CONFIG->get_setting('tasks_show_completed', $GO_SECURITY->user_id)=='1') echo 'true'; else echo 'false'; ?>;
GO.tasks.remind='<?php echo $settings['remind']; ?>';
GO.tasks.reminderDaysBefore=parseInt(<?php echo $settings['reminder_days']; ?>);
GO.tasks.reminderTime='<?php echo $settings['reminder_time']; ?>';
</script>