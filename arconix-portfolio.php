<?php

/**
 * Plugin Name: Arconix Portfolio Gallery
 * Plugin URI: http://arconixpc.com/plugins/arconix-portfolio
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.5.0
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * Text Domain: arconix-portfolio
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

// Load the metabox class
require_once dirname( __FILE__ ) . '/includes/cmb2/init.php';

// Load the Glancer class
if ( !class_exists( 'Gamajo_Dashboard_Glancer' ) )
    require_once dirname( __FILE__ ) . '/includes/classes/gamajo-dashboard-glancer.php';

// Set our plugin activation hook
register_activation_hook( __FILE__, 'activate_arconix_portfolio' );

function activate_arconix_faq() {
    require_once plugin_dir_path( __FILE__ ) . '/includes/classes/arconix-portfolio-activator.php';
    Arconix_Portfolio_Activator::activate();
}

// Register the autoloader
spl_autoload_register( 'arconix_portfolio_autoloader' );

/**
 * Class Autoloader
 * 
 * @param	string	$class_name		Class to check to autoload
 * @return	null                    Return if it's not a valid class
 */
function arconix_portfolio_autoloader( $class_name ) {
    /**
     * If the class being requested does not start with our prefix,
     * we know it's not one in our project
     */
    if ( 0 !== strpos( $class_name, 'Arconix_' ) ) {
        return;
    }

    $file_name = str_replace(
    array( 'Arconix_', '_' ), // Prefix | Underscores 
    array( '', '-' ), // Remove | Replace with hyphens
    strtolower( $class_name ) // lowercase
    );

    // Compile our path from the current location
    $file = dirname( __FILE__ ) . '/includes/classes/' . $file_name . '.php';

    // If a file is found, load it
    if ( file_exists( $file ) ) {
        require_once( $file );
    }
}

/**
 * Arconix Portfolio Plugin
 *
 * This is the base class which sets the version, loads dependencies and gets the plugin running
 *
 * @since   1.5.0
 */
final class Arconix_Portfolio_Plugin {

    /**
     * Plugin version.
     *
     * @since   1.5.0
     * @var     string	$version        Plugin version
     */
    const version = '1.5.0';

    /**
     * Post Type Settings
     *
     * @since   1.5.0
     * @var     array   $settings       Post Type default settings 
     */
    protected $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.5.0
     */
    public function __construct() {
        $this->settings = $this->set_settings();
    }

    /**
     * Load the plugin instructions
     * 
     * @since   1.5.0
     */
    public function init() {
        $this->register_post_type();
        $this->register_taxonomy();
        $this->load_public();

        if ( is_admin() ) {
            $this->load_admin();
            $this->load_metaboxes();
        }
    }

    /**
     * Set up our Custom Post Type
     * 
     * @since   1.5.0
     */
    private function register_post_type() {
        $settings = $this->settings;

        $names = array(
            'post_type_name' => 'portfolio',
            'singular'       => 'Portfolio',
            'plural'         => 'Portfolios'
        );

        $pt = new Arconix_CPT_Register();
        $pt->add( $names, $settings['post_type']['args'] );
    }

    /**
     * Register the Post Type Taxonomy
     * 
     * @since   1.5.0
     */
    private function register_taxonomy() {
        $settings = $this->settings;

        $tax = new Arconix_Taxonomy_Register();
        $tax->add( 'feature', 'portfolio', $settings['taxonomy']['args'] );
    }

    /**
     * Load the Public-facing components of the plugin
     * 
     * @since   1.5.0
     */
    private function load_public() {
        $p = new Arconix_Portfolio_Public();

        $p->init();
    }

    /**
     * Loads the admin functionality
     *
     * @since   1.5.0
     */
    private function load_admin() {
        new Arconix_Portfolio_Admin();
    }

    /**
     * Set up the Post Type Metabox
     * 
     * @since   1.5.0
     */
    private function load_metaboxes() {
        $m = new Arconix_Portfolio_Metaboxes();

        $m->init();
    }

    /**
     * Get the default Post Type and Taxonomy registration settings
     * 
     * Settings are stored in a filterable array for customization purposes
     * 
     * @since   1.5.0
     * @return  array           Default registration settings
     */
    public function set_settings() {
        $settings = array(
            'post_type' => array(
                'args' => array(
                    'menu_position' => 20,
                    'menu_icon'     => 'dashicons-portfolio',
                    'has_archive'   => false,
                    'supports'      => array( 'title', 'editor', 'revisions', 'page-attributes' ),
                    'rewrite'       => array( 'with_front' => false )
                )
            ),
            'taxonomy'  => array(
                'args' => array(
                    'hierarchical' => false,
                    'show_ui'      => true,
                    'query_var'    => true,
                    'rewrite'      => array( 'with_front' => false )
                )
            )
        );

        return apply_filters( 'arconix_portfolio_defaults', $settings );
    }

}

/** Vroom vroom */
add_action( 'plugins_loaded', 'arconix_portfolio_run' );

function arconix_portfolio_run() {
    load_plugin_textdomain( 'arconix-portfolio' );

    $arconix_portfolio = new Arconix_Portfolio_Plugin();
    $arconix_portfolio->init();
}