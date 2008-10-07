<?php
require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc');
$cal = new calendar();

$calendar = $cal->get_calendar();
?>
<script type="text/javascript">
GO.calendar.defaultCalendar = {id: <?php echo $calendar['id']; ?>, name: '<?php echo $calendar['name']; ?>'};
</script>