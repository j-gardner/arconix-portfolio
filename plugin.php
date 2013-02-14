<?php
/**
 * Plugin Name: Arconix Portfolio Gallery
 * Plugin URI: http://arconixpc.com/plugins/arconix-portfolio
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.2.0
 *
 * Author: John Gardner
 * Author URI: http://arconixpc.com
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


class Arconix_Portfolio {

    /**
     * Construct Method
     */
    function __construct() {
        $this->constants();

        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

        add_action( 'init', array( $this, 'content_types' ) );
        add_action( 'after_setup_theme', array( $this, 'post_thumbnail_support' ), 9999 );
        add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
        add_action( 'right_now_content_table_end', array( $this, 'right_now' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

        add_filter( 'manage_portfolio_posts_columns', array( $this, 'columns_filter' ) );
        add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
        add_filter( 'widget_text', 'do_shortcode' );

        add_image_size( 'portfolio-thumb', 275, 200, TRUE );
        add_image_size( 'portfolio-large', 620, 9999 );        

        add_shortcode( 'portfolio', array( $this, 'portfolio_shortcode' ) );        
    }

    /**
     * Define plugin constants
     *
     * @since  1.2.0
     */
    function constants() {
        define( 'ACP_VERSION', '1.2.0' );
        define( 'ACP_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACP_IMAGES_URL', trailingslashit( ACP_URL . 'images' ) );
        define( 'ACP_INCLUDES_URL', trailingslashit( ACP_URL . 'includes' ) );
        define( 'ACP_CSS_URL', trailingslashit( ACP_INCLUDES_URL . 'css' ) );
        define( 'ACP_JS_URL', trailingslashit( ACP_INCLUDES_URL . 'js' ) );
        define( 'ACP_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACP_INCLUDES_DIR', trailingslashit( ACP_DIR . 'includes' ) );
        define( 'ACP_VIEWS_DIR', trailingslashit( ACP_INCLUDES_DIR . 'views' ) );
    }

    /**
     * Runs on Plugin Activation
     * Registers our Post Type and Taxonomy
     * 
     * @since  1.2.0
     */
    function activation() {
        $this->register_content_types();
        flush_rewrite_rules();
    }

    /**
     * Runs on Plugin Deactivation
     *
     * @since  1.2.0
     */
    function deactivation() {
        flush_rewrite_rules();
    }

    /**
     * Register the post_type and taxonomy
     *
     * @since 1.2.0
     */
    function content_types() {
        $defaults = $this->portfolio_defaults();
        register_post_type( $defaults['post_type']['slug'], $defaults['post_type']['args'] );
        register_taxonomy( $defaults['taxonomy']['slug'], $defaults['post_type']['slug'],  $defaults['taxonomy']['args'] );
        flush_rewrite_rules( false );
    }

    /**
     * Define the defaults used in the registration of the post type and taxonomy
     *
     * @since  1.2.0
     * @return array $defaults
     */
    function portfolio_defaults() {
        include_once( ACP_VIEWS_DIR . 'defaults.php' );

        return apply_filters( 'arconix_portfolio_defaults', $defaults );
    }

    /**
     * Correct messages when Portfolio post type is saved
     *
     * @global stdObject $post
     * @global int $post_ID
     * @param array $messages
     * @return array $messages
     * @since 0.9
     */
    function updated_messages( $messages ) {
        global $post, $post_ID;

        $messages['portfolio'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __( 'Portfolio Item updated. <a href="%s">View portfolio item</a>' ), esc_url( get_permalink($post_ID) ) ),
            2 => __( 'Custom field updated.' ),
            3 => __( 'Custom field deleted.' ),
            4 => __( 'Portfolio item updated.' ),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio item restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __( 'Portfolio item published. <a href="%s">View portfolio item</a>' ), esc_url( get_permalink($post_ID) ) ),
            7 => __( 'Portfolio item saved.'),
            8 => sprintf( __( 'Portfolio item submitted. <a target="_blank" href="%s">Preview portfolio item</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9 => sprintf( __( 'Portfolio item scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview portfolio item</a>' ),
              // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
            10 => sprintf( __( 'Portfolio item draft updated. <a target="_blank" href="%s">Preview portfolio item</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        );

        return $messages;
    }

    /**
     * Filter the columns on the admin screen and define our own
     *
     * @param array $columns
     * @return array $soumns
     * @since 0.9.0
     * @version  1.2.0
     */
    function columns_filter ( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'portfolio_thumbnail' => __( 'Image', 'acp' ),
            'title' => __( 'Title', 'acp' ),
            'portfolio_description' => __( 'Description', 'acp' ),
            'portfolio_features' => __( 'Features', 'acp' ),
            'date' => __( 'Date', 'acp' )
        );

        return $columns;
    }

    /**
     * Filter the data that shows up in the columns we defined above
     *
     * @global  stdObject $post
     * @param  object $column
     * @since  0.9.0
     * @version  1.2.0
     */
    function columns_data( $column ) {
        global $post;

        switch( $column ) {
            case "portfolio_thumbnail":
                printf( '<p>%s</p>', the_post_thumbnail( 'thumbnail' ) );
                break;
            case "portfolio_description":
                the_excerpt();
                break;
            case "portfolio_features":
                echo get_the_term_list( $post->ID, 'feature', '', ', ', '' );
                break;
        }
    }

    /**
     * Check for post-thumbnails and add portfolio post type to it
     *
     * @global type $_wp_theme_features
     * @since 0.9
     */
    function post_thumbnail_support() {
        global $_wp_theme_features;

        if( ! isset( $_wp_theme_features['post-thumbnails'] ) )
            $_wp_theme_features['post-thumbnails'] = array( array( 'portfolio' ) );
        elseif( is_array( $_wp_theme_features['post-thumbnails'] ) )
            $_wp_theme_features['post-thumbnails'][0][] = 'portfolio';
    }

    /**
     * Portfolio Shortcode
     *
     * @param array $atts
     * @param string $content
     * @since 0.9
     * @version 1.2.0
     */
    function portfolio_shortcode( $atts, $content = null ) {
        // Shortcode defaults
        $defaults = apply_filters( 'arconix_portfolio_shortcode_args',
            array(
                'link' => 'image',
                'thumb' => 'portfolio-thumb',
                'full' => 'portfolio-large',
                'title' => 'above',
                'display' => '',
                'heading' => 'Display',
                'orderby' => 'date',
                'order' => 'desc',
                'terms' => '',
                'operator' => 'IN'
            )
        );

        // Merge our two arrays together, using the values in $atts to override $defaults
        $args = wp_parse_args( $atts, $defaults );

        return get_portfolio_data( $args );
    }

   /**
    * Return Porfolio Content
    *
    * Grab all portfolio items from the database and sets up their display.
    *
    * Supported Arguments
    * - link =>  page, image
    * - thumb => any built-in image size
    * - full => any built-in image size (this setting is ignored of 'link' is set to 'page')
    * - title => above, below or 'blank' ("yes" is converted to "above" for backwards compatibility)
    * - display => content, excerpt (leave blank for nothing)
    * - heading => When displaying the 'feature' items in a row above the portfolio items, define the heading text for that section.
    * - orderby => date or any other orderby param available. http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
    * - order => ASC (ascending), DESC (descending)
    * - terms => a 'feature' tag you want to filter on operator => 'IN', 'NOT IN' filter for the term tag above
    *
    * 'Image' is the only officially supported link option. While linking to a page is possible, it may require additional coding
    * knowledge due to the fact that there are so many themes and nearly every one is different. {@see http://arconixpc.com/2012/linking-portfolio-items-to-pages }
    *
    * @param array $args
    * @param bool $echo Determines whether the data is returned or echo'd
    * @since  1.3
    *
    */
    function get_portfolio_data( $args, $echo = false ) {
        include_once( ACP_VIEWS_DIR . 'get-data.php' );

        // Either echo or return the results
        if( $echo )
            echo $return;
        else
            return $return;
    }

    /**
     * Load the plugin scripts. If the css file is present in the theme directory, it will be loaded instead,
     * allowing for an easy way to override the default template. If you'd like to remove the CSS or JS entirely,
     * such as when building the styles or scripts into a single file, simply add a filter that returns false
     *
     * @since 0.9
     * @version 1.2.0
     */
    function scripts() {
        wp_register_script( 'jquery-quicksand', ACP_JS_URL . 'jquery.quicksand.min.js', array( 'jquery' ), '1.3', true );
        wp_register_script( 'jquery-easing', ACP_JS_URL . 'jquery.easing.1.3.min.js', array( 'jquery-quicksand' ), '1.3', true );

        // JS -- Only requires jquery-easing as Easing requires Quicksand, which requires jQuery, so all dependencies load properly
        if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.js' ) )
            wp_register_script( 'arconix-portfolio-js', get_stylesheet_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), ACP_VERSION, true );
        elseif( file_exists( get_template_directory() . '/arconix-portfolio.js' ) )
            wp_register_script( 'arconix-portfolio-js', get_template_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), ACP_VERSION, true );
        else
            if( apply_filters( 'pre_register_arconix_portfolio_js', true ) )
                wp_register_script( 'arconix-portfolio-js', ACP_JS_URL . 'portfolio.min.js', array( 'jquery-easing' ), ACP_VERSION, true );

        // CSS
        if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.css' ) )
            wp_enqueue_style( 'arconix-portfolio', get_stylesheet_directory_uri() . '/arconix-portfolio.css', false, ACP_VERSION );
        elseif( file_exists( get_template_directory() . '/arconix-portfolio.css' ) )
            wp_enqueue_style( 'arconix-portfolio', get_template_directory_uri() . '/arconix-portfolio.css', false, ACP_VERSION );
        else
            if( apply_filters( 'pre_register_arconix_portfolio_css', true ) )
                wp_enqueue_style( 'arconix-portfolio', ACP_CSS_URL . 'portfolio.css', false, ACP_VERSION );
    }

    /**
     * Includes admin css
     *
     * @since  1.2.0
     */
    function admin_css() {
        wp_enqueue_style( 'arconix-portfolio-admin', ACP_CSS_URL . 'admin.css', false, ACP_VERSION );
    }

    /**
     * Adds a widget to the dashboard.
     *
     * @since 0.9.1
     */
    function register_dashboard_widget() {
        wp_add_dashboard_widget( 'ac-portfolio', 'Arconix Portfolio', array( $this, 'dashboard_widget_output' ) );
    }

    /**
     * Output for the dashboard widget
     *
     * @since 0.9.1
     * @version 1.2.0
     */
    function dashboard_widget_output() {
        include_once( ACP_VIEWS_DIR . 'dash-widget.php' );
    }

    /**
     * Add the Portfolio Post type to the "Right Now" Dashboard Widget
     *
     * @link http://bajada.net/2010/06/08/how-to-add-custom-post-types-and-taxonomies-to-the-wordpress-right-now-dashboard-widget
     * @since  0.9.0
     * @version  1.2.0
     */
    function right_now() {
        include_once( ACP_VIEWS_DIR . 'right-now.php' );
    }
}

new Arconix_Portfolio;