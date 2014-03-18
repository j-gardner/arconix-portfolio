<?php
/**
 * Plugin Name: Arconix Portfolio Gallery
 * Plugin URI: http://arconixpc.com/plugins/arconix-portfolio
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.3.2
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


// Include our class
include_once( plugin_dir_path( __FILE__ ) . '/includes/class-portfolio.php' );

/**
 * Init function instantiates the MetaBox and Dashboard At a Glance classes
 * @return void
 *
 * @since  0.9.0
 * @version  1.4.0
 */
function arconix_portfolio_init() {
    if ( ! class_exists( 'cmb_Meta_Box' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/metabox/init.php' );

    if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-gamajo-dashboard-glancer.php');
}

new Arconix_Portfolio;