<?php
/**
 * Defines and handles all backend plugin operation
 *
 * @since   1.0.0
 * @package arconix-portfolio/admin
 */

/**
 * Defines and handles all backend plugin operation.
 */
class Arconix_Portfolio_Admin {

	/**
	 * The version of this plugin.
	 *
	 * @since   1.4.0
	 * @access  private
	 * @var     string      $version    The vurrent version of this plugin.
	 */
	private $version;

	/**
	 * The directory path to this plugin.
	 *
	 * @since   1.4.0
	 * @access  private
	 * @var     string      $dir    The directory path to this plugin
	 */
	private $dir;

	/**
	 * The url path to this plugin.
	 *
	 * @since   1.4.0
	 * @access  private
	 * @var     string      $url    The url path to this plugin
	 */
	private $url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.2.0
	 * @version 1.4.0
	 * @access  public
	 * @param   string $version    The version of this plugin.
	 */
	public function __construct( $version ) {
		$this->version = $version;
		$this->dir     = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->url     = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->init();
	}

	/**
	 * Init the Admin side
	 *
	 * Loads all actions and filters to be used.
	 *
	 * @since   1.4.0
	 */
	public function init() {
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'admin_scripts' ), 11 );
		add_action( 'admin_print_scripts-post.php', array( $this, 'admin_scripts' ), 11 );
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

		add_filter( 'manage_portfolio_posts_columns', array( $this, 'columns_filter' ) );
		add_filter( 'widget_text', 'do_shortcode' );

		add_image_size( 'portfolio-thumb', 275, 200 );
		add_image_size( 'portfolio-large', 620, 9999 );

		add_shortcode( 'portfolio', array( $this, 'acp_portfolio_shortcode' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'display_custom_quickedit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_quick_meta' ) );
		// For use if Arconix Flexslider is active.
		add_filter( 'arconix_flexslider_slide_image_return', array( $this, 'flexslider_image_return' ), 10, 4 );
	}

	/**
	 * Save the portfolio settings.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_quick_meta( $post_id ) {

		if ( isset( $_POST['_acp_link_type'] ) ) { // phpcs:ignore
			update_post_meta( $post_id, '_acp_link_type', $_POST['_acp_link_type'] ); // phpcs:ignore
		}
		if ( isset( $_POST['_acp_link_value'] ) ) { // phpcs:ignore
			update_post_meta( $post_id, '_acp_link_value', $_POST['_acp_link_value'] ); // phpcs:ignore
		}

	}

	/**
	 * Define Quick edit settings.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 * @return void
	 */
	public function display_custom_quickedit( $column_name, $post_type ) {
		wp_enqueue_script( 'arconix-portfolio-admin-js', $this->url . 'js/admin.js', array( 'jquery' ), $this->version, true );
		switch ( $column_name ) {
			case 'portfolio_link':
				?>
			<fieldset class="inline-edit-col-right inline-edit">
			<div class="inline-edit-col column-<?php echo $column_name; ?>">
				<label class="inline-edit-group">
					<span class="title">Link Type</span>
					<select class="cmb_select" name="_acp_link_type" id="_acp_link_type">   
						<option value="none">None</option>
						<option value="image">Image</option>
						<option value="page">Page</option>
						<option value="external">External Link</option>
					</select>
				</label>
				<label  id="_acp_link_value" for="_acp_link_value" style="display: none;">External Link
					<input type="text" class="regular-text" name="_acp_link_value" value="">
					<p class="cmb_metabox_description">Enter the destination hyperlink</p>
				</label>
			</div>
			</fieldset>
				<?php
				break;
		}
	}
	/**
	 * Filter the columns on the admin screen and define our own
	 *
	 * @since    0.9.0
	 * @version  1.4.0
	 * @param    array $columns Define columns in the admin.
	 * @return   array $columns
	 */
	public function columns_filter( $columns ) {
		$col_img  = array( 'portfolio_thumbnail' => __( 'Image', 'acp' ) );
		$col_desc = array( 'portfolio_description' => __( 'Description', 'acp' ) );
		$col_link = array( 'portfolio_link' => __( 'Link Type', 'acp' ) );

		$columns = array_slice( $columns, 0, 1, true ) + $col_img + array_slice( $columns, 1, null, true );
		$columns = array_slice( $columns, 0, 3, true ) + $col_desc + array_slice( $columns, 3, null, true );
		$columns = array_slice( $columns, 0, 4, true ) + $col_link + array_slice( $columns, 4, null, true );

		return apply_filters( 'arconix_portfolio_columns', $columns );
	}

	/**
	 * Filter the data that shows up in the columns we defined above
	 *
	 * @since   0.9.0
	 * @version 1.2.0
	 * @global  stdObject   $post
	 * @param   array $column Column name.
	 */
	public function columns_data( $column ) {
		global $post;

		switch ( $column ) {
			case 'portfolio_thumbnail':
				printf( '<p>%s</p>', the_post_thumbnail( 'thumbnail' ) );
				break;
			case 'portfolio_description':
				the_excerpt();
				break;
			case 'portfolio_link':
				printf( '<div class="portfolio-link-type">%s</div>', get_post_meta( $post->ID, '_acp_link_type', true ) );
				break;
		}
	}

	/**
	 * Load the plugin scripts. If the css file is present in the theme directory, it will be loaded instead,
	 * allowing for an easy way to override the default template. If you'd like to remove the CSS or JS entirely,
	 * such as when building the styles or scripts into a single file, simply reference the filter and return false
	 *
	 * @example add_filter( 'pre_register_arconix_portfolio_js', '__return_false' );
	 *
	 * @since   0.9
	 * @version 1.4.0
	 */
	public function scripts() {
		// If WP_DEBUG is true, load the non-minified versions of the files (for development environments).
		SCRIPT_DEBUG === true ? $prefix = '.min' : $prefix = '';

		wp_register_script( 'jquery-quicksand', $this->url . 'js/jquery.quicksand' . $prefix . '.js', array( 'jquery' ), '1.4', true );
		wp_register_script( 'jquery-easing', $this->url . 'js/jquery.easing.1.3' . $prefix . '.js', array( 'jquery-quicksand' ), '1.3', true );

		// JS -- Only requires jquery-easing as Easing requires Quicksand, which requires jQuery, so all dependencies load in the correct order.
		if ( apply_filters( 'pre_register_arconix_portfolio_js', true ) ) {
			if ( file_exists( get_stylesheet_directory() . '/arconix-portfolio.js' ) ) {
				wp_register_script( 'arconix-portfolio-js', get_stylesheet_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), $this->version, true );
			} elseif ( file_exists( get_template_directory() . '/arconix-portfolio.js' ) ) {
				wp_register_script( 'arconix-portfolio-js', get_template_directory_uri() . '/arconix-portfolio.js', array( 'jquery-easing' ), $this->version, true );
			} else {
				wp_register_script( 'arconix-portfolio-js', $this->url . 'js/arconix-portfolio.js', array( 'jquery-easing' ), $this->version, true );
			}
		}

		// CSS.
		if ( apply_filters( 'pre_register_arconix_portfolio_css', true ) ) {
			if ( file_exists( get_stylesheet_directory() . '/arconix-portfolio.css' ) ) {
				wp_enqueue_style( 'arconix-portfolio', get_stylesheet_directory_uri() . '/arconix-portfolio.css', false, $this->version );
			} elseif ( file_exists( get_template_directory() . '/arconix-portfolio.css' ) ) {
				wp_enqueue_style( 'arconix-portfolio', get_template_directory_uri() . '/arconix-portfolio.css', false, $this->version );
			} else {
				wp_enqueue_style( 'arconix-portfolio', $this->url . 'css/arconix-portfolio.css', false, $this->version );
			}
		}

	}

	/**
	 * Load javascript on the portfolio post admin screen
	 *
	 * @since   1.4.0
	 * @global  string  $post_type  Post Type being manipulated
	 */
	public function admin_scripts() {
		global $post_type;
		if ( 'portfolio' === $post_type ) {
			wp_enqueue_script( 'arconix-portfolio-admin-js', $this->url . 'js/admin.js', array( 'jquery' ), $this->version, true );
		}
	}

	/**
	 * Includes admin css
	 *
	 * @since  1.2.0
	 */
	public function admin_css() {
		wp_enqueue_style( 'arconix-portfolio-admin', $this->url . 'css/admin.css', false, $this->version );
	}

	/**
	 * Adds a widget to the dashboard.
	 *
	 * Can be removed entirely via a filter, but is visible by default for admins only
	 *
	 * @since   0.9.1
	 * @version 1.3.0
	 */
	public function register_dashboard_widget() {
		if ( apply_filters( 'pre_register_arconix_portfolio_dashboard_widget', true ) &&
			apply_filters( 'arconix_portfolio_dashboard_widget_security', current_user_can( 'manage_options' ) ) ) {
				wp_add_dashboard_widget( 'ac-portfolio', 'Arconix Portfolio', array( $this, 'dashboard_widget_output' ) );
		}
	}

	/**
	 * Output for the dashboard widget
	 *
	 * @since   0.9.1
	 * @version 1.4.0
	 */
	public function dashboard_widget_output() {
		echo '<div class="rss-widget">';

		wp_widget_rss_output(
			array(
				'url'          => 'http://arconixpc.com/tag/arconix-portfolio/feed', // feed url.
				'title'        => 'Arconix Portfolio Posts', // feed title.
				'items'        => 3, // how many posts to show.
				'show_summary' => 1, // display excerpt.
				'show_author'  => 0, // display author.
				'show_date'    => 1, // display post date.
			)
		);

		echo '<div class="acp-widget-bottom"><ul>';
		?>
		<li><a href="http://arcnx.co/apwiki"><img src="<?php echo $this->url . 'images/page-16x16.png'; ?>">Documentation</a></li>
		<li><a href="http://arcnx.co/aphelp"><img src="<?php echo $this->url . 'images/help-16x16.png'; ?>">Support Forum</a></li>
		<li><a href="http://arcnx.co/aptrello"><img src="<?php echo $this->url . 'images/trello-16x16.png'; ?>">Dev Board</a></li>
		<li><a href="http://arcnx.co/apsource"><img src="<?php echo $this->url . 'images/github-16x16.png'; ?>">Source Code</a></li>
		<?php
		echo '</ul></div></div>';
	}

	/**
	 * Portfolio Shortcode
	 *
	 * @param   array  $atts Shortcode attributes.
	 * @param   string $content Content.
	 * @since   0.9
	 * @version 1.3.1
	 */
	public function acp_portfolio_shortcode( $atts, $content = null ) {
		if ( wp_script_is( 'arconix-portfolio-js', 'registered' ) ) {
			wp_enqueue_script( 'arconix-portfolio-js' );
		}

		$p = new Arconix_Portfolio();

		return $p->loop( $atts );
	}

	/**
	 * Modify the Arconix Flexslider image information
	 *
	 * References the custom URL config set by the portfolio items
	 *
	 * @since   1.4.0
	 * @global  stdObj      $post           Standard WP Post object
	 * @param   string $content        Existing image data.
	 * @param   bool   $link_image     Wrap the image in a hyperlink to the permalink (false for basic image slider).
	 * @param   string $image_size     The size of the image to display. Accepts any valid built-in or added WordPress image size.
	 * @param   string $caption        Caption to be displayed.
	 * @return  string      $s              string | null Modified flexslider image or nothing if we're not on a portfolio CPT
	 */
	public function flexslider_image_return( $content, $link_image, $image_size, $caption ) {
		global $post;

		// return early if we're not working with a portfolio item or we don't have a featured image.
		if ( 'portfolio' !== $post->post_type || ! has_post_thumbnail() ) {
			return;
		}

		$s = '<div class="arconix-slide-image-wrap">';

		if ( 'true' === $link_image || 1 === $link_image ) {
			/*
			 * Return the hyperlinked portfolio image. Setting the 1st param to false
			 * forces the plugin to use the portfolio item's link type. Since we're not
			 * running as a result of the shortcode, there's no way to know if that has
			 * been configured
			 */
			$p  = new Arconix_Portfolio();
			$s .= $p->get_portfolio_image( false, $image_size, 'full' );
		} else {
			$id = get_the_ID();
			$s .= get_the_post_thumbnail( $id, $image_size );
		}
		$f  = new Arconix_FlexSlider();
		$s .= $f->slide_caption( $caption );

		$s .= '</div>';

		return $s;
	}
}
