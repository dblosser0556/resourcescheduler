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
   $atts = array_change_key_case((array)$atts, CASE_LOWER);
	 if (!isset($atts['id'])) wp_die(__("Resource Id not set.", 'resourcescheduler'));
	 $resourceID = (int)$atts['id'];
	 if ($resourceID == 0) wp_die(__("Resouce Id invalid.", 'resourcescheduler'));
	 
	 
	 $resource = $this->getResourceByID($resourceID);
	 if ($resource == null) wp_die(__("Resource not found.", 'resourcescheduler'));
	 
	
	 
	 // get the capabilities of the current user. 
	 $currentUser = $this->getCurrentUser();

	 //get the list of reservation types for the drop down.
	 $reservationTypes = explode('|', $resource->allowedtypes);
	 $types = array();
	 foreach($reservationTypes as $type) {
		preg_match("/(?!\[)\w+/", $type, $typeName);
		$types[] = $typeName[0];
	 }
	 $reservationTypesJA = json_encode($types);


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="alert" class="alert alert-info alert-dismissible" role="alert" style="display:none">
	<button id="alertAlert" type="button" class="close" data-dismiss="alert">&times;</button>
	<span id="alertText">Reached the end of the calendar.</span>
</div>

<div id="success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
	<button id="messageSuccess" type="button" class="close" data-dismiss="alert">&times;</button>
	<span id="successText">Reached the end of the calendar.</span>
</div>

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
	<div  id="loader" class="smt-spinner-circle" style="display:none">
   		<div class="smt-spinner"></div>
	</div>
    <div id="calendar"></div>
</div>
<div class="modal fade" id="events-modal">
    <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
					<h5 class="modal-title">Event</h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
					<button type="button" id="close"  data-dismiss="modal" class="btn btn-secondary" >Cancel</button>
					<button type="submit" id="submit" data-dismiss="modal" class="btn btn-primary" >Save changes</button>
					<button type="submit" id="delete" data-dismiss="modal" class="btn btn-warning" >Delete</button>
                </div>
            </div>
        </div>
</div>


<script>
	


"use strict";
const backendURL = jQuery("form.resform").attr('action');

var options = {
    events_source: '/wp-admin/admin-ajax.php', 
	view: 'month',
	modal: '#events-modal',
	modal_type: 'template',
	modal_title : function (e) {return e.title},
	reservationTypes: <?= $reservationTypesJA ?> ,
	username : '<?= $currentUser->username ?>',
	tmpl_path: '<?= (plugins_url() . "/" . $this->plugin_name . "/public/tmpls/") ?>',
    /*http://two.wordpress.test/wp-content/plugins/resourcescheduler/public/tmpls/' */
	tmpl_cache: false,
	time_start: '<?= $resource->open ?>',
	time_end: '<?= $resource->close ?>',
	time_split: '<?= $resource->time_split ?>',
	max_event_length_minutes: '<?= $resource->max_reservation_minutes ?>',
	format12: false,
	resourceid: <?= $resourceID ?>,
	canReserve: <?= $currentUser->canReserve ?>,
	maxReserveDate: '<?= $currentUser->maxDate ?>',
    onAfterViewLoad: function(view) {
        jQuery('.page-header h3').text(this.getTitle());
        jQuery('.btn-group button').removeClass('active');
        jQuery('button[data-calendar-view="' + view + '"]').addClass('active');
        //jQuery('a.add-event').on('click', function(){
		//	var caldate = jQuery(this).data('cal-date');
        //    handleAddEvent(caldate);
        //}) ;
	},
	onAfterModalShown: function(events) {
		var eventId = jQuery('#resform input[name="id"]').val();
		if (eventId === "0") {
			jQuery('button#delete').hide();
		} else {
			jQuery('button#delete').show();
		}
	},
	
    classes: {
        months: {
            general: 'label'
        }
    }
};



jQuery("button#submit").click(function(){
			var date = jQuery('#resform [name="date"]').val();
			var start = jQuery('#resform [name="start"]').val();
			var end = jQuery('#resform [name="end"]').val();


			var startDate = new Date(date + ' ' + start);
			var startUTC = startDate.toISOString();
			var endDate = new Date(date + ' ' + end);
			var endUTC = endDate.toISOString();
			var resData = jQuery('#resform').serialize();
			var resData = resData + '&startUTC=' + startUTC + '&endUTC=' + endUTC;
			console.log(resData);

			jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php',
				data: resData,
				beforeSend: function() {
					jQuery("#loader").show();
				},
			
				success: function (response) {
					console.log('add success ', response);
					var res = JSON.parse(response);
					
					
					if (res.success)
						displaySuccess(res.msg);
					else
						displayAlert(res.msg);

					calendar.view(calendar.options.view);
					jQuery("#loader").hide();
					jQuery("button#delete").show();
				},
				error: function (response) {
					console.log('add error ', response);
					var errmsg = JSON.parse(response.responseText);
					displayAlert(errmsg.msg);
					jQuery("#loader").hide();
					jQuery("button#delete").show();
				}
			});
		});

	jQuery("button#delete").click(function(){
		var iD = jQuery('#resform [name="id"]').val();
		console.log(iD);
		jQuery.ajax({
			type: "POST",
			url: '/wp-admin/admin-ajax.php',
			data: "action=addEvent&id=" + jQuery('#resform [name="id"]').val() + "&delete=true",
			beforeSend: function() {
				jQuery("#loader").show();
			},
		
			success: function (response) {
				console.log('success ', response);
				var res = JSON.parse(response);
				console.log(res);
				
				if (res.success)
					displaySuccess(res.msg);
				else
					displayAlert(res.msg);

				calendar.view(calendar.options.view);
				jQuery("#loader").hide();
			},
			error: function (response) {
				console.log('error ', response);
				var errmsg = response.responseText;
				displayAlert(errmsg);
				jQuery("#loader").hide();
			},
			done: function (response) {
				console.log('done ', response);

			}
		});
	});

	jQuery("button#close").click(function(){
		jQuery("#dialog").hide();
	});

	function displayAlert(msg) {
		jQuery("#alertText").text(msg);
		jQuery("#alert").show();
	}

	function displaySuccess(msg) {
		console.log('display success fired');
		jQuery("#successText").text(msg);
		jQuery("#success").show();
	}


	var calendar = jQuery('#calendar').calendar(options);

</script>
