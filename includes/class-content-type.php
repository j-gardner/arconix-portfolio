<?php
/**
 * Defines and handles the Portfolio Post Type and Feature Taxonomy registration
 *
 * @since   1.4.0
 */
class Arconix_Portfolio_Content_Type {

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.2.0
     * @version 1.4.0
     * @access  public
     */
    public function __construct() {
        register_activation_hook( __FILE__,         array( $this, 'activation' ) );
        register_deactivation_hook( __FILE__,       array( $this, 'deactivation' ) );

        add_action( 'init',                         array( $this, 'content_types' ) );
        add_action( 'dashboard_glance_items',       array( $this, 'at_a_glance' ) );

        add_filter( 'post_updated_messages',        array( $this, 'updated_messages' ) );
    }

    /**
     * Runs on Plugin Activation
     * Registers our Post Type and Taxonomy
     *
     * @since  1.2.0
     */
    public function activation() {
        $this->content_types();
        flush_rewrite_rules();
    }

    /**
     * Runs on Plugin Deactivation
     *
     * @since  1.2.0
     */
    public function deactivation() {
        flush_rewrite_rules();
    }


    /**
     * Register the post_type and taxonomy
     *
     * @since 1.2.0
     */
    public function content_types() {
        $defaults = $this->defaults();
        register_post_type( $defaults['post_type']['slug'], $defaults['post_type']['args'] );
        register_taxonomy( $defaults['taxonomy']['slug'], $defaults['post_type']['slug'],  $defaults['taxonomy']['args'] );
    }

    /**
     * Define the defaults used in the registration of the post type and taxonomy
     *
     * @return  array $defaults
     *
     * @since   1.2.0
     * @version 1.4.0
     */
    public function defaults() {
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
                    'show_admin_column'         => true,
                    'update_count_callback'     => '_update_post_term_count',
                    'query_var'                 => true,
                    'rewrite'                   => array( 'slug' => 'feature' )
                )
            )
        );

        return apply_filters( 'arconix_portfolio_defaults', $defaults );
    }

    /**
     * Correct messages when Portfolio post type is saved
     *
     * @since   0.9.0
     * @version 1.4.0
     * @global  stdObject    $post              WP Post object
     * @global  int          $post_ID           Post ID
     * @param   array        $messages          Existing array of messages
     * @return  array                           updated messages
     */
    public function updated_messages( $messages ) {
        global $post, $post_ID;
        $post_type = get_post_type( $post_ID );

        $obj = get_post_type_object( $post_type );
        $singular = $obj->labels->singular_name;

        $messages[$post_type] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => sprintf( __( $singular . ' updated. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            2  => __( 'Custom field updated.' ),
            3  => __( 'Custom field deleted.' ),
            4  => __( $singular . ' updated.' ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( $singular . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( $singular . ' published. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ) ),
            7  => __( 'Page saved.' ),
            8  => sprintf( __( $singular . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower( $singular ) . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9  => sprintf( __( $singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower( $singular ) . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
            10 => sprintf( __( $singular . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower( $singular ) . '</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        );

        return $messages;
    }

    /**
     * Add the Portfolio post type and Feature taxonomy to the WP 3.8 "At a Glance" dashboard
     *
     * @since 1.4.0
     */
    public function at_a_glance() {
        $glancer = new Gamajo_Dashboard_Glancer;
        $glancer->add( 'portfolio' );
    }

}