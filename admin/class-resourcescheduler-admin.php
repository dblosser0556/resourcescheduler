<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/admin
 * @author     Dave Blosser <blosserdl@gmail.com>
 */
class Resourcescheduler_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Resourcescheduler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Resourcescheduler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/resourcescheduler-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Resourcescheduler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Resourcescheduler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/resourcescheduler-admin.js', array('jquery'), $this->version, false);

	}

	public function add_resourcescheduler_admin_menu()
	{

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		//add_options_page( 'Reserve Me resource Setup', 'Add resources', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
		//);
		add_menu_page(
			'Manage Reservations',
			__('Reservations', 'resourcescheduler'),
			'manage_options',
			$this->plugin_name,
			array($this, 'load_admin_reservations'),
			'dashicons-calendar',
			6
		);

		add_submenu_page(
			$this->plugin_name,
			'Manage resources and/or Facilities',
			__('Resources', 'resourcescheduler'),
			'manage_options',
			($this->plugin_name) . '-resources',
			array($this, 'load_admin_resources')
		);

		add_submenu_page(
			null,
			'Edit Resource',
			'Edit Resource',
			'manage_options',
			($this->plugin_name) . '-resource',
			array($this, 'load_admin_resource')
		);

		// add roles
		add_submenu_page(
			$this->plugin_name,
			'Manage Roles',
			__('Roles', 'resourcescheduler'),
			'manage_options',
			($this->plugin_name) . '-roles',
			array($this, 'load_admin_roles')
		);

		add_submenu_page(
			null,
			'Edit Role',
			'Edit Role',
			'manage_options',
			($this->plugin_name) . '-role',
			array($this, 'load_admin_role')
		);
	}


	public function load_admin_resources()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-resources.php';
	}

	public function load_admin_resource()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-resource.php';
	}

	public function load_admin_roles()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-roles.php';
	}

	public function load_admin_role()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-role.php';
	}

	public function load_admin_reservations()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-reservations.php';
	}


	private function handleError($msg, $code = 400)
	{
		status_header($code);
		$results['success'] = false;
		$results['msg'] = $msg;
		echo json_encode($results);
		die();
	}

	private function getTable($table)
	{
		global $wpdb;
		return "{$wpdb->prefix}resourcescheduler_{$table}";
	}

	public function addEvent()
	{
		PC::debug($_REQUEST);

		if (!current_user_can('place_reservation')) return $this->handleError('No permission.');

		if (isset($_REQUEST['delete']) && isset($_REQUEST['id'])) { // delete reservation
			$event = $this->getEventById($_REQUEST['id']);
			if ($event == null || $event->userid != wp_get_current_user()->ID) return $this->handleError('Wrong ID or no permissions.');
			$this->deleteEventById($event->id);
			$results['success'] = true;
			$results['msg'] = 'Event ' . $event->title . ' successfully deleted';
			echo json_encode($results);
			die();
		}

		// check if we got a full dataset
		if (!isset($_REQUEST['id']) ||
			!isset($_REQUEST['start']) ||
			!isset($_REQUEST['end']) ||
			!isset($_REQUEST['date']) ||
			!isset($_REQUEST['type']) ||
			!isset($_REQUEST['resourceid'])) return $this->handleError('Missing Data.');

		// check to see if this is an update
		$eventId = $_REQUEST['id'];
				
	
			
		// check to see if current user can add reservation
		$currentUser = $this->getCurrentUser();

		if (!$currentUser->canReserve && $eventId == 0) {
			return $this->handleError('Maximum Number of Reservations in Period', 400);
		}

		$resource = $this->getResourceByID($_REQUEST['resourceid']);
		$eventTypes = $this->getEventTypes($resource->allowedtypes);

		// check court hour restraints
		$starttime = $_REQUEST['start'];
		$endtime = $_REQUEST['end'];

		//make sure the reservation is within the open and close time of the resource.
		if (strtotime("1970-01-01" . $starttime) < strtotime("1970-01-01 " . $resource->open)
			|| strtotime("1970-01-01 " . $endtime) > strtotime("1970-01-01 " . $resource->close))
			return $this->handleError('Invalid time.');

		//convert the date time to UTC
		$startdatetime = new DateTime($_REQUEST['startUTC']);
		$enddatetime = new DateTime($_REQUEST['endUTC']);


		//make sure there are not other reservations during this time.
		if ($this->isReserved($resource->id, $startdatetime->format('Y-m-d H:i:s'), 
			$enddatetime->format('Y-m-d H:i:s'), $eventId ))
			return $this->handleError('Already reserved.', 400);
		

		$title = $starttime . " - " . $endtime . " " .
			(new WP_User(wp_get_current_user()->ID))->display_name;

		$titleshort = (new WP_User(wp_get_current_user()->ID))->display_name;

		$type = $_REQUEST['type'];
		$class = $eventTypes[$type];
		
		// all good!

		global $wpdb;
		$res_table = $this->getTable('reservations');

		if ($eventId == 0) {
		$count = $wpdb->insert(
			$res_table,
			array(
				'resourceid' => (int)$_REQUEST['resourceid'],
				'title' => $title,
				'titleshort' => $titleshort,
				'type' => $type,
				'class' => $class,
				'userid' => wp_get_current_user()->ID,
				'start' => $startdatetime->format('Y-m-d H:i:s'),
				'end' => $enddatetime->format('Y-m-d H:i:s')
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s'
			)
		);

		if (!$count) {
			return $this->handleError('Something went wrong.');
		}

		$results['success'] = true;
		$results['msg'] = 'Inserted '. $title;
		print_r(json_encode($results));
		die();
	}
	else {
		$count = $wpdb->update(
			$res_table,
			array(
				'resourceid' => (int)$_REQUEST['resourceid'],
				'title' => $title,
				'titleshort' => $titleshort,
				'type' => $type,
				'class' => $class, 
				'userid' => wp_get_current_user()->ID,
				'start' => $startdatetime->format('Y-m-d H:i:s'),
				'end' => $enddatetime->format('Y-m-d H:i:s')
			),
			array( 'id' => $_REQUEST['id'] ),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s'
			)
		);

		if (!$count) {
			return $this->handleError('Something went wrong.');
		}

		$results['success'] = true;
		$results['msg'] = 'Updated ' . $title;
		print_r(json_encode($results));
		die();
		
	}
	}

	public function getReservations()
	{
		global $wpdb;
		return $wpdb->get_results("SELECT reservations.*, resources.name as resourcename FROM {$this->getTable('reservations')} as reservations,
	   		{$this->getTable('resources')} as resources
	    	WHERE resources.id=reservations.resourceid 
				AND reservations.start >= NOW() ORDER BY reservations.start");
	}

	public function getEventById($eventId)
	{
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM {$this->getTable('reservations')} WHERE id = $eventId");
	}

	public function deleteEventById($eventId)
	{
		global $wpdb;
		return $wpdb->delete($this->getTable('reservations'), array('id' => $eventId), array('%d'));
	}
	
	public function getEventTypes($allowedTypes) {
		$typeList = explode("|", $allowedTypes);
		$eventTypes = array();
		foreach ($typeList as $type ) {
			preg_match("/(?<=\[)\w+\-\w+/", $type, $class );
			preg_match("/(?!\[)\w+/", $type, $typeName);
			$eventTypes[$typeName[0]] = $class[0];

		}
		return $eventTypes;
	}

	public function getEventsSchedule()
	{
		$event = array();
		$start = date('Y-m-d h:i:s', $_REQUEST['from'] / 1000);
		$end = date('Y-m-d h:i:s', $_REQUEST['to'] / 1000);
		$startUTC = $_REQUEST['utc_offset_from'];
		$endUTC = $_REQUEST['utc_offset_to'];
		$resourceid = $_REQUEST['id'];



		global $wpdb;


		$sql = "SELECT reservations.*, resources.name as resourcename FROM "
			. $this->getTable('reservations') . " as reservations,"
			. $this->getTable('resources') . " as resources "
			. "WHERE resources.id=reservations.resourceid AND reservations.start >= '" . $start . "' "
			. "AND reservations.end <= '" . $end . "' "
			. "AND reservations.resourceid = " . $resourceid . " "
			. "ORDER BY reservations.start";


		$reservations = $wpdb->get_results($sql);



		$event = array();
		foreach ($reservations as $row) {


			$event[] = array(
				'id' => $row->id,
				'title' => $row->title,
				'url' => $row->id,
				'type' => $row->type,
				'class' => $row->class,
				'start' => strtotime($row->start) . '000',
				'end' => strtotime($row->end) . '000',
				'resourcename' => $row->resourcename
			);
		}

		$out = json_encode(array('success' => 1, 'result' => $event));

		echo $out;
		die();
	}

	public function getCurrentUser()
	{
		$current_user = wp_get_current_user();

		$roles = $current_user->roles;



		if ($roles[0] === 'administrator') {
			$maxDate = new DateTime('2200-01-01');
			$currentUser = new stdClass();
			$currentUser->userid = $current_user->ID;
			$currentUser->role = 'administrator';
			$currentUser->maxDate = $maxDate->format('Y-m-d');
			$currentUser->canReserve = true;
			return $currentUser;
		}
		
		// assumes only one role if multiple roles this is a breaking issue
		global $wpdb;
		$query = "SELECT * FROM {$this->getTable('roles')} WHERE slug = '$roles[0]'";



		$role = $wpdb->get_results($query);


		if (!isset($role)) {
			$currentUser = new stdClass();
			$currentUser->userid = $current_user->ID;
			$currentUser->role = '';
			$currentUser->maxDate = '1900-01-01';
			$currentUser->canReserve = false;
			return $currentUser;
		}


		$startDate = new DateTimeImmutable();
		$endDate = $startDate->add(new DateInterval('P' . $role[0]->maxdays . 'D'));

		$count = $this->getEventCountForUser($current_user->ID, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));


		$currentUser = new stdClass();
		$currentUser->userid = $current_user->ID;
		$currentUser->role = $role[0]->name;
		$currentUser->maxDate = $endDate->format('Y-m-d');
		$currentUser->canReserve = ($count < $role[0]->maxres) ? true : false;
		return $currentUser;

	}

	public function getEventCountForUser($userId, $startDate, $endDate)
	{

		$query = "SELECT COUNT(*) FROM {$this->getTable('reservations')} WHERE 
	    userid = '$userId' AND start >= '$startDate' AND end <= '$endDate'";

		global $wpdb;
		$count = $wpdb->get_var($query);

		return $count;
	}

	// function used to see if there are any reservations during the passed in time period.
	public function isReserved($resourceID, $startDate, $endDate, $eventId)
	{
		global $wpdb;
		$query = "SELECT COUNT(*) FROM {$this->getTable('reservations')} WHERE resourceid = $resourceID 
		AND id <> $eventId AND
	    ((start < '$startDate' AND end > '$startDate') OR
		(start < '$endDate' AND end > '$endDate') OR 
		(start >= '$startDate' AND end <= '$endDate'))";

		PC::debug($query);

		$count = $wpdb->get_var($query);
		PC::debug($count > 0);

		return ($count > 0);
	}

	public function getResourceByID($resourceID)
	{
		global $wpdb;
		$table_resources = $this->getTable('resources');
		return $wpdb->get_row("SELECT * FROM $table_resources WHERE id = $resourceID");
	}

}
