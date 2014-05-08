<?php

class Arconix_Portfolio {

    /**
     * Construct Method
     */
    function __construct() { }

    /**
     * Establish the portfolio item defaults
     *
     * @return array defaults
     *
     * @since  1.4.0
     */
    function defaults() {
        $defaults = array(
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
        );

        return apply_filters( 'arconix_portfolio_query_defaults', $defaults );
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
    *
    * @since   1.2.0
    * @version 1.4.0
    *
    */
    function loop( $args, $echo = false ) {
        // Get our default arguments
        $d = $this->defaults();

        // Merge incoming args with the function defaults
        $args = wp_parse_args( $args, $d );

        if( $args['title'] != "below" ) $args['title'] == "above"; // For backwards compatibility with "yes" and built-in data check

        // Default Query arguments
        $qargs = array(
            'post_type'         => 'portfolio',
            'meta_key'          => '_thumbnail_id', // Pull only items with featured images
            'posts_per_page'    => $args['posts_per_page'],
            'orderby'           => $args['orderby'],
            'order'             => $args['order'],
        );

        // If the user has defined any tax terms, then we create our tax_query and merge to our main query
        $terms = $args['terms'];
        if( $terms ) {
            $tax_qargs = array(
                'tax_query' => array(
                    array(
                        'taxonomy'  => 'feature',
                        'field'     => 'slug',
                        'terms'     => $args['terms'],
                        'operator'  => $args['operator']
                    )
                )
            );

            // Merge the tax array with the general query
            $qargs = array_merge( $qargs, $tax_qargs );
        }

        ob_start(); // start our buffer to be output at the end of the function


        // After all that build up, run our query
        $query = new WP_Query( apply_filters( 'arconix_portfolio_query', $qargs ) );

        if( $query->have_posts() ) {

            // right now this function calls the list directly.
            $this->arconix_portfolio_filter_list( $args );

            while( $query->have_posts() ) : $query->the_post();

                $this->arconix_portfolio_item( $args );

            endwhile;
        } // End if have post

    $this->arconix_portfolio_after_items( $args );

    wp_reset_postdata();


    // Either echo or return the results
    if( $echo )
        echo ob_get_clean();
    else
        return ob_get_clean();
    }

    /**
     * Runs after the items have been output
     *
     * @param  array   $args incoming arguments
     * @param  boolean $echo return or echo the data
     *
     * @return string
     *
     * @since  1.4.0
     */
    function arconix_portfolio_after_items( $args, $echo = true ) {
        ob_start();

        echo '</ul>';


        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

    /**
     * Runs after we know there are portfolio items to loop through but before we actually
     * loop through them
     *
     * @param  array   $args the args pushed into the function (typically via shortcode)
     * @param  boolean $echo echo or return the data
     *
     * @return string        the image wrapped in a hyperlink
     *
     * @since  1.4.0
     */
    function arconix_portfolio_do_before_items( $args, $echo = true ) {
        // FUNCTION IN DEVELOPMENT RIGHT NOW
        ob_start();

        do_action( 'arconix_portfolio_before_items' );

        // Either echo or return the results
        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

    /**
     * Determines if the 'features' have been added to the portfolio items returned
     * by the query. If there are more than one, we create an unordered list to display
     * them.
     *
     * @param  array   $terms the terms used to create the filter list
     * @param  array   $args  the args pushed into the function (typically via shortcode)
     * @param  boolean $echo  echo or return the data
     *
     * @return string         the image wrapped in a hyperlink
     *
     * @since  1.4.0
     */
    function arconix_portfolio_filter_list( $args, $echo = true ) {
        ob_start();

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

        // If we have multiple terms, then build our filter list
        if( count( $terms ) > 1 ) {
            echo '<ul class="arconix-portfolio-features">';

            if( $args['heading'] ) {
                $heading = $args['heading'];

                echo "<li class='arconix-portfolio-category-title'>{$heading}</li>";
            }

            echo '<li class="arconix-portfolio-feature active"><a href="javascript:void(0)" class="all">' . __( 'All', 'acp' ) . '</a></li>';

            // Break each of the items into individual elements and modify the output
            foreach( $terms as $term ) {
                echo '<li class="arconix-portfolio-feature"><a href="javascript:void(0)" class="' . $term->slug . '">' . $term->name . '</a></li>';
            }

            echo '</ul>';

        }

        echo '<ul class="arconix-portfolio-grid">';




        // Either echo or return the results
        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }


    /**
     * Output the individual portfolio item. Runs inside the loop
     *
     * @param  array   $args the args pushed into the function (typically via shortcode)
     * @param  boolean $echo echo or return the data
     *
     * @return string        the image wrapped in a hyperlink
     *
     * @since  1.4.0
     */
    function arconix_portfolio_item( $args, $echo = true ) {
        ob_start();

        $id = get_the_ID();

        // Get the terms list
        $get_the_terms = get_the_terms( $id, 'feature' );

        // Add each term for a given portfolio item as a data type so it can be filtered by Quicksand
        echo '<li data-id="id-' . $id . '" data-type="';

            if( $get_the_terms ) {
                foreach ( $get_the_terms as $term ) {
                    echo $term->slug . ' ';
                }
            }

        echo '">';

        // Above image Title output
        if( $args['title'] == "above" )
            echo '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';


        // Outputs the image wrapped in the appropriate hyperlink
        $this->arconix_portfolio_item_image( $args );


        // Below image Title output
        if( $args['title'] == "below" )
            echo '<div class="arconix-portfolio-title">' . get_the_title() . '</div>';

        // Display the content
        switch( $args['display'] ) {
            case "content" :
                echo '<div class="arconix-portfolio-text">' . get_the_content() . '</div>';
                break;

            case "excerpt" :
                echo '<div class="arconix-portfolio-text">' . get_the_excerpt() . '</div>';
                break;

            default : // If it's anything else, return nothing.
                break;
        }

        echo '</li>';

        // Either echo or return the results
        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }


    /**
     * Handles the output of the portfolio item image including what link is fired
     *
     * @param  array   $args the args pushed into the function (typically via shortcode)
     * @param  boolean $echo echo or return the data
     *
     * @return string        the image wrapped in a hyperlink
     *
     * @since  1.4.0
     */
    function arconix_portfolio_item_image( $args, $echo = true ) {

        ob_start();

        $link = $args['link'];

        $id = get_the_ID();

        /**
         * As of v1.3.0, the destination of the image link can be defined at the item level. In order to remain
         * backwards compatible we have to check if a shortcode parameter was set. If a shortcode param was set,
         * that takes precedence
         */
        if( $link ) {
            switch( $link ) {
                case "page" :
                    echo '<a class="page" href="' . get_permalink() . '" rel="bookmark">';
                    echo get_the_post_thumbnail( $id, $args['thumb'] );
                    echo '</a>';
                    break;

                case "image" :
                default :
                    $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $args['full'] );
                    echo '<a class="image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                    echo get_the_post_thumbnail( $id, $args['thumb'] );
                    echo '</a>';
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
                    echo '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                    echo get_the_post_thumbnail( $id, $args['thumb'] );
                    echo '</a>';
                    echo '<!-- link type = ' . $link_type . ' -->';
                    break;

                case 'page' :
                    echo '<a class="portfolio-page" href="' . get_permalink() . '" rel="bookmark">';
                    echo get_the_post_thumbnail( $id, $args['thumb'] );
                    echo '</a>';
                    echo '<!-- link type = ' . $link_type . ' -->';
                    break;

                case 'external' :
                    if( empty( $link_value ) ) { // If the user forgot to enter a link value in the text box, just show the image
                        $_portfolio_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $args['full'] );
                        echo '<a class="portfolio-image" href="' . $_portfolio_img_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                        echo get_the_post_thumbnail( $id, $args['thumb'] );
                        echo '</a>';
                        echo '<!-- link value missing -->';
                    }
                    else {
                        $extra_class = '';
                        $extra_class = apply_filters( 'arconix_portfolio_external_link_class', $extra_class );
                        echo '<a class="portfolio-external '. $extra_class . '" href="' . esc_url( $link_value ) . '">';
                        echo get_the_post_thumbnail( $id, $args['thumb'] );
                        echo '</a>';
                        echo '<!-- link type = ' . $link_type . ' -->';
                    }
                    break;
            }
        }

        // Either echo or return the results
        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

}