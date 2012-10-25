<?php
/**
 * Plugin Name: Arconix Portfolio Gallery
 * Plugin URI: http://arconixpc.com/plugins/arconix-portfolio
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.1.1
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */



register_activation_hook( __FILE__, 'arconix_portfolio_activation' );
/**
 * This function runs on plugin activation. It checks for the existence of the CPT
 * and creates it otherwise
 *
 * @since 0.9
 */
function arconix_portfolio_activation() {

    if( ! post_type_exists( 'portfolio' ) ) {
	arconix_portfolio_init();
	global $_arconix_portfolio;
	$_arconix_portfolio->create_post_type();
	$_arconix_portfolio->create_taxonomy();
    }
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'arconix_portfolio_deactivation' );
/**
 * This function runs on deactivation and flushes the re-write rules so permalinks work properly
 * 
 * @since 1.0 
 */
function arconix_portfolio_deactivation() {
    
    flush_rewrite_rules();
}


add_action( 'after_setup_theme', 'arconix_portfolio_init' );
/**
 * Initializes the plugin
 * Includes the libraries, defines global variables, instantiates the class
 *
 * @since 0.9
 */
function arconix_portfolio_init() {
    global $_arconix_portfolio;

    define( 'ACP_URL', plugin_dir_url( __FILE__ ) );
    define( 'ACP_VERSION', '1.1.1' );

    /** Includes **/
    require_once( dirname( __FILE__ ) . '/includes/class-portfolio.php' );


    /** Instantiate **/
    $_arconix_portfolio = new Arconix_Portfolio;

}


?>