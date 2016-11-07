<?php
/**
 * Arconix Portfolio Metabox Class
 *
 * Registers the plugin's settings metabox
 *
 * @since   1.4.0
 * @version 1.5.0
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
            'id'            => 'arconix_portfolio_settings',
            'title'         => __( 'Portfolio Setting', 'arconix-portfolio' ),
            'object_types'  => array( 'portfolio' ),
            'context'       => 'side',
            'priority'      => 'default',
            'show_names'    => false
        ) );

        // Add the Link Type field
        $cmb->add_field( array(
            'id'        => '_acp_link_type',
            'name'      => __( 'Select Link Type', 'arconix-portfolio' ),
            'desc'      => __( 'Set the hyperlink value for the portfolio item', 'arconix-portfolio' ),
            'type'      => 'select',
            'options'   => array(
                'image'     => __( 'Image', 'arconix-portfolio' ),
                'page'      => _x( 'Page', 'one side of a sheet of paper: words on a page', 'arconix-portfolio' ),
                'external'  => __( 'External Link', 'arconix-portfolio' )
            )
        ) );

        // Add the External Link url box
        $cmb->add_field( array(
            'id'        => '_acp_link_value',
            'name'      => __( 'External Link', 'arconix-portfolio' ),
            'desc'      => __( 'Enter the destination hyperlink', 'arconix-portfolio' ),
            'type'      => 'text'
        ) );
    }

}