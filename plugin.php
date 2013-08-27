<?php
/**
 * Plugin Name: Arconix Portfolio Gallery
 * Plugin URI: http://arconixpc.com/plugins/arconix-portfolio
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.3.0
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

        register_activation_hook( __FILE__,             array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__,           array( $this, 'deactivation' ) );

        add_action( 'init',                             array( $this, 'content_types' ) );
        add_action( 'after_setup_theme',                array( $this, 'post_thumbnail_support' ), 9999 );
        add_action( 'manage_posts_custom_column',       array( $this, 'columns_data' ) );
        add_action( 'wp_enqueue_scripts',               array( $this, 'scripts' ) );
        add_action( 'admin_enqueue_scripts',            array( $this, 'admin_css' ) );
        add_action( 'right_now_content_table_end',      array( $this, 'right_now' ) );
        add_action( 'wp_dashboard_setup',               array( $this, 'register_dashboard_widget' ) );
        add_action( 'init',                             'arconix_portfolio_init_meta_boxes', 99 );

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
        define( 'ACP_VERSION',          '1.3.0' );
        define( 'ACP_URL',              trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'ACP_IMAGES_URL',       trailingslashit( ACP_URL . 'images' ) );
        define( 'ACP_INCLUDES_URL',     trailingslashit( ACP_URL . 'includes' ) );
        define( 'ACP_CSS_URL',          trailingslashit( ACP_INCLUDES_URL . 'css' ) );
        define( 'ACP_JS_URL',           trailingslashit( ACP_INCLUDES_URL . 'js' ) );
        define( 'ACP_DIR',              trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'ACP_INCLUDES_DIR',     trailingslashit( ACP_DIR . 'includes' ) );
        define( 'ACP_VIEWS_DIR',        trailingslashit( ACP_INCLUDES_DIR . 'views' ) );
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
                    'menu_icon'         => ACP_IMAGES_URL . 'portfolio-icon-16x16.png',
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
                'terms'             => '',
                'operator'          => 'IN',
                'terms_orderby'     => 'name',
                'terms_order'       => 'ASC'
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
                        array( 'name' => 'Image',           'value' => 'image'),
                        array( 'name' => 'Page',            'value' => 'page'),
                        array( 'name' => 'YouTube Video',   'value' => 'youtube' ),
                        array( 'name' => 'Vimeo Video',     'value' => 'vimeo' ),
                        array( 'name' => 'External Link',   'value' => 'external' )
                    )
                ),
                array(
                    'id'        => '_acp_link_value',
                    'name'      => __( 'Optional Link', 'acp' ),
                    'desc'      => __( 'If selected, enter the video ID of the YouTube/Vimeo video or the URL of the hyperlink', 'acp' ),
                    'type'      => 'text_medium'
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
     * Portfolio Shortcode
     *
     * @param array $atts
     * @param string $content
     * @since 0.9
     * @version 1.2.0
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
    * knowledge due to the fact that there are so many themes and nearly every one is different. {@see http://arconixpc.com/2012/linking-portfolio-items-to-pages }
    *
    * @param array $args
    * @param bool $echo Determines whether the data is returned or echo'd
    * @since  1.2.0
    * @version 1.3.0
    *
    */
    function get_portfolio_data( $args, $echo = false ) {
        $default_args = $this->portfolio_defaults();
        $defaults = $default_args['query'];

        // Merge incoming args with the function defaults and then extract them into variables
        $args = wp_parse_args( $args, $defaults );
        extract( $args );

        if( $title != "below" ) $title == "above"; // For backwards compatibility with "yes" and built-in data check

        // Default Query arguments
        $args = array(
            'post_type' => 'portfolio',
            'meta_key' => '_thumbnail_id', // Should pull only items with featured images
            'posts_per_page' => $posts_per_page,
            'orderby' => $orderby,
            'order' => $order,
        );

        // If the user has defined any tax terms, then we create our tax_query and merge to our main query
        if( $terms ) {
            $tax_query_args = apply_filters( 'arconix_portfolio_tax_query_args', 
                array(
                    'tax_query' => array(
                        array(
                            'taxonomy' => $default_args['taxonomy']['slug'],
                            'field' => 'slug',
                            'terms' => $terms,
                            'operator' => $operator
                        )
                    )
                )
            );
            
            // Join the tax array to the general query
            $args = array_merge( $args, $tax_query_args );
        }

        $return = ''; // Var that will be concatenated with our portfolio data

        // Create a new query based on our own arguments
        $portfolio_query = new WP_Query( $args );

        if( $portfolio_query->have_posts() ) {
            
            $a = array(); // Var to hold our operate arguments
            
            if( $terms ) {            
                // Translate our user-entered slug into an id we can use
                $termid = get_term_by( 'slug', $terms, $default_args['taxonomy']['slug'] );
                $termid = $termid->term_id;
                
                // Change the get_terms argument based on the shortcode $operator, but default to IN
                switch( $operator) {
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
            $a['orderby'] = $terms_orderby;
            $a['order'] = $terms_order;

            // Allow a user to filter the terms list to modify or add their own parameters.
            $a = apply_filters( 'arconix_portfolio_get_terms', $a );

            // Get the tax terms only from the items in our query
            $get_terms = get_terms( 'feature', $a );        
            
            // If there are multiple terms in use, then create our filter list
            if( count( $get_terms ) > 1 )  {
                $display_list = '<ul class="arconix-portfolio-features">';
                
                if( $heading)
                    $display_list .= "<li class='arconix-portfolio-category-title'>{$heading}</li>";

                $display_list .= '<li class="arconix-portfolio-feature active"><a href="javascript:void(0)" class="all">' . __( 'All', 'acp' ) . '</a></li>';

                // Break each of the items into individual elements and modify the output
                $term_list = '';        
                foreach( $get_terms as $term ) {
                    $term_list .= '<li class"arconix-portfolio-feature"><a href="javascript:void(0)" class="' . $term->slug . '">' . $term->name . '</a></li>';
                }

                // Return our modified list
                $display_list .= $term_list . '</ul>';

                // Allow users to filter how the 'features' are displayed
                $return .= apply_filters( 'arconix_portfolio_display_list', $display_list );
            }

            $return .= '<ul class="arconix-portfolio-grid">';

            while( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
                $p_id = get_the_ID();

                // Get the terms list
                $get_the_terms = get_the_terms( $p_id, 'feature' );                

                // Add each term for a given portfolio item as a data type so it can be filtered by Quicksand
                $return .= '<li data-id="id-' . $p_id . '" data-type="';
                
                if( $get_the_terms ) {
                    foreach ( $get_the_terms as $term ) {
                        $return .= $term->slug . ' ';
                    }
                }
                
                $return .= '">';

                // Above image Title output
                if( $title == "above" ) $return .= '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';

                /**
                 * As of v1.3.0, the destination of the link can be defined at the item level. In order to remain backwards compatible
                 * we have to check if a shortcode parameter was set. If set, run through existing code, otherwise run through the new code
                 */
                
                if( $link ) {
                    switch( $link ) {
                        case "page" :
                            $return .= '<a class="page" href="' . get_permalink() . '" rel="bookmark">';                        
                            $return .= get_the_post_thumbnail( $p_id, $thumb );
                            $return .= '</a>';
                            break;

                        case "image" :
                        default :
                            $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                            $return .= '<a class="image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                            $return .= get_the_post_thumbnail( $p_id, $thumb );
                            $return .= '</a>';
                            break;
                    }
                }
                else {
                    // Grab the post meta
                    $link_type = get_post_meta( $p_id, '_acp_link_type', true );
                    $link_value = get_post_meta( $p_id, '_acp_link_value', true );

                    switch ( $link_type ) {
                        case 'image' :
                        default :
                            $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                            $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                            $return .= get_the_post_thumbnail( $p_id, $thumb );
                            $return .= '</a>';
                            $return .= '<!-- link type = ' . $link_type . ' -->';
                            break;

                        case 'page' :
                            $return .= '<a class="portfolio-page" href="' . get_permalink() . '" rel="bookmark">';                        
                            $return .= get_the_post_thumbnail( $p_id, $thumb );
                            $return .= '</a>';
                            $return .= '<!-- link type = ' . $link_type . ' -->';
                            break;

                        case 'youtube' :
                            if( empty( $link_value ) ) { // If the user forgot to enter a link value in the text box, just show the image
                                $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                                $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- video id missing -->';
                            }
                            else {
                                $return .= '<a class="portfolio-video" href="http://youtube.com/watch?v=' . $link_value . '">';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- link type = ' . $link_type . ' -->';
                            }
                            break;

                        case 'vimeo' :
                            if( empty( $link_value ) ) { // If the user forgot to enter a link value in the text box, just show the image
                                $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                                $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- video id missing -->';
                            }
                            else {
                                $return .= '<a class="portfolio-video" href="http://vimeo.com/' . $link_value . '">';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- link type = ' . $link_type . ' -->';
                            }
                            break;

                        case 'external' :
                            if( empty( $link_value ) ) { // If the user forgot to enter a link value in the text box, just show the image
                                $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                                $return .= '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- link missing -->';
                            }
                            else {
                                $return .= '<a class="portfolio-external" href="' . esc_url( $link_value ) . '">';
                                $return .= get_the_post_thumbnail( $p_id, $thumb );
                                $return .= '</a>';
                                $return .= '<!-- link type = ' . $link_type . ' -->';
                            }
                    }
                }

                // Below image Title output
                if( $title == "below" ) $return .= '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';

                // Display the content
                switch( $display ) {
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

            endwhile;
        }
        $return .= '</ul>';

        $return = apply_filters( 'arconix_portfolio_return', $return );

    // Either echo or return the results
    if( $echo )
        echo $return;
    else
        return $return;
    }

    /**
     * Load the plugin scripts. If the css file is present in the theme directory, it will be loaded instead,
     * allowing for an easy way to override the default template. If you'd like to remove the CSS or JS entirely,
     * such as when building the styles or scripts into a single file, simply reference the filter and return false
     *
     * @example add_filter( 'pre_register_arconix_portfolio_js', '__return_false' );
     *
     * @since 0.9
     * @version 1.2.2
     */
    function scripts() {
        // If WP_DEBUG is true, load the non-minified versions of the files (for development environments)
        WP_DEBUG === true ? $prefix = '.min' : $prefix = '';

        wp_register_script( 'jquery-quicksand', ACP_JS_URL . 'jquery.quicksand' . $prefix . '.js', array( 'jquery' ), '1.3', true );
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

function arconix_portfolio_init_meta_boxes() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once( plugin_dir_path( __FILE__ ) . '/includes/metabox/init.php' );
}

new Arconix_Portfolio;