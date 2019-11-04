<?php
/**
 * Arconix Portfolio Metabox Class
 *
 * Loads the external library and registers the necessary metabox
 *
 * @since   1.4.0
 * @package arconix-portfolio/metabox
 */

if ( file_exists( dirname( __FILE__ ) . '/metabox/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/metabox/init.php';
}

/**
 * Create a metabox on the Portfolio admin page.
 */
class Arconix_Portfolio_Metaboxes {

	/**
	 * Initialize the class
	 *
	 * @since   1.2.0
	 * @version 1.4.0
	 * @access  public
	 */
	public function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'metabox_init' ) );
	}

	/**
	 * Conditionally load the metabox class
	 *
	 * @since   1.4.0
	 */
	public function metabox_init() {
		$acp_box = new_cmb2_box(
			array(
				'id'           => '_acp_metabox',
				'title'        => esc_html__( 'Portfolio Setting', 'acp' ),
				'object_types' => array( 'portfolio' ),
			)
		);

		$acp_box->add_field(
			array(
				'name'             => esc_html__( 'Select Link Type', 'acp' ),
				'desc'             => esc_html__( 'Set the hyperlink value for the portfolio item', 'acp' ),
				'id'               => '_acp_link_type',
				'type'             => 'select',
				'options'          => array(
					'none'	   => esc_html__( 'None', 'acp' ),
					'image'    => esc_html__( 'Image', 'acp' ),
					'page'     => esc_html__( 'Page', 'acp' ),
					'external' => esc_html__( 'External', 'acp' ),
				),
			)
		);

		$acp_box->add_field(
			array(
				'name' => esc_html__( 'External Link', 'acp' ),
				'desc' => esc_html__( 'Enter the destination hyperlink', 'acp' ),
				'id'   => '_acp_link_value',
				'type' => 'text_url',
			)
		);
	}
}
