<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/public/partials
 */
?>
<?php
    $court = new stdClass();
    $court->id = 1;
    $court->name = 'Court 1';

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="container" style="display: block">
    <div class="page-header">

		<div class="float-right form-inline">
			<div class="btn-group mr-3">
				<button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
				<button class="btn btn-default" data-calendar-nav="today">Today</button>
				<button class="btn btn-primary" data-calendar-nav="next">Next >></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-warning" data-calendar-view="year">Year</button>
				<button class="btn btn-warning" data-calendar-view="month">Month</button>
				<button class="btn btn-warning" data-calendar-view="week">Week</button>
				<button class="btn btn-warning" data-calendar-view="day">Day</button>
			</div>
		</div>

		<h3></h3>
	</div>

    <div id="calendar"></div>
</div>
<!-- reservation form -->
<div class="modal" tabindex="-1" role="dialog" id="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= $court->name ?> <?= __('Reservation', 'resourcescheduler'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  	<form id="resform">
		  	<input type="hidden" name="action" value="add_reservation">
    		<input type="hidden" name="courtid" value="<?= $court->id ?>" />
    		<input type="hidden" name="day" />
    		<input type="hidden" name="hour" />
			<div class="form-group">
				<label for="date"><?= __('Date', 'resourcescheduler'); ?></label>
				<input type="text" class="form-control" id="date" readonly >
			</div>
			<div class="form-group">
				<label for="member"><?= __('Player', 'resourcescheduler'); ?></label>
				<input type="text" class="form-control" id="member" value="<?= $username ?> " readonly>
			</div>
			<div class="form-group">
				<label for="type"><?= __('Type', 'resourcescheduler'); ?></label>
				<select class="form-control" name="type" id="type">
					<?php 
						$html = "";
						foreach ($reservationTypes as $reservationType) {
							$html .= "<option value=" . $reservationType . ">" . $reservationType . "</option>";
						}
						echo $html
					?>
				  </select>
			</div>
			
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" id="submit" class="btn btn-primary" data-dismiss="modal">Save changes</button>
      </div>
    </div>
  </div>
</div> 
<script>
	


"use strict";
function notify(event) {
		var d = jQuery('#dialog');
		d.find('[name="day"]').val(event);
		d.show();
	}

var options = {
    events_source: '/wp-admin/admin-ajax.php',
    view: 'month',
    tmpl_path: '<?php echo (plugins_url() . "/" . $this->plugin_name . "/public/tmpls/") ?>',
    /*http://two.wordpress.test/wp-content/plugins/resourcescheduler/public/tmpls/' */
    tmpl_cache: false,
    day: '2013-03-12',
   
    onAfterViewLoad: function(view) {
        jQuery('.page-header h3').text(this.getTitle());
        jQuery('.btn-group button').removeClass('active');
        jQuery('button[data-calendar-view="' + view + '"]').addClass('active');
        jQuery('a.add-event').on('click', function(){
            var date = jQuery(this).data('cal-date');
            notify(date);
        }) ;
    },
    classes: {
        months: {
            general: 'label'
        }
    }
};

var calendar = jQuery('#calendar').calendar(options);

</script>
