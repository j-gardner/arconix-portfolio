<?php

class Arconix_Portfolio {

    /**
     * Construct Method
     */
    function __construct() {
        $this->constants();

        register_activation_hook( __FILE__,             array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__,           array( $this, 'deactivation' ) );

        add_action( 'init',                             array( $this, 'content_types' ) );
        add_action( 'init',                             array( $this, 'init' ), 9999 );
        add_action( 'after_setup_theme',                array( $this, 'post_thumbnail_support' ), 9999 );
        add_action( 'manage_posts_custom_column',       array( $this, 'columns_data' ) );
        add_action( 'wp_enqueue_scripts',               array( $this, 'scripts' ) );
        add_action( 'admin_enqueue_scripts',            array( $this, 'admin_css' ) );
        add_action( 'dashboard_glance_items',           array( $this, 'at_a_glance' ) );
        add_action( 'wp_dashboard_setup',               array( $this, 'register_dashboard_widget' ) );

        add_filter( 'manage_portfolio_posts_columns',   array( $this, 'columns_filter' ) );
        add_filter( 'post_updated_messages',            array( $this, 'updated_messages' ) );
        add_filter( 'cmb_meta_boxes',                   array( $this, 'metaboxes' ) );
        add_filter( 'widget_text',                      'do_shortcode' );

        add_image_size( 'portfolio-thumb',              275, 200 );
        add_image_size( 'portfolio-large',              620, 9999 );

        add_shortcode( 'portfolio',                     array( $this, 'acp_portfolio_shortcode' ) );
    }

    /**
     * Define plugin constants
     *
     * @since  1.2.0
     */
    function constants() {
        define( 'ACP_VERSION',          '1.3.2' );
        define( 'ACP_URL',              trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACP_IMAGES_URL',       trailingslashit( ACP_URL . 'images' ) );
        define( 'ACP_CSS_URL',          trailingslashit( ACP_URL . 'css' ) );
        define( 'ACP_JS_URL',           trailingslashit( ACP_URL . 'js' ) );
        define( 'ACP_DIR',              trailingslashit( plugin_dir_path( __FILE__ ) ) );
    }

    /**
     * Runs on Plugin Activation
     * Registers our Post Type and Taxonomy
     * 
     * @since  1.2.0
     */
    function activation() {
        $this->content_types();
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

        
    }

    /**
     * Load our Meta Box and WP3.8 Dashboard classes
     * 
     * @since  2.0.0
     */
    function init() {
        if ( ! class_exists( 'cmb_Meta_Box' ) )
            require_once( ACP_DIR . '/metabox/init.php' );

        if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) )
            require_once( ACP_DIR . 'class-gamajo-dashboard-glancer.php' );
    }

    /**
     * Define the defaults used in the registration of the post type and taxonomy
     *
     * @since  1.2.0
     * @return array $defaults
     */
    function portfolio_defaults() {
        // Establishes plugin registration defaults for post type and taxonomy
        $defaults = array(
            'post_type' => array(
                'slug' => 'portfolio',
                'args' => array(
                    'labels' => array(
                        'name'                  => __( 'Portfolio',                             'acp' ),
                        'singular_name'         => __( 'Portfolio',                             'acp' ),
                        'add_new'               => __( 'Add New',                               'acp' ),
                        'add_new_item'          => __( 'Add New Portfolio Item',                'acp' ),
                        'edit'                  => __( 'Edit',                                  'acp' ),
                        'edit_item'             => __( 'Edit Portfolio Item',                   'acp' ),
                        'new_item'              => __( 'New Item',                              'acp' ),
                        'view'                  => __( 'View Portfolio',                        'acp' ),
                        'view_item'             => __( 'View Portfolio Item',                   'acp' ),
                        'search_items'          => __( 'Search Portfolio',                      'acp' ),
                        'not_found'             => __( 'No portfolio items found',              'acp' ),
                        'not_found_in_trash'    => __( 'No portfolio items found in Trash',     'acp' )
                    ),
                    'public'            => true,
                    'query_var'         => true,
                    'menu_position'     => 20,
                    'menu_icon'         => 'dashicons-portfolio',
                    'has_archive'       => false,
                    'supports'          => array( 'title', 'editor', 'thumbnail' ),
                    'rewrite'           => array( 'slug' => 'portfolio', 'with_front' => false )
                )
            ),
            'taxonomy' => array(
                'slug' => 'feature',
                'args' => array(
                    'labels' => array(
                        'name'                          => __( 'Features',                              'acp' ),
                        'singular_name'                 => __( 'Feature',                               'acp' ),
                        'search_items'                  => __( 'Search Features',                       'acp' ),
                        'popular_items'                 => __( 'Popular Features',                      'acp' ),
                        'all_items'                     => __( 'All Features',                          'acp' ),
                        'parent_item'                   => null,
                        'parent_item_colon'             => null,
                        'edit_item'                     => __( 'Edit Feature' ,                         'acp' ),
                        'update_item'                   => __( 'Update Feature',                        'acp' ),
                        'add_new_item'                  => __( 'Add New Feature',                       'acp' ),
                        'new_item_name'                 => __( 'New Feature Name',                      'acp' ),
                        'separate_items_with_commas'    => __( 'Separate features with commas',         'acp' ),
                        'add_or_remove_items'           => __( 'Add or remove features',                'acp' ),
                        'choose_from_most_used'         => __( 'Choose from the most used features',    'acp' ),
                        'menu_name'                     => __( 'Features',                              'acp' ),
                    ),
                    'hierarchical'              => false,
                    'show_ui'                   => true,
                    'update_count_callback'     => '_update_post_term_count',
                    'query_var'                 => true,
                    'rewrite'                   => array( 'slug' => 'feature' )
                )
            ),
            'query' => array(
                'link'              => '',
                'thumb'             => 'portfolio-thumb',
                'full'              => 'portfolio-large',
                'title'             => 'above',
                'display'           => '',
                'heading'           => 'Display',
                'orderby'           => 'date',
                'order'             => 'desc',
                'posts_per_page'    => -1,
                'terms_orderby'     => 'name',
                'terms_order'       => 'ASC',
                'terms'             => '',
                'operator'          => 'IN'
            )
        );

        return apply_filters( 'arconix_portfolio_defaults', $defaults );
    }

    /**
     * Create the post type metabox
     *
     * @param array $meta_boxes
     * @return array $meta_boxes
     * @since 1.3.0
     */
    function metaboxes( $meta_boxes ) {
        $metabox = array(
            'id'            => 'portfolio-setting',
            'title'         => __( 'Portfolio Setting', 'acp' ),
            'pages'         => array( 'portfolio' ),
            'context'       => 'side',
            'priority'      => 'default',
            'show_names'    => false,
            'fields'        => array(
                array(
                    'id'        => '_acp_link_type',
                    'name'      => __( 'Select Link Type', 'acp' ),
                    'type'      => 'select',
                    'desc'      => __( 'Set the hyperlink value for the portfolio item', 'acp' ),
                    'options'   => array(
                        array( 'name' => 'Image',           'value' => 'image' ),
                        array( 'name' => 'Page',            'value' => 'page' ),
                        array( 'name' => 'External Link',   'value' => 'external' )
                    )
                ),
                array(
                    'id'        => '_acp_link_value',
                    'name'      => __( 'Optional Link', 'acp' ),
                    'desc'      => __( 'If selected, enter the destination hyperlink', 'acp' ),
                    'type'      => 'text'
                )
            )
        );

        $meta_boxes[] = apply_filters( 'arconix_portfolio_metabox', $metabox );

        return $meta_boxes;
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
            'portfolio_link' => __( 'Link Type', 'acp' ),
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
            case "portfolio_link":
                get_post_meta( $post->ID, '_acp_link_type', true );
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
     * Load the plugin scripts. If the css file is present in the theme directory, it will be loaded instead,
     * allowing for an easy way to override the default template. If you'd like to remove the CSS or JS entirely,
     * such as when building the styles or scripts into a single file, simply reference the filter and return false
     *
     * @example add_filter( 'pre_register_arconix_portfolio_js', '__return_false' );
     *
     * @since 0.9
     * @version 2.0.0
     */
    function scripts() {
        // If WP_DEBUG is true, load the non-minified versions of the files (for development environments)
        WP_DEBUG === true ? $prefix = '.min' : $prefix = '';

        wp_register_script( 'jquery-quicksand', ACP_JS_URL . 'jquery.quicksand' . $prefix . '.js', array( 'jquery' ), '1.4', true );
        wp_register_script( 'jquery-easing', ACP_JS_URL . 'jquery.easing.1.3' . $prefix . '.js', array( 'jquery-quicksand' ), '1.3', true );

        // JS -- Only requires jquery-easing as Easing requires Quicksand, which requires jQuery, so all dependencies load in the correct order
        if( apply_filters( 'pre_register_arconix_portfolio_js', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.js' ) )
                wp_register_script( 'arconix-portfolio-js', get_stylesheet_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), ACP_VERSION, true );
            elseif( file_exists( get_template_directory() . '/arconix-portfolio.js' ) )
                wp_register_script( 'arconix-portfolio-js', get_template_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), ACP_VERSION, true );
            else
                wp_register_script( 'arconix-portfolio-js', ACP_JS_URL . 'arconix-portfolio.js', array( 'jquery-easing' ), ACP_VERSION, true );
        }        

        // CSS
        if( apply_filters( 'pre_register_arconix_portfolio_css', true ) ) {
            if( file_exists( get_stylesheet_directory() . '/arconix-portfolio.css' ) )
                wp_enqueue_style( 'arconix-portfolio', get_stylesheet_directory_uri() . '/arconix-portfolio.css', false, ACP_VERSION );
            elseif( file_exists( get_template_directory() . '/arconix-portfolio.css' ) )
                wp_enqueue_style( 'arconix-portfolio', get_template_directory_uri() . '/arconix-portfolio.css', false, ACP_VERSION );
            else
                wp_enqueue_style( 'arconix-portfolio', ACP_CSS_URL . 'arconix-portfolio.css', false, ACP_VERSION );
        }
        
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
     * Can be removed entirely via a filter, but is visible by default for admins only
     *
     * @since 0.9.1
     * @version 1.3.0
     */
    function register_dashboard_widget() {
        if( apply_filters( 'pre_register_arconix_portfolio_dashboard_widget', true ) and 
            apply_filters( 'arconix_portfolio_dashboard_widget_security', current_user_can( 'manage_options' ) ) )
                wp_add_dashboard_widget( 'ac-portfolio', 'Arconix Portfolio', array( $this, 'dashboard_widget_output' ) );
    }

    /**
     * Output for the dashboard widget
     *
     * @since 0.9.1
     * @version 1.4.0
     */
    function dashboard_widget_output() {
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
        <li><a href="http://arcnx.co/apwiki"><img src="<?php echo ACP_IMAGES_URL . 'page-16x16.png'?>">Documentation</a></li>
        <li><a href="http://arcnx.co/aphelp"><img src="<?php echo ACP_IMAGES_URL . 'help-16x16.png'?>">Support Forum</a></li>
        <li><a href="http://arcnx.co/aptrello"><img src="<?php echo ACP_IMAGES_URL . 'trello-16x16.png'?>">Dev Board</a></li>
        <li><a href="http://arcnx.co/apsource"><img src="<?php echo ACP_IMAGES_URL . 'github-16x16.png'?>">Source Code</a></li>
        <?php
        echo '</ul></div></div>';
    }

    /**
     * Add the Portfolio post type and Feature taxonomy to the WP 3.8 "At a Glance" dashboard
     *
     * @since  1.4.0
     */
    function at_a_glance() {
        $glancer = new Gamajo_Dashboard_Glancer;
        $glancer->add( 'portfolio' );
    }

    /**
     * Portfolio Shortcode
     *
     * @param array $atts
     * @param string $content
     * @since 0.9
     * @version 1.3.1
     */
    function acp_portfolio_shortcode( $atts, $content = null ) {
        if( wp_script_is( 'arconix-portfolio-js', 'registered' ) ) wp_enqueue_script( 'arconix-portfolio-js' );

        return $this->get_portfolio_data( $atts );
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
    * knowledge due to the fact that there are so many themes and nearly every one is different. 
    * {@see http://arconixpc.com/2012/linking-portfolio-items-to-pages }
    *
    * @param array $args
    * @param bool $echo Determines whether the data is returned or echo'd
    * @since  1.2.0
    * @version 2.0.0
    *
    */
    function get_portfolio_data( $args, $echo = false ) {
        // Get our default arguments
        $default_args = $this->portfolio_defaults();
        $defaults = $default_args['query'];

        // Merge incoming args with the function defaults
        $args = wp_parse_args( $args, $defaults );
        //extract( $args );

        $terms = $args['terms'];

        if( $args['title'] != "below" ) $args['title'] == "above"; // For backwards compatibility with "yes" and built-in data check

        // Default Query arguments
        $qargs = array(
            'post_type' => 'portfolio',
            'meta_key' => '_thumbnail_id', // Pull only items with featured images
            'posts_per_page' => $args['posts_per_page'],
            'orderby' => $args['orderby'],
            'order' => $args['order'],
        );

        // If the user has defined any tax terms, then we create our tax_query and merge to our main query
        if( $terms ) {
            $tax_qargs = array( 
                'tax_query' => array(
                    array(
                        'taxonomy' => 'feature',
                        'field' => 'slug',
                        'terms' => $args['terms'],
                        'operator' => $args['operator']
                    )
                )
            );
            
            // Merge the tax array with the general query
            $qargs = array_merge( $qargs, $tax_qargs );
        }

        $return = ''; // Var that will contain all our portfolio data



        // Create a new query based on our own arguments (available to be filtered)
        $query = new WP_Query( apply_filters( 'arconix_portfolio_query', $qargs ) );

        if( $query->have_posts() ) {


            //
            $this->arconix_portfolio_before_items( $args );
            //
            
            

            while( $query->have_posts() ) : $query->the_post();
                
                $p_id = get_the_ID();

                //
                $this->arconix_portfolio_item( $p_id, $args );
                //
                
                
            endwhile;
        } // End if have post

        //
        //arconix_portfolio_after_items( $args );
        //

        $return .= '</ul>';

        $return = apply_filters( 'arconix_portfolio_return', $return );

    // Either echo or return the results
    if( $echo )
        echo $return;
    else
        return $return;
    }


    function arconix_portfolio_before_items( $args, $echo = false ) {

        $a = array(); // Var to hold our operate arguments

        $terms = $args['terms'];
            
        if( $terms ) {
            // Translate our user-entered slug into an id we can use
            $termid = get_term_by( 'slug', $terms, 'feature' );
            $termid = $termid->term_id;
            
            // Change the get_terms argument based on the shortcode $operator, but default to IN
            switch( $args['operator'] ) {
                case "NOT IN":
                    $a = array( 'exclude' => $termid );
                    break;
                
                case "IN":
                default:
                    $a = array( 'include' => $termid );
                    break;
            }
        }

        // Set our terms list orderby and order
        $a['orderby'] = $args['terms_orderby'];
        $a['order'] = $args['terms_order'];

        // Allow a user to filter the terms list to modify or add their own parameters.
        $a = apply_filters( 'arconix_portfolio_get_terms', $a );

        // Get the tax terms only from the items in our query
        $terms = get_terms( 'feature', $a );

        $return = '';

        // If there aren't multiple terms in use, return early
        if( count( $terms ) > 1 ) {

            //
            $this->arconix_portfolio_filter_list( $terms, $args );
            //                

        }

        $return .= '<ul class="arconix-portfolio-grid">';

        // Either echo or return the results
        if ( $echo )
            echo $return;
        else
            return $return;
    }


    function arconix_portfolio_filter_list( $terms, $args, $echo = false ) {

        $list = '<ul class="arconix-portfolio-features">';
                
        if( $args['heading'] ) {
            $heading = $args['heading'];

            $list .= "<li class='arconix-portfolio-category-title'>{$heading}</li>";
        }            

        $list .= '<li class="arconix-portfolio-feature active"><a href="javascript:void(0)" class="all">' . __( 'All', 'acp' ) . '</a></li>';

        // Break each of the items into individual elements and modify the output
        foreach( $terms as $term ) {
            $list .= '<li class="arconix-portfolio-feature"><a href="javascript:void(0)" class="' . $term->slug . '">' . $term->name . '</a></li>';
        }

        $list .= '</ul>';


        // Either echo or return the results
        if ( $echo )
            echo $list;
        else
            return $list;
    
    }



    function arconix_portfolio_item( $id, $args, $echo = false ) {

        // Get the terms list
        $get_the_terms = get_the_terms( $id, 'feature' );                

        // Add each term for a given portfolio item as a data type so it can be filtered by Quicksand
        $return = '<li data-id="id-' . $id . '" data-type="';
        
        if( $get_the_terms ) {
            foreach ( $get_the_terms as $term ) {
                $return .= $term->slug . ' ';
            }
        }
        
        $return .= '">';

        // Above image Title output
        if( $args['title'] == "above" ) 
            $return .= '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';


        //
        $this->arconix_portfolio_item_image( $id, $args );
        //
        

        // Below image Title output
        if( $args['title'] == "below" ) 
            $return .= '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';

        // Display the content
        switch( $args['display'] ) {
            case "content" :
                $return .= '<div class="arconix-portfolio-text">' . get_the_content() . '</div>';
                break;

            case "excerpt" :
                $return .= '<div class="arconix-portfolio-text">' . get_the_excerpt() . '</div>';
                break;

            default : // If it's anything else, return nothing.
                break;
        }

        $return .= '</li>';

        // Either echo or return the results
        if ( $echo )
            echo $return;
        else
            return $return;
    }



    function arconix_portfolio_item_image( $id, $args, $echo = false ) {
        /**
         * As of v1.3.0, the destination of the image link can be defined at the item level. In order to remain 
         * backwards compatible we have to check if a shortcode parameter was set. If a shortcode param was set,
         * that takes precedence
         */
        $link = $args['link'];

        $return = '';
        
        if( $link ) {
            switch( $link ) {
                case "page" :
                    $return .= '<a class="page" href="' . get_permalink() . '" rel="bookmark">';                        
                    $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                    $return .= '</a>';
                    break;

                case "image" :
                default :
                    $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $args['full'] );
                    $return .= '<a class="image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                    $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                    $return .= '</a>';
                    break;
            }
        }
        else {
            // Grab the post meta
            $link_type = get_post_meta( $id, '_acp_link_type', true );
            $link_value = get_post_meta( $id, '_acp_link_value', true );

            switch ( $link_type ) {
                case 'image' :
                default :
                    $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $args['full'] );
                    $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                    $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                    $return .= '</a>';
                    $return .= '<!-- link type = ' . $link_type . ' -->';
                    break;

                case 'page' :
                    $return .= '<a class="portfolio-page" href="' . get_permalink() . '" rel="bookmark">';                        
                    $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                    $return .= '</a>';
                    $return .= '<!-- link type = ' . $link_type . ' -->';
                    break;

                case 'external' :
                    if( empty( $link_value ) ) { // If the user forgot to enter a link value in the text box, just show the image
                        $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $args['full'] );
                        $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                        $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                        $return .= '</a>';
                        $return .= '<!-- link missing -->';
                    }
                    else {
                        $extra_class = '';
                        $extra_class = apply_filters( 'arconix_portfolio_external_link_class', $extra_class );
                        $return .= '<a class="portfolio-external '. $extra_class . '" href="' . esc_url( $link_value ) . '">';
                        $return .= get_the_post_thumbnail( $id, $args['thumb'] );
                        $return .= '</a>';
                        $return .= '<!-- link type = ' . $link_type . ' -->';
                    }
                    break;
            }
        }

        // Either echo or return the results
        if ( $echo )
            echo $return;
        else
            return $return;
    }

}