<?php
namespace QuorStudio\Maker_Camp\TemplateFunctions;

/**
 * Default setup routine
 *
 * @uses add_action()
 * @uses do_action()
 *
 * @return void
 */
function setup() {
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	/**
	 * Setup template loader
	 */
	add_filter( 'template_include', $n( 'template_loader' ) );

	/**
	 * Template hooks
	 */
	add_action( 'makercamp_before_main_content', $n( 'makercamp_output_content_container' ) );
	add_action( 'makercamp_after_main_content', $n( 'makercamp_output_content_container_end' ) );
	add_action( 'makercamp_main_content_template', $n( 'makercamp_output_main_content' ) );

	add_action( 'makercamp_before_single_camp_day', $n( 'makercamp_output_single_weeks' ), 10 );
	add_action( 'makercamp_before_single_camp_day', $n( 'makercamp_output_single_videos' ), 20 );

	add_action( 'makercamp_single_camp_day_content', $n( 'makercamp_single_camp_day_content' ) );

	add_action( 'makercamp_after_single_camp_day', $n( 'makercamp_output_single_resources' ) );

	add_action( 'template_redirect', $n( 'locked_day_template_redirect' ) );

	do_action( 'makercamp_template_functions_loaded' );
}

/**
 * Redirect if we're on locked camp day
 */
function locked_day_template_redirect() {
	global $post;

	$redirect = FALSE;

	if ( is_singular( 'camp_day' ) || is_post_type_archive( 'camp_day' ) || is_tax( 'week' ) ) {

		if ( is_singular( 'camp_day' ) && ! is_preview() ) {
			$__is_locked = get_post_meta( $post->ID, '_lock_status', TRUE ) == 1 ? FALSE : TRUE;

			if ( $__is_locked ) {
				$redirect = TRUE;
			}
		}

		if ( is_post_type_archive( 'camp_day' ) || is_tax( 'week' ) ) {
			$redirect = TRUE;
		}

	}

	if ( $redirect ) {

		$last_unlocked_day = get_posts( array(
			'post_type'   => 'camp_day',
			'numberposts' => 1,
			'order'       => 'DESC',
			'meta_key'    => '_week_day',
			'orderby'     => 'meta_value_num',
			'meta_query'  => array(
				array(
					'key'     => '_week_day',
					'value'   => array( 1, 2, 3, 4, 5 ),
					'compare' => 'IN'
				),
				array(
					'key'   => '_lock_status',
					'value' => '1'
				)
			)
		) );

		$redirect_url = home_url();
		if ( ! empty( $last_unlocked_day ) ) {
			$redirect_url = get_permalink( $last_unlocked_day[ 0 ]->ID );
		}

		wp_redirect( $redirect_url );
		exit();
	}
}

/**
 * Template loader
 *
 * @param $template
 *
 * @return string
 */
function template_loader( $template ) {
	$find = array();
	$file = '';

	if ( is_single() && get_post_type() == 'camp_day' ) {

		$file    = 'single-camp_day.php';
		$find[ ] = $file;
		$find[ ] = MAKERCAMP_THEME_TEMPLATE_PATH . $file;

	}

	if ( $file ) {
		$template = locate_template( $find );
		if ( ! $template ) {
			$template = MAKERCAMP_INC . 'templates/' . $file;
		}
	}

	return $template;
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @access public
 *
 * @param string $template_name
 * @param array  $args          (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path  (default: '')
 *
 * @return void
 */
function makercamp_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = makercamp_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), MAKERCAMP_VERSION );
		return;
	}

	do_action( 'makercamp_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'makercamp_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 * @access public
 *
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path  (default: '')
 *
 * @return string
 */
function makercamp_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = MAKERCAMP_THEME_TEMPLATE_PATH;
	}

	if ( ! $default_path ) {
		$default_path = MAKERCAMP_INC . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'makercamp_locate_template', $template, $template_name, $template_path );
}

/**
 * Get template part (for templates like the single-camp_day).
 *
 * @access public
 *
 * @param mixed  $slug
 * @param string $name (default: '')
 *
 * @return void
 */
function makercamp_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/makercamp_templates/slug-name.php
	if ( $name ) {
		$template = locate_template( array(
			"{$slug}-{$name}.php",
			MAKERCAMP_THEME_TEMPLATE_PATH . "{$slug}-{$name}.php"
		) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( MAKERCAMP_INC . "templates/{$slug}-{$name}.php" ) ) {
		$template = MAKERCAMP_INC . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/makercamp_templates/slug.php
	if ( ! $template ) {
		$template = locate_template( array(
			"{$slug}.php",
			MAKERCAMP_THEME_TEMPLATE_PATH . "{$slug}.php"
		) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'makercamp_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, FALSE );
	}
}

/*************************************************
 * Template functions: Global things
 ************************************************/

if ( ! function_exists( 'makercamp_output_content_container' ) ) {

	/**
	 * Output the start of the page container.
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_content_container() {
		makercamp_get_template( 'general/container-start.php' );
	}
}

if ( ! function_exists( 'makercamp_output_content_container_end' ) ) {

	/**
	 * Output the end of the page container.
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_content_container_end() {
		makercamp_get_template( 'general/container-end.php' );
	}
}

if ( ! function_exists( 'makercamp_output_main_content' ) ) {

	/**
	 * Output main content template
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_main_content() {
		makercamp_get_template_part( 'content', 'single-camp_day' );
	}
}

if ( ! function_exists( 'makercamp_output_single_weeks' ) ) {

	/**
	 * Output single weeks template
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_single_weeks() {
		makercamp_get_template( 'single/camp_day/weeks.php' );
	}
}

if ( ! function_exists( 'makercamp_output_single_videos' ) ) {

	/**
	 * Output single videos template
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_single_videos() {
		makercamp_get_template( 'single/camp_day/videos.php' );
	}
}

if ( ! function_exists( 'makercamp_single_camp_day_content' ) ) {

	/**
	 * Output single content template
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_single_camp_day_content() {
		makercamp_get_template( 'single/camp_day/content.php' );
	}
}

if ( ! function_exists( 'makercamp_output_single_resources' ) ) {

	/**
	 * Output single resources template
	 *
	 * @access public
	 * @return void
	 */
	function makercamp_output_single_resources() {
		makercamp_get_template( 'single/camp_day/resources.php' );
	}
}