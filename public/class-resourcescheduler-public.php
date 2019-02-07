<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/public
 * @author     Dave Blosser <blosserdl@gmail.com>
 */
class Resourcescheduler_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/resourcescheduler-public.css', array(), date("h:i:s"), 'all' );
		wp_enqueue_style( $this->plugin_name . 'calendar', plugin_dir_url( __FILE__ ) . 'css/calendar.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-load-bs',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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
	
		
		wp_enqueue_script( $this->plugin_name . "underscore", plugin_dir_url( __FILE__ ) . 'js/underscore-min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "jstz", plugin_dir_url( __FILE__ ) . 'js/jstz.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "bootstrap", plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . "calendar", plugin_dir_url( __FILE__ ) . 'js/calendar.js', array( 'jquery' ), date("h:i:s"), false );
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/resourcescheduler-public.js', array( 'jquery' ), date("h:i:s"), false );
	}

	private function getTable($table) {
		global $wpdb;
		return "{$wpdb->prefix}resourcescheduler_{$table}";
	}

	public function getResourceByID($resourceID) {
		global $wpdb;
		$table_resources = $this->getTable('resources');
		return $wpdb->get_row("SELECT * FROM $table_resources WHERE id = $resourceID");
	}

	
	public function getCurrentUser() {
		$current_user = wp_get_current_user();
		

		// check to make sure it there is a logged in user.
		// if not return a use with no capabilities
		if ($current_user->ID == 0) {
			$currentUser = new stdClass();
			$currentUser->userid = 0;
			$currentUser->username = "guest";
			$currentUser->role = '';
			$currentUser->maxDate = '1900-01-01';
			$currentUser->canReserve = 0;
			return $currentUser;
		}

		$roles = $current_user->roles;
		
		

		if ($roles[0] === 'administrator') {
			$maxDate = new DateTime('2200-01-01');
			$currentUser = new stdClass();
			$currentUser->userid = $current_user->ID;
			$currentUser->username = $current_user->display_name;
			$currentUser->role = 'administrator';
			$currentUser->maxDate = $maxDate->format('Y-m-d');
			$currentUser->canReserve = 1;
			return $currentUser;
		}
		
		// assumes only one role if multiple roles this is a breaking issue
		global $wpdb;
		$query = "SELECT * FROM {$this->getTable('roles')} WHERE slug = '$roles[0]'";
		
		

		$role = $wpdb->get_results($query);
		
		// if this users role is not in the roles table then set the 
		// user with no capabilities
		if (!isset($role)) {
			$currentUser = new stdClass();
			$currentUser->userid = $current_user->ID;
			$currentUser->role = '';
			$currentUser->username = $current_user->display_name;
			$currentUser->maxDate = '1900-01-01';
			$currentUser->canReserve = 0;
			return $currentUser;
		}

	
		$startDate = new DateTimeImmutable();
		$endDate = $startDate->add(new DateInterval('P'. $role[0]->maxdays . 'D'));
		
		$count = $this->getReservationCountForUser($current_user->ID, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));


		$currentUser = new stdClass();
		$currentUser->userid = $current_user->ID;
		$currentUser->role = $role[0]->name;
		$currentUser->username = $current_user->display_name;
		$currentUser->maxDate = $endDate->format('Y-m-d');
		$currentUser->canReserve = ($count < $role[0]->maxres) ? 1 : 0;
		return $currentUser;

	}

	public function getReservationCountForUser($userId, $startDate, $endDate){

		$query = "SELECT COUNT(*) FROM {$this->getTable('reservations')} WHERE 
	    userid = '$userId' AND start >= '$startDate' AND end <= '$endDate'";

		global $wpdb;
		$count = $wpdb->get_var($query);

		return $count;
	}


	public function public_shortcode( $atts, $content = null ) {
		ob_start();
		include_once( 'partials/'.$this->plugin_name.'-public-display.php' );
		return ob_get_clean();
	}
}
