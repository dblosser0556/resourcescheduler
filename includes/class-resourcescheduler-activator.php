<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/dblosser0556
 * @since      1.0.0
 *
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Resourcescheduler
 * @subpackage Resourcescheduler/includes
 * @author     Dave Blosser <blosserdl@gmail.com>
 */
class Resourcescheduler_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
// create tables
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		if (!function_exists('dbDelta')) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		}

	// assets table
		$table_resources = $wpdb->prefix . 'resourcescheduler_resources';
		$sql = "CREATE TABLE $table_resources (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			open varchar(10) NOT NULL,
			close varchar(10) NOT NULL,
			time_split smallint(2) NOT NULL,
			max_reservation_minutes smallint(2) NOT NULL,
			days smallint(2) NOT NULL CHECK (days<365),
			history smallint(2) NOT NULL CHECK (days<365),
			allowedtypes varchar(2048) NULL,
			format12 tinyint(1) DEFAULT 0 NOT NULL ,
			CHECK (open<close),
			UNIQUE KEY id (id)
		) $charset_collate;";
		dbDelta($sql);

		// reservations table
		$table_name = $wpdb->prefix . 'resourcescheduler_reservations';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			resourceid mediumint(9) NOT NULL,
			title varchar(255) NOT NULL,
			titleshort varchar(255) NULL,
			type varchar(63) NOT NULL,
			class varchar(255) NULL,
			userid mediumint(9) NOT NULL,
			start datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			end datetime  DEFAULT '0000-00-00 00:00:00' NOT NULL,
			FOREIGN KEY (resourceid) REFERENCES {$table_resources}(id) ON DELETE CASCADE,
			UNIQUE KEY id (id)
		) $charset_collate;";
		dbDelta($sql);

		// role table
		$table_name = $wpdb->prefix . 'resourcescheduler_roles';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(20) NOT NULL,
			slug varchar(20) NOT NULL,
			maxdays smallint(2) NOT NULL CHECK (maxdays<365),
			maxres mediumint(9) NOT NULL,
			standardrole varchar(40),
			UNIQUE KEY id (id)
		) $charset_collate;";
		dbDelta($sql);

//add default roles
		$table_name = $wpdb->prefix . 'resourcescheduler_roles';
		$wpdb->insert(
			$table_name,
			array(
				'name' => 'Member',
				'slug' => 'member',
				'maxdays' => 5,
				'maxres' => 5,
				'standardrole' => 'Subscriber'
			),
			array('%s', '%s', '%d', '%d', '%s')
		);


		$wpdb->insert(
			$table_name,
			array(
				'name' => 'Team Captain',
				'slug' => 'teamcaptain',
				'maxdays' => 5,
				'maxres' => 5,
				'standardrole' => 'Subscriber'
			),
			array('%s', '%s', '%d', '%d', '%s')
		);

	// create role and capabilities
		$cap = 'place_reservation';


		add_role(
			'member',
			__('Member', 'resourcescheduler'),
			array(
				'place_reservation' => true,
				'read' => true
			)
		);

		add_role(
			'teamcaptain',
			__('Team Captain', 'resourcescheduler'),
			array(
				'place_reservation' => true,
				'read' => true
			)
		);

		$role = get_role('administrator');
		$role->add_cap('place_reservation');

	}
}


