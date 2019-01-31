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
		//add_options_page( 'Reserve Me Court Setup', 'Add Courts', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
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
			'Manage Courts and/or Facilities',
			__('Facilities', 'resourcescheduler'),
			'manage_options',
			($this->plugin_name) . '-facilities',
			array($this, 'load_admin_facilities')
		);

		add_submenu_page(
			null,
			'Edit Facility',
			'Edit Facility',
			'manage_options',
			($this->plugin_name) . '-facility',
			array($this, 'load_admin_facility')
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


	public function load_admin_facilities()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-facilities.php';
	}

	public function load_admin_facility()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/resourcescheduler-facility.php';
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

	private function getTable($table)
	{
		global $wpdb;
		return "{$wpdb->prefix}resourcescheduler_{$table}";
	}

	public function add_event() {
		return;
	}

	public function get_events_schedule()
	{
		$event = array();

		$event[] = array(
			"id" => "293",
			"title" => "This is warning class event with very long title to check how it fits to evet in day view",
			"url" => "http://www.example.com/",
			"class" => "event-warning",
			"start" => "1362938400000",
			"end" => "1363197686300"
		);

		$event[] = array(

			"id" => "256",
			"title" => "Event that ends on timeline",
			"url" => "http://www.example.com/",
			"class" => "event-warning",
			"start" => "1363155300000",
			"end" => "1363227600000"
		);
		$event[] = array(
			"id" => "276",
			"title" => "Short day event",
			"url" => "http://www.example.com/",
			"class" => "event-success",
			"start" => "1363245600000",
			"end" => "1363252200000"
		);
		$event[] = array(
			"id" => "294",
			"title" => "This is information class ",
			"url" => "http=>//www.example.com/",
			"class" => "event-info",
			"start" => "1363111200000",
			"end" => "1363284086400"
		);
		$event[] = array(
			"id" => "297",
			"title" => "This is success event",
			"url" => "http://www.example.com/",
			"class" => "event-success",
			"start" => "1363234500000",
			"end" => "1363284062400"
		);
		$event[] = array(
			"id" => "54",
			"title" => "This is simple event",
			"url" => "http://www.example.com/",
			"class" => "",
			"start" => "1363712400000",
			"end" => "1363716086400"
		);
		$event[] = array(
			"id" => "532",
			"title" => "This is inverse event",
			"url" => "http://www.example.com/",
			"class" => "event-inverse",
			"start" => "1364407200000",
			"end" => "1364493686400"
		);
		$event[] = array(
			"id" => "548",
			"title" => "This is special event",
			"url" => "http://www.example.com/",
			"class" => "event-special",
			"start" => "1363197600000",
			"end" => "1363629686400"
		);
		$event[] = array(
			"id" => "295",
			"title" => "Event 3",
			"url" => "http://www.example.com/",
			"class" => "event-important",
			"start" => "1364320800000",
			"end" => "1364407286400"
		);

		$out = json_encode(array('success' =>1, 'result' => $event));

		echo $out;
		exit;
	}

}
