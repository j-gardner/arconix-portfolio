<?php
/**
 * Public-facing functionality of the plugin.
 * 
 * Handles the registration of scripts and styles as well as the shortcode registration and related output.
 * 
 * @author      John Gardner
 * @link        http://arconixpc.com/plugins/arconix-portfolio
 * @license     GPLv2 or later
 * @since       1.5.0
 */
class Arconix_Portfolio_Public {

    /**
     * The url path to this plugin.
     *
     * @since   1.5.0
     * @access  private
     * @var     string      $url        The url path to this plugin
     */
    private $url;

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );
    }    
    
    /**
     * Get our hooks into WordPress
     *
     * @since   1.5.0
     */
    public function init() {
        add_action( 'wp_enqueue_scripts',                       array( $this, 'scripts' ) );
        
        add_filter( 'widget_text',                              'do_shortcode' );

        add_image_size( 'portfolio-thumb',                      275, 200 );
        add_image_size( 'portfolio-large',                      620, 9999 );

        add_shortcode( 'portfolio',                             array( $this, 'acp_portfolio_shortcode' ) );
        
        // For use if Arconix Flexslider is active
        add_filter( 'arconix_flexslider_slide_image_return',    array( $this, 'flexslider_image_return' ), 10, 4 );
    }
    
    /**
     * Load the plugin scripts. If the css file is present in the theme directory, it will be loaded instead,
     * allowing for an easy way to override the default template. If you'd like to remove the CSS or JS entirely,
     * such as when building the styles or scripts into a single file, simply reference the filter and return false
     *
     * @example add_filter( 'pre_register_arconix_portfolio_js', '__return_false' );
     *
     * @since   0.9
     * @version 1.5.0
     */
    public function scripts() {
        // If SCRIPT_DEBUG is true, load the non-minified versions of the files (for development environments)
        SCRIPT_DEBUG === true ? $prefix = '' : $prefix = '.min';

        wp_register_script( 'jquery-quicksand', $this->url . 'js/jquery.quicksand' . $prefix . '.js', array( 'jquery' ), '1.4', true );
        wp_register_script( 'jquery-easing', $this->url . 'js/jquery.easing.1.3' . $prefix . '.js', array( 'jquery-quicksand' ), '1.3', true );

        // JS -- Only requires jquery-easing as Easing requires Quicksand, which requires jQuery, so all dependencies load in the correct order
        if( apply_filters( 'pre_register_arconix_portfolio_js', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.js' ) )
                wp_register_script( 'arconix-portfolio-js', get_stylesheet_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), Arconix_Portfolio_Plugin::version, true );
            elseif( file_exists( get_template_directory() . '/arconix-portfolio.js' ) )
                wp_register_script( 'arconix-portfolio-js', get_template_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), Arconix_Portfolio_Plugin::version, true );
            else
                wp_register_script( 'arconix-portfolio-js', $this->url . 'js/arconix-portfolio.js', array( 'jquery-easing' ), Arconix_Portfolio_Plugin::version, true );
        }

        // CSS
        if( apply_filters( 'pre_register_arconix_portfolio_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.css' ) )
                wp_enqueue_style( 'arconix-portfolio', get_stylesheet_directory_uri() . '/arconix-portfolio.css', false, Arconix_Portfolio_Plugin::version );
            elseif( file_exists( get_template_directory() . '/arconix-portfolio.css' ) )
                wp_enqueue_style( 'arconix-portfolio', get_template_directory_uri() . '/arconix-portfolio.css', false, Arconix_Portfolio_Plugin::version );
            else
                wp_enqueue_style( 'arconix-portfolio', $this->url . 'css/arconix-portfolio.css', false, Arconix_Portfolio_Plugin::version );
        }

    }
    
    /**
     * Portfolio Shortcode
     *
     * @param   array   $atts
     * @param   string  $content
     * @since   0.9
     * @version 1.3.1
     */
    public function acp_portfolio_shortcode( $atts, $content = null ) {
        if( wp_script_is( 'arconix-portfolio-js', 'registered' ) ) 
            wp_enqueue_script( 'arconix-portfolio-js' );

        $p = new Arconix_Portfolio;

        return $p->loop( $atts );
    }

    /**
     * Modify the Arconix Flexslider image information
     *
     * References the custom URL config set by the portfolio items
     *
     * @since   1.4.0
     * @global  stdObj      $post           Standard WP Post object
     * @param   string      $content        Existing image data
     * @param   bool        $link_image     Wrap the image in a hyperlink to the permalink (false for basic image slider)
     * @param   string      $image_size     The size of the image to display. Accepts any valid built-in or added WordPress image size
     * @param   string      $caption        Caption to be displayed
     * @return  string      $s              string | null Modified flexslider image or nothing if we're not on a portfolio CPT
     */
    public function flexslider_image_return( $content, $link_image, $image_size, $caption ) {
        global $post;

        // return early if we're not working with a portfolio item or we don't have a featured image
        if ( $post->post_type != 'portfolio' || ! has_post_thumbnail() ) return;

        $s = '<div class="arconix-slide-image-wrap">';

        if ( $link_image === 'true' || $link_image === 1 ) {
            /*
             * Return the hyperlinked portfolio image. Setting the 1st param to false
             * forces the plugin to use the portfolio item's link type. Since we're not
             * running as a result of the shortcode, there's no way to know if that has
             * been configured
             */
            $p = new Arconix_Portfolio();
            $s .= $p->get_portfolio_image ( false, $image_size, 'full' );
        } else {
            $id = get_the_ID();
            $s .= get_the_post_thumbnail( $id, $image_size );
        }
        $f = new Arconix_FlexSlider();
        $s .= $f->slide_caption( $caption );

        $s .= '</div>';

        return $s;
    }
    
    
}