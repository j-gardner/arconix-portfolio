<?php
/**
 * Defines and handles the Portfolio Post Type and Feature Taxonomy registration.
 *
 * @since   1.4.0
 * @package arconix-portfolio/admin
 */

/**
 * Create the Portfolio post type.
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
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		add_action( 'init', array( $this, 'content_types' ) );
		add_action( 'dashboard_glance_items', array( $this, 'at_a_glance' ) );

		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
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
		register_taxonomy( $defaults['taxonomy']['slug'], $defaults['post_type']['slug'], $defaults['taxonomy']['args'] );
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
		// Establishes plugin registration defaults for post type and taxonomy.
		$defaults = array(
			'post_type' => array(
				'slug' => 'portfolio',
				'args' => array(
					'labels'        => array(
						'name'               => __( 'Portfolio', 'acp' ),
						'singular_name'      => __( 'Portfolio', 'acp' ),
						'add_new'            => __( 'Add New', 'acp' ),
						'add_new_item'       => __( 'Add New Portfolio Item', 'acp' ),
						'edit'               => __( 'Edit', 'acp' ),
						'edit_item'          => __( 'Edit Portfolio Item', 'acp' ),
						'new_item'           => __( 'New Item', 'acp' ),
						'view'               => __( 'View Portfolio', 'acp' ),
						'view_item'          => __( 'View Portfolio Item', 'acp' ),
						'search_items'       => __( 'Search Portfolio', 'acp' ),
						'not_found'          => __( 'No portfolio items found', 'acp' ),
						'not_found_in_trash' => __( 'No portfolio items found in Trash', 'acp' ),
					),
					'public'        => true,
					'query_var'     => true,
					'menu_position' => 20,
					'menu_icon'     => 'dashicons-portfolio',
					'has_archive'   => false,
					'supports'      => array( 'title', 'editor', 'thumbnail' ),
					'rewrite'       => array(
						'slug'       => 'portfolio',
						'with_front' => false,
					),
				),
			),
			'taxonomy'  => array(
				'slug' => 'feature',
				'args' => array(
					'labels'                => array(
						'name'                       => __( 'Features', 'acp' ),
						'singular_name'              => __( 'Feature', 'acp' ),
						'search_items'               => __( 'Search Features', 'acp' ),
						'popular_items'              => __( 'Popular Features', 'acp' ),
						'all_items'                  => __( 'All Features', 'acp' ),
						'parent_item'                => null,
						'parent_item_colon'          => null,
						'edit_item'                  => __( 'Edit Feature', 'acp' ),
						'update_item'                => __( 'Update Feature', 'acp' ),
						'add_new_item'               => __( 'Add New Feature', 'acp' ),
						'new_item_name'              => __( 'New Feature Name', 'acp' ),
						'separate_items_with_commas' => __( 'Separate features with commas', 'acp' ),
						'add_or_remove_items'        => __( 'Add or remove features', 'acp' ),
						'choose_from_most_used'      => __( 'Choose from the most used features', 'acp' ),
						'menu_name'                  => __( 'Features', 'acp' ),
					),
					'hierarchical'          => false,
					'show_ui'               => true,
					'show_admin_column'     => true,
					'update_count_callback' => '_update_post_term_count',
					'query_var'             => true,
					'rewrite'               => array( 'slug' => 'feature' ),
				),
			),
		);

		return apply_filters( 'arconix_portfolio_defaults', $defaults );
	}

	/**
	 * Correct messages when Portfolio post type is saved
	 *
	 * @since   0.9.0
	 * @version 1.4.0
	 * @global  stdObject $post    WP Post object
	 * @global  int $post_ID       Post ID
	 * @param   array $messages    Existing array of messages.
	 * @return  array              updated messages
	 */
	public function updated_messages( $messages ) {
		global $post, $post_ID;
		$post_type = get_post_type( $post_ID );

		$obj      = get_post_type_object( $post_type );
		$singular = $obj->labels->singular_name;

		$messages[ $post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			// translators: %1$s: singular name, %2$s: URL, %3$s: singular name in lowercase.
			1  => sprintf( __( '%1$s updated. <a href="%2$s">View %3$s</a>' ), $singular, esc_url( get_permalink( $post_ID ) ), strtolower( $singular ) ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			// translators: %s: singular name.
			4  => sprintf( __( '%s updated.' ), $singular ),
			// translators: %1$s: singular name, %2$s: Revision title.
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s' ), $singular, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.
			// translators: %1$s: singular name, %2$s: URL, %3$s: singular name in lowercase.
			6  => sprintf( __( '%1$s published. <a href="%2$s">View %3$s</a>' ), $singular, esc_url( get_permalink( $post_ID ) ), strtolower( $singular ) ),
			7  => __( 'Page saved.' ),
			// translators: %1$s: singular name, %2$s: URL, %3$s: singular name in lowercase.
			8  => sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %3$s</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), strtolower( $singular ) ),
			// translators: %1$s: singular name, %2$s: Post date,  %3$s: URL, %3$s: singular name in lowercase.
			9  => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %4$s</a>' ), $singular, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), strtolower( $singular ) ),
			// translators: %1$s: singular name, %2$s: URL, %3$s: singular name in lowercase.
			10 => sprintf( __( '%1$s draft updated. <a target="_blank" href="%2$s">Preview %3$s</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), strtolower( $singular ) ),
		);
		return $messages;
	}

	/**
	 * Add the Portfolio post type and Feature taxonomy to the WP 3.8 "At a Glance" dashboard
	 *
	 * @since 1.4.0
	 */
	public function at_a_glance() {
		$glancer = new Gamajo_Dashboard_Glancer();
		$glancer->add( 'portfolio' );
	}

}
