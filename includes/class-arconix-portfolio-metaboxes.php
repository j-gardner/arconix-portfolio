<?php
/**
 * Arconix Portfolio Metabox Class
 *
 * Loads the external library and registers the necessary metabox
 *
 * @since   1.4.0
 */
class Arconix_Portfolio_Metaboxes {

    /**
     * Initialize the class
     *
     * @since   1.2.0
     * @version 1.4.0
     * @access  public
     * @param   string      $version    The version of this plugin.
     */
    public function __construct() {
        add_action( 'init',             array( $this, 'metabox_init' ), 9999 );

        add_filter( 'cmb_meta_boxes',   array( $this, 'metaboxes' ) );
    }

    /**
     * Conditionally load the metabox class
     *
     * @since   1.4.0
     */
    public function metabox_init() {
        if ( ! class_exists( 'cmb_Meta_Box' ) )
            require_once( $this->inc . 'metabox/init.php');
    }

    /**
     * Create the post type metabox
     *
     * @since   1.3.0
     * @version 1.4.0
     * @param   array   $meta_boxes     Existing metaboxes
     * @return  array   $meta_boxes     Array with new metabox added
     */
    public function metaboxes( $meta_boxes ) {
        $meta_boxes['portfolio_settings'] =
            apply_filters( 'arconix_portfolio_metabox', array(
                'id'            => 'portfolio_settings',
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
                            array( 'name' => 'None',   'value' => 'none' ),
                            array( 'name' => 'Image',           'value' => 'image' ),
                            array( 'name' => 'Page',            'value' => 'page' ),
                            array( 'name' => 'External Link',   'value' => 'external' )
                        )
                    ),
                    array(
                        'id'        => '_acp_link_value',
                        'name'      => __( 'External Link', 'acp' ),
                        'desc'      => __( 'Enter the destination hyperlink', 'acp' ),
                        'type'      => 'text'
                    )
                )
            )
        );

        return $meta_boxes;
    }

}