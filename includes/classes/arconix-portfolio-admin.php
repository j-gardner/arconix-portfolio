<?php
/**
 * Defines and handles all backend plugin operation
 *
 * @since   1.0.0
 */
class Arconix_Portfolio_Admin extends Arconix_CPT_Admin {

    /**
     * The url path to this plugin.
     *
     * @since   1.4.0
     * @access  private
     * @var     string      $url    The url path to this plugin
     */
    private $url;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.2.0
     * @version 1.4.0
     * @access  public
     * @param   string      $version    The version of this plugin.
     */
    public function __construct( $version ) {
        $this->url = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) );

        parent::__construct( 'portfolio' );
    }

    /**
     * Init the Admin side
     *
     * Loads all actions and filters to be used.
     *
     * @since   1.4.0
     */
    public function init() {
        
        add_action( 'admin_enqueue_scripts',                    array( $this, 'admin_css' ) );
        add_action( 'admin_print_scripts-post-new.php',         array( $this, 'admin_scripts' ), 11 );
        add_action( 'admin_print_scripts-post.php',             array( $this, 'admin_scripts' ), 11 );
        add_action( 'wp_dashboard_setup',                       array( $this, 'register_dashboard_widget' ) );
        add_action( 'after_setup_theme',                        array( $this, 'post_thumbnail_support' ), 9999 );

        parent::init();
    }

    /**
     * Filter the columns on the admin screen and define our own
     *
     * @since    0.9.0
     * @version  1.4.0
     * @param    array $columns
     * @return   array $soumns
     */
    public function columns_define ( $columns ) {
        $col_img = array( 'portfolio_thumbnail' => __( 'Image', 'acp' ) );
        $col_desc = array( 'portfolio_description' => __( 'Description', 'acp' ) );
        $col_link = array( 'portfolio_link' => __( 'Link Type', 'acp' ) );

        $columns = array_slice( $columns, 0, 1, true ) + $col_img + array_slice( $columns, 1, NULL, true );
        $columns = array_slice( $columns, 0, 3, true ) + $col_desc + array_slice( $columns, 3, NULL, true );
        $columns = array_slice( $columns, 0, 4, true ) + $col_link + array_slice( $columns, 4, NULL, true );

        return apply_filters( 'arconix_portfolio_columns', $columns );
    }

    /**
     * Filter the data that shows up in the columns we defined above
     *
     * @since   0.9.0
     * @version 1.2.0
     * @global  stdObject   $post
     * @param   array       $column
     */
    public function column_value( $column ) {
        global $post;

        switch( $column ) {
            case "portfolio_thumbnail":
                printf( '<p>%s</p>', the_post_thumbnail( 'thumbnail' ) );
                break;
            case "portfolio_description":
                the_excerpt();
                break;
            case "portfolio_link":
                printf( '<div class="portfolio-link-type">%s</div>', get_post_meta( $post->ID, '_acp_link_type', true ) );
                break;
        }
    }

    /**
      * Check for post-thumbnails and add portfolio post type to it
      *
      * @global array $_wp_theme_features
      * @since  1.5.0
      */
    public function post_thumbnail_support() {
        global $_wp_theme_features;

        if ( !isset( $_wp_theme_features['post-thumbnails'] ) )
            $_wp_theme_features['post-thumbnails'] = array( array( 'portfolio' ) );
        elseif( is_array( $_wp_theme_features['post-thumbnails'] ) ) - $_wp_theme_features['post-thumbnails'][0][] = 'portfolio';

    }

    /**
     * Load javascript on the portfolio post admin screen
     *
     * @since   1.4.0
     * @global  string  $post_type  Post Type being manipulated
     */
    public function admin_scripts() {
        global $post_type;
        if( 'portfolio' == $post_type )
            wp_enqueue_script( 'arconix-portfolio-admin-js', $this->url . 'js/admin.js', array( 'jquery' ), Arconix_Portfolio_Plugin::version, true );
    }

    /**
     * Includes admin css
     *
     * @since  1.2.0
     */
    public function admin_css() {
        wp_enqueue_style( 'arconix-portfolio-admin', $this->url . 'css/admin.css', false, Arconix_Portfolio_Plugin::version );
    }

    /**
     * Adds a widget to the dashboard.
     *
     * Can be removed entirely via a filter, but is visible by default for admins only
     *
     * @since   0.9.1
     * @version 1.3.0
     */
    public function register_dashboard_widget() {
        if( apply_filters( 'pre_register_arconix_portfolio_dashboard_widget', true ) and
            apply_filters( 'arconix_portfolio_dashboard_widget_security', current_user_can( 'manage_options' ) ) )
                wp_add_dashboard_widget( 'ac-portfolio', 'Arconix Portfolio', array( $this, 'dashboard_widget_output' ) );
    }

    /**
     * Output for the dashboard widget
     *
     * @since   0.9.1
     * @version 1.4.0
     */
    public function dashboard_widget_output() {
        echo '<div class="rss-widget">';

        wp_widget_rss_output( array(
          'url'       => 'http://arconixpc.com/tag/arconix-portfolio/feed', // feed url
          'title'     => 'Arconix Portfolio Posts', // feed title
          'items'     => 3, //how many posts to show
          'show_summary'  => 1, // display excerpt
          'show_author'   => 0, // display author
          'show_date'   => 1 // display post date
        ) );

        echo '<div class="acp-widget-bottom"><ul>';
        ?>
        <li><a href="http://arcnx.co/apwiki"><img src="<?php echo $this->url . 'images/page-16x16.png'?>"><?php _e( 'Documentation', 'arconix-portfolio' ); ?></a></li>
        <li><a href="http://arcnx.co/aphelp"><img src="<?php echo $this->url . 'images/help-16x16.png'?>"><?php _e( 'Support Forum', 'arconix-portfolio' ); ?></a></li>
        <li><a href="http://arcnx.co/aptrello"><img src="<?php echo $this->url . 'images/trello-16x16.png'?>"><?php _e( 'Dev Board', 'arconix-portfolio' ); ?></a></li>
        <li><a href="http://arcnx.co/apsource"><img src="<?php echo $this->url . 'images/github-16x16.png'?>"><?php _e( 'Source Code', 'arconix-portfolio' ); ?></a></li>
        <?php
        echo '</ul></div></div>';
    }

}