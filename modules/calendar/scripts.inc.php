<?php
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
$cal = new calendar();


$settings = $cal->get_settings($GO_SECURITY->user_id);
$calendar = $cal->get_calendar($settings['calendar_id']);
$reminder = $cal->reminder_seconds_to_form_input($settings['reminder']);
?>
<script type="text/javascript">
GO.calendar.defaultCalendar = {id: <?php echo $calendar['id']; ?>, name: "<?php echo $calendar['name']; ?>"};
GO.calendar.defaultBackground='<?php echo $settings['background']; ?>';
GO.calendar.defaultReminderValue='<?php echo $reminder['reminder_value']; ?>';
GO.calendar.defaultReminderMultiplier='<?php echo $reminder['reminder_multiplier']; ?>';
</script>