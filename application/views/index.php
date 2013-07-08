
<h1>This is the Calendar View</h1>

	<?php foreach($all_events as $event): ?>
		
		<a href="<?php echo site_url('calendar/single_event_view/' . $event->id) ?>" ><h2><?php echo $event->base_info ?></h2></a>
		
		INSTRUCTOR: <?php echo $event->instructor ?> &nbsp &nbsp; &nbsp COURSE CODE: <?php echo $event->course_code ?><br />
		DATE: <?php echo $event->proper_data ?><br />

		<br />
	<?php endforeach;?>


