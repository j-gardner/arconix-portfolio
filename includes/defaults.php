<?php
// Establishes plugin registration defaults for post type and taxonomy
$defaults = array(
	'post_type' => array(
		'slug' => 'portfolio',
		'args' => array(
			'labels' => array(
				'name'					=> __( 'Portfolio',								'acp' ),
				'singular_name'			=> __( 'Portfolio',								'acp' ),
				'add_new'				=> __( 'Add New',								'acp' ),
				'add_new_item'			=> __( 'Add New Portfolio Item',				'acp' ),
				'edit'					=> __( 'Edit',									'acp' ),
				'edit_item'				=> __( 'Edit Portfolio Item',					'acp' ),
				'new_item'				=> __( 'New Item',								'acp' ),
				'view'					=> __( 'View Portfolio',						'acp' ),
				'view_item'				=> __( 'View Portfolio Item',					'acp' ),
				'search_items'			=> __( 'Search Portfolio',						'acp' ),
				'not_found'				=> __( 'No portfolio items found',				'acp' ),
				'not_found_in_trash'	=> __( 'No portfolio items found in Trash',		'acp' )
			),
			'public'			=> true,
			'query_var'			=> true,
			'menu_position'		=> 20,
			'menu_icon'			=> ACP_IMAGES_URL . 'portfolio-icon-16x16.png',
			'has_archive'		=> false,
			'supports'			=> array( 'title', 'editor', 'thumbnail' ),
			'rewrite'			=> array( 'slug' => 'portfolio', 'with_front' => false )
		)
	),
	'taxonomy' => array(
		'slug' => 'feature',
		'args' => array(
			'labels' => array(
				'name'							=> __( 'Features',								'acp' ),
				'singular_name'					=> __( 'Feature',								'acp' ),
				'search_items'					=> __( 'Search Features',						'acp' ),
				'popular_items'					=> __( 'Popular Features',						'acp' ),
				'all_items'						=> __( 'All Features',							'acp' ),
				'parent_item'					=> null,
				'parent_item_colon'				=> null,
				'edit_item'						=> __( 'Edit Feature' ,							'acp' ),
				'update_item'					=> __( 'Update Feature',						'acp' ),
				'add_new_item'					=> __( 'Add New Feature',						'acp' ),
				'new_item_name'					=> __( 'New Feature Name',						'acp' ),
				'separate_items_with_commas'	=> __( 'Separate features with commas',			'acp' ),
				'add_or_remove_items'			=> __( 'Add or remove features',				'acp' ),
				'choose_from_most_used'			=> __( 'Choose from the most used features',	'acp' ),
				'menu_name'						=> __( 'Features',								'acp' ),
			),
			'hierarchical'				=> false,
			'show_ui'					=> true,
			'update_count_callback'		=> '_update_post_term_count',
			'query_var'					=> true,
			'rewrite'					=> array( 'slug' => 'feature' )
		)
	)
);