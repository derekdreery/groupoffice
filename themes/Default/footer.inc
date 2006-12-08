
<!--Start Footer-->
<?php
if ($GO_CONFIG->debug)
{
	$load_time = round(getmicrotime()-$load_start, 2);
	go_log(LOG_DEBUG, 'Page: '.$_SERVER['PHP_SELF'].';load time: '.$load_time.'ms;mem. usage: '.memory_get_usage().'bytes; DB Queries:'.$_SESSION['query_count']);
}
?>
</body>
</html>
