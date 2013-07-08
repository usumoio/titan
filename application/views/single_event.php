
<h1><?php echo $event->base_info ?></h1>

INSTRUCTOR: <?php echo $event->instructor ?> &nbsp &nbsp; &nbsp COURSE CODE: <?php echo $event->course_code ?><br />
DATE: <?php echo $event->proper_data ?><br />
START TIME: <?php echo $event->start_time ?><br />
END TIME: <?php echo $event->end_time ?><br />
ADDRESS: <?php echo $event->address ?><br />
PRONE #: <?php echo $event->phone_number ?><br /><br />
COURSE DESCRIPTION: <?php echo $event->decsription ?><br />
<br />

<a href="<?php echo site_url('calendar/index') ?>" ><h4>Back</h4></a>
