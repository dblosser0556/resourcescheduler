<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/includes
 * @author     Dave Blosser <blosserdl@gmail.com>
 */
class Resourcescheduler_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
	//remove the roles
	$role = get_role('administrator');
	$role->remove_cap('place_reservation', true);

	global $wpdb;
	$table_name = $wpdb->prefix . 'resourcescheduler_roles';
	$roles = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");

	foreach($roles as $role) {
		remove_role($role->slug);
	}

	// reservations table
	$table_name = $wpdb->prefix . 'resourcescheduler_reservations';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);


	// courts table
	$table_name = $wpdb->prefix . 'resourcescheduler_facilities';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);

	// role table
	$table_name = $wpdb->prefix . 'resourcescheduler_roles';
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
	}
}

