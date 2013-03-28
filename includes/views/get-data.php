<?php
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
                    'taxonomy' => $defaults['taxonomy']['slug'],
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
        $termid = get_term_by( 'slug', $terms, 'feature' );
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
        
        $a['orderby'] = $terms_orderby;
    }

    // Allow a user to filter the terms list to add their own parameters.
    $a = apply_filters( 'arconix_portfolio_get_terms', $a );

    // Get the tax terms only from the items in our query
    $get_terms = get_terms( 'feature', $a );        
    
    // If there are multiple terms in use, then run through our display list
    if( count( $get_terms ) > 1 )  {
        $return .= '<ul class="arconix-portfolio-features">';
        
        if( $heading)
            $return .= "<li class='arconix-portfolio-category-title'>{$heading}</li>";

        $return .= '<li class="active"><a href="javascript:void(0)" class="all">All</a></li>';

        $term_list = '';

        // Break each of the items into individual elements and modify the output
        foreach( $get_terms as $term ) {
            $term_list .= '<li><a href="javascript:void(0)" class="' . $term->slug . '">' . $term->name . '</a></li>';
        }

        /** Return our modified list */
        $return .= $term_list . '</ul>';
    }


    $return .= '<ul class="arconix-portfolio-grid">';

while( $portfolio_query->have_posts() ) : $portfolio_query->the_post();

    // Get the terms list
    $get_the_terms = get_the_terms( get_the_ID(), 'feature' );

    // Add each term for a given portfolio item as a data type so it can be filtered by Quicksand
    $return .= '<li data-id="id-' . get_the_ID() . '" data-type="';
    
    if( $get_the_terms ) {
        foreach ( $get_the_terms as $term ) {
            $return .= $term->slug . ' ';
        }
    }
    
    $return .= '">';

    // Above image Title output
    if( $title == "above" ) $return .= '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';

    // Handle the image link
    switch( $link ) {
        case "page" :
            $return .= '<a href="' . get_permalink() . '" rel="bookmark">';                        
            $return .= get_the_post_thumbnail( get_the_ID(), $thumb );
            $return .= '</a>';
            break;

        case "image" :
            $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
            $return .= '<a href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
            $return .= get_the_post_thumbnail( get_the_ID(), $thumb );
            $return .= '</a>';
            break;

        default : // If it's anything else, return nothing.
            break;
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
