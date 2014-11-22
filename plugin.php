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

class Arconix_Portfolio_Gallery {

    /**
     * Stores the current version of the plugin.
     *
     * @since   1.0.0
     * @var     string  $version    Current plugin version
     */
    public static $version = '1.3.2';

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @version 2.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->load_admin();
    }

    /**
     * Load the required dependencies for the plugin.
     *
     * - Admin loads the backend functionality
     * - Public provides front-end functionality
     *
     * @since   2.0.0
     */
    private function load_dependencies() {
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-arconix-portfolio-admin.php' );
        require_once( plugin_dir_path( __FILE__ ) . '/includes/class-arconix-portfolio-public.php' );
    }

    /**
     * Load the Administration portion
     *
     * @since   2.0.0
     */
    private function load_admin() {
        if ( is_admin() )
            new Arconix_Plugins_Admin( $this->get_version() );
    }

    /**
     * Get the current version of the plugin
     *
     * @return  string  Plugin current version
     */
    public function get_version() {
        return self::version;
    }
}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_portfolio_gallery_run' );
function arconix_portfolio_gallery_run() {
    new Arconix_Portfolio_Gallery();
}