<?php
/**
 * Class to handle the output of the portfolio items
 *
 * @since 1.0.0
 */
class Arconix_Portfolio {

    /**
     * Holds loop defaults, populated in constructor.
     *
     * @since   1.0.0
     * @access  protected
     * @var     array       $defaults   default args
     */
    protected $defaults;

    /**
     * Constructor
     *
     * Populates the $defaults var
     *
     * @since   1.4.0
     */
    function __construct() {
        $this->defaults = array(
            'link'              => '',
            'thumb'             => 'portfolio-thumb',
            'full'              => 'portfolio-large',
            'title'             => 'above',
            'title_link'        => 'false',
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
    }

    /**
     * Returns a filterable array of class defaults.
     *
     * @since   1.4.0
     * @return  array    $defaults
     */
    function defaults() {
        return apply_filters( 'arconix_portfolio_query_defaults', $this->defaults );
    }

   /**
    * Return Porfolio Content
    *
    * Grab all portfolio items from the database and sets up their display. Accepts an array of arguments (see $defaults)
    *
    * Supported Arguments
    * - link            => image | page | external - External is only supported at the item level
    * - thumb           => any built-in image size
    * - full            => any built-in image size (this setting is ignored of 'link' is set to 'page')
    * - title           => above | below - Display the item title above or below the image
    * - title_link      => true | false - Wrap the portfolio title in the same hyperlink as the image
    * - display         => content | excerpt - Output the full content or an excerpt only. Leave blank for nothing
    * - heading         => When displaying the 'feature' items in a row above the portfolio items, define the heading text for that section.
    * - orderby         => date | any other orderby param available. {@see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters}
    * - order           => ASC | DESC
    * - posts_per_page  => Limit how many portfolio items are returned (-1 is all)
    * - terms_orderby   => Name or any other orderby param available. {@see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters}
    * - terms_order     => ASC | DESC
    * - terms           => A 'feature' tag you want to filter on
    * - operator        => IN | NOT IN - Include or exclude the value defined in the 'terms' param
    *
    * 'Image' is the only officially supported link option at the shortcode level. While linking to a page is possible, it may require additional coding
    * knowledge due to the fact that there are so many themes and nearly every one is different. External is not supported as a shortcode parameter.
    * {@see http://arconixpc.com/2012/linking-portfolio-items-to-pages }
    *
    * @since    1.2.0
    * @version  1.4.0
    * @param    array   $args   Incoming arguments
    * @param    bool    $echo   Echo or return the data
    * @return   string          Unordered list of portfolio items all dressed up with images and hyperlinks
    */
    function loop( $args, $echo = false ) {
        // Merge incoming args with the function defaults
        $args = wp_parse_args( $args, $this->defaults() );
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
        if( $args['terms'] ) {
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

        $s = ''; // our return container

        // Run our Query
        $query = new WP_Query( apply_filters( 'arconix_portfolio_query', $qargs ) );

        if( $query->have_posts() ) :

            $s .= $this->before_items( $args );

            while( $query->have_posts() ) : $query->the_post();

                $s .= $this->item( $args );

            endwhile;

            $s .= $this->after_items( $args );

        endif;

        wp_reset_postdata();

        // Either echo or return the results
        if( $echo === true )
            echo $s;
        else
            return $s;
    }

    /**
     * Runs if there are portfolio items to loop through but before the items are output.
     *
     * Outputs the features list (if the 'feature' taxonomy is used) and the opening <ul> tag
     *
     * @since  1.4.0
     * @param  array    $args   Args pushed into the function (typically via shortcode)
     * @return string
     */
    function before_items( $args ) {
        $s = $this->get_filter_list( $args );

        $s .= $this->begin_portfolio_grid();

        return apply_filters( 'arconix_portfolio_before_items', $s, $args );
    }

    /**
     * Creates the individual portfolio item.
     *
     * Main item wrapper. Returns each portfolio item, its title and content if available
     *
     * @since  1.4.0
     * @param  array    $args   The args pushed into the function (typically via shortcode)
     * @return string           The image wrapped in a hyperlink
     */
    function item( $args ) {
        // Get the terms list
        $id = get_the_ID();
        $get_the_terms = get_the_terms( $id, 'feature' );

        // Add each term for a given portfolio item as a data type so it can be filtered by Quicksand
        $s = '<li data-id="id-' . $id . '" data-type="';

            if ( $get_the_terms ) {
                foreach ( $get_the_terms as $term )
                    $s .= $term->slug . ' ';
            }

        $s .= '">';

        // Above image Title output
        if( $args['title'] == "above" ) $s .= $this->get_portfolio_title( $args['title_link'], $args['link'], $args['full'] );

        // Outputs the image wrapped in the appropriate hyperlink
        $s .= $this->get_portfolio_image( $args['link'], $args['thumb'], $args['full'] );

        // Below image Title output
        if( $args['title'] == "below" ) $s .= $this->get_portfolio_title( $args['title_link'], $args['link'], $args['full'] );

        $s .= $this->get_portfolio_content( $args['display'] );

        $s .= '</li>';

        return apply_filters( 'arconix_portfolio_item', $s, $args );
    }

    /**
     * Runs after the portfolio items have been output.
     *
     * Returns the closing <ul> tag, but accepts the main arguments array so it can be filtered if need be
     *
     * @since  1.4.0
     * @param  array    $args   incoming arguments
     * @return string
     */
    function after_items( $args ) {
        $s = $this->end_portfolio_grid();

        return apply_filters( 'arconix_portfolio_before_items', $s, $args );
    }

    /**
     * Creates the unordered list of features that can be clicked on to animate a filter functionality.
     *
     * Determines if the 'features' have been added to the portfolio items returned
     * by the query. If there are more than one, we create an unordered list to display
     * them.
     *
     * @since  1.4.0
     * @param  array    $args   Args pushed into the function (typically via shortcode)
     * @return string           An unordered list of "features" to power the filter functionality
     */
    function get_filter_list( $args ) {
        $s = '';
        $a = array(); // Var to hold our operate arguments

        if( $args['terms'] ) {
            // Translate our user-entered slug into an id we can use
            $termid = get_term_by( 'slug', $args['terms'], 'feature' );
            $termid = $termid->term_id;

            // Change the get_terms argument based on the shortcode $operator, but default to IN
            switch( $args['operator'] ) {
                // All except this term
                case "NOT IN":
                    $a = array( 'exclude' => $termid );
                    break;

                // Just this term
                case "IN":
                default:
                    $a = array( 'include' => $termid );
                    break;
            }
        }

        // Set our terms list orderby and order
        $a['orderby'] = $args['terms_orderby'];
        $a['order'] = $args['terms_order'];

        // Get the tax terms only from the items in our query, allowing the user to filter the arguments
        $terms = get_terms( 'feature', apply_filters( 'arconix_portfolio_get_terms', $a ) );

        // If we have multiple terms, then build our filter list
        if( count( $terms ) > 1 ) {
            $s .= '<ul class="arconix-portfolio-features">';

            if( $args['heading'] )
                $s .= "<li class='arconix-portfolio-category-title'>{$args['heading']}</li>";

            $s .= '<li class="arconix-portfolio-feature active"><a href="javascript:void(0)" class="all">' . __( 'All', 'acp' ) . '</a></li>';

            // Break each of the items into individual elements and modify the output
            foreach( $terms as $term )
                $s .= '<li class="arconix-portfolio-feature"><a href="javascript:void(0)" class="' . $term->slug . '">' . $term->name . '</a></li>';

            $s .= '</ul>';
        }

        return $s;
    }

    /**
     * Start the portfolio unordered list
     *
     * @since   1.4.0
     * @return  string          Begin the unordered portfolio list
     */
    function begin_portfolio_grid() {
        return '<ul class="arconix-portfolio-grid">';
    }

    /**
     * Handles the output of the portfolio item image including what link is fired
     *
     * @since   1.4.0
     * @param   string  $link   image | page - If not set at the shortcode level, will be assigned the item level setting
     * @param   string  $thumb  Image size of the thumbnail
     * @param   string  $full   Image size of the full image (ignored if linking to a page or external site)
     * @return  string          Image wrapped in an appropriate hyperlink
     */
    function get_portfolio_image( $link, $thumb, $full ) {
        $id = get_the_ID();
        $extra_class = apply_filters( 'arconix_portfolio_external_link_class', '' );
        $url = $this->get_portfolio_hyperlink( $link, $full );
        $s = '';
        if ( ! $link ) $link = get_post_meta( $id, '_acp_link_type', true );
        if ($link != 'none') $s = '<a class="portfolio-' . $link . ' ' . $extra_class . '" href="' . $url . '">';
       
        $s .= get_the_post_thumbnail( $id, $thumb );
        if ($link != 'none') $s .= '</a>';

        return $s;
    }

    /**
     * Get the Portfolio title
     *
     * @since   1.4.0
     * @param   bool    $is_link    true | false - Wrap the portfolio title in a hyperlink
     * @param   string  $link       image | page | external - Where the hyperlink will point to (ignored if $is_link is false)
     * @param   string  $full       larger image size (ignored if $is_link is false or $link is not 'image')
     * @return  string              The portfolio title
     */
    function get_portfolio_title( $is_link, $link, $full ) {
        $s = '<div class="arconix-portfolio-title">';

        if ( $is_link === 'true' )
            $s .= '<a href="' . $this->get_portfolio_hyperlink( $link, $full) . '">';

        $s .= get_the_title();

        if ( $is_link === 'true' )
            $s .= '</a>';

        $s .= '</div>';

        return apply_filters( 'arconix_portfolio_item_title', $s, $is_link, $link );
    }

    /**
     * Display the portfolio content
     *
     * @since   1.4.0
     * @param   string  $display    content | excerpt | none - What content should be displayed with this portfolio item
     * @return  string              Early if set to none, otherwise the content or excerpt
     */
    function get_portfolio_content( $display ) {
        switch( $display ) {
            case "content" :
                return '<div class="arconix-portfolio-text">' . get_the_content() . '</div>';
                break;

            case "excerpt" :
                return '<div class="arconix-portfolio-text">' . get_the_excerpt() . '</div>';
                break;

            default : // If it's anything else, return nothing.
                return;
                break;
        }

    }

    /**
     * Get the proper portfolio hyperlink
     *
     * Based on what's set at the shortcode or portfolio item level, return the
     * correct URL for the correct scenario. When 'image' is the destination, the
     * $full argument is used to note the larger image size determined by the user
     *
     * @since   1.4.0
     * @param   string  $link   image | page | external - If not set at the shortcode level, will be assigned the item level setting
     * @param   string  $full   Image size of the full image (ignored not linking to an image)
     * @return  string          Returns the proper portfolio url
     */
    function get_portfolio_hyperlink( $link, $full ) {
        $id = get_the_ID();

        if ( ! $link )
            $link = get_post_meta( $id, '_acp_link_type', true );

        switch ( $link ) {
            case 'page' :
                $url = get_permalink();
                break;
            case 'external' :
                $url = esc_url( get_post_meta( $id, '_acp_link_value', true ) );
                break;
            case 'image' :
            default :
                $img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $full );
                $url = $img_url[0];
                break;
        }

        return $url;
    }

    /**
     * Close the unordered portfolio list
     *
     * @since   1.4.0
     * @return  string          The closing unordered list html tag
     */
    function end_portfolio_grid() {
        return '</ul>';
    }
}