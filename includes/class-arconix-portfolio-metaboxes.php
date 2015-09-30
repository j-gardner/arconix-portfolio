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
     * @version 1.5.0
     * @access  public
     */
    public function __construct() {
        add_action( 'cmb2_init',    array( $this, 'cmb2') );
        //add_filter( 'cmb_meta_boxes',   array( $this, 'metaboxes' ) );
    }

    /**
     * Define the Metabox and its fields
     *
     * @since   1.5.0
     * @access  public
     */
    public function cmb2() {
        // Initiate the metabox
        $cmb = new_cmb2_box( array(
            'id'            => 'portfolio_settings',
            'title'         => __( 'Portfolio Setting', 'acp' ),
            'object_types'  => array( 'portfolio' ),
            'context'       => 'side',
            'priority'      => 'default',
            'show_names'    => false
        ) );

        // Add the Link Type field
        $cmb->add_field( array(
            'id'        => '_acp_link_type',
            'name'      => __( 'Select Link Type', 'acp' ),
            'desc'      => __( 'Set the hyperlink value for the portfolio item', 'acp' ),
            'type'      => 'select',
            'options'   => array(
                'image'     => __( 'Image', 'acp' ),
                'page'      => _x( 'Page', 'one side of a sheet of paper: words on a page', 'acp' ),
                'external'  => __( 'External Link', 'acp' )
            )
        ));

        // Add the External Link url box
        $cmb->add_field( array(
            'id'        => '_acp_link_value',
            'name'      => __( 'External Link', 'acp' ),
            'desc'      => __( 'Enter the destination hyperlink', 'acp' ),
            'type'      => 'text'
        ));
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