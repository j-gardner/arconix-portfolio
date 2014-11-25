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
     * @since   2.0.0
     * @access  private
     * @var     string      $version    Current plugin version
     */
    private $version;

    /**
     * The directory path to the plugin file's includes folder.
     *
     * @since   2.0.0
     * @access  private
     * @var     string      $inc    The directory path to the includes folder
     */
    private $inc;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @version 2.0.0
     */
    public function __construct() {
        $this->version = '1.3.2';
        $this->inc = trailingslashit( plugin_dir_path( __FILE__ ) . '/includes' );
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

        if ( ! class_exists( 'cmb_Meta_Box' ) )
            require_once( $this->inc . 'metabox/init.php');

        if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) )
            require_once( $this->inc . 'class-gamajo-dashboard-glancer.php' );
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
     * @since   2.0.0
     * @return  string  Plugin current version
     */
    public function get_version() {
        return $this->version;
    }
}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_portfolio_gallery_run' );
function arconix_portfolio_gallery_run() {
    new Arconix_Portfolio_Gallery;
}