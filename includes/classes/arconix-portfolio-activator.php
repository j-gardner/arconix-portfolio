<?php
/**
 * Activator class for Portfolio Plugin
 * 
 * @package     WordPress
 * @subpackage  Arconix Portfolio
 * @author      John Gardner
 * @link        http://arconixpc.com/plugins/arconix-portfolio
 * @license     GPL-2.0+
 * @since       1.5.0
 */
class Arconix_Portfolio_Activator {

	public static function activate( $wp = '4.6', $php = '5.3' ) {

		global $wp_version;

		if( version_compare( $wp_version, $wp, '<' ) && version_compare( PHP_VERSION, $php, '<' ) ) {
			$string = sprintf( __( 'This plugin requires either WordPress 4.6 or PHP 5.3. You are running versions %s and %s, respectively', 
			'arconix-portfolio' ), $wp_version , PHP_VERSION );

			deactivate_plugins( basename( __FILE__ ) );

			wp_die( $string, __( 'Plugin Activation Error', 'arconix-portfolio' ), array( 'response' => 200, 'back_link' => TRUE ) );
		
		}
	}

}
