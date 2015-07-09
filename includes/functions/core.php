<?php
namespace QuorStudio\Maker_Camp\Core;

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

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );

	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts_enqueue' ) );
	add_action( 'wp_enqueue_scripts', $n( 'frontend_scripts_enqueue' ) );

	add_action( 'init', $n( 'create_post_types' ) );
	add_action( 'admin_menu', $n( 'remove_meta_boxes' ) );
	add_action( 'add_meta_boxes', $n( 'add_metabox' ) );
	add_action( 'save_post', $n( 'save_metabox_data' ) );

	/**
	 * Week taxonomy related functions
	 */
	add_action( 'wp_ajax_radio_tax_add_taxterm', $n( 'ajax_add_term' ) );
	add_action( 'week_term_edit_form_tag', $n( 'edit_form_tag' ) );
	add_action( 'week_edit_form_fields', $n( 'additional_week_fields' ), 10, 2 );
	add_action( 'edited_week', $n( 'save_week_fields' ) );

	do_action( 'makercamp_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @uses apply_filters()
 * @uses get_locale()
 * @uses load_textdomain()
 * @uses load_plugin_textdomain()
 * @uses plugin_basename()
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'makercamp' );
	load_textdomain( 'makercamp', WP_LANG_DIR . '/makercamp/makercamp-' . $locale . '.mo' );
	load_plugin_textdomain( 'makercamp', FALSE, plugin_basename( MAKERCAMP_PATH ) . '/languages/' );
}

/**
 * Loads admin javascript
 */
function admin_scripts_enqueue() {
	global $typenow;
	if ( $typenow == 'camp_day' ) {
		wp_enqueue_media();

		// Registers and enqueues the required javascript.
		wp_register_script( 'makercamp-admin', MAKERCAMP_URL . 'assets/js/maker-camp-admin.min.js', array( 'jquery' ), NULL, TRUE );
		wp_localize_script( 'makercamp-admin', 'vars',
			array(
				'uploader_title'          => __( 'Choose or Upload a File', 'makercamp' ),
				'uploader_button'         => __( 'Use this file', 'makercamp' ),
				'url_placeholder'         => __( 'Enter a url', 'makercamp' ),
				'url_placeholder_with_or' => __( 'Enter a url or...', 'makercamp' ),
				'title_placeholder'       => __( 'Enter readable title', 'makercamp' ),
				'link_delete'             => __( 'Remove', 'makercamp' ),
				'custom_taxonomy_slug'    => 'week'
			)
		);
		wp_enqueue_script( 'makercamp-admin' );
	}
}

/**
 * Loads frontend javascript and css
 */
function frontend_scripts_enqueue() {

	if ( is_single() && get_post_type() == 'camp_day' ) {


		// Registers and enqueues the required javascript.
		wp_enqueue_style( 'flexslider-css', MAKERCAMP_URL . 'assets/js/vendor/flexslider/flexslider.css' );
		wp_enqueue_script( 'flexslider-js', MAKERCAMP_URL . 'assets/js/vendor/flexslider/jquery.flexslider-min.js', array( 'jquery' ), NULL, TRUE );

		wp_enqueue_style( 'fancybox-css', MAKERCAMP_URL . 'assets/js/vendor/fancybox/source/jquery.fancybox.css' );
		wp_enqueue_script( 'fancybox-js', MAKERCAMP_URL . 'assets/js/vendor/fancybox/source/jquery.fancybox.pack.js', array( 'jquery' ), NULL, TRUE );

		wp_enqueue_style( 'makercamp-css', MAKERCAMP_URL . 'assets/css/maker-camp.min.css' );
		wp_enqueue_script( 'makercamp-frontend', MAKERCAMP_URL . 'assets/js/maker-camp.min.js', array(
			'jquery',
			'fancybox-js',
			'flexslider-js'
		), NULL, TRUE );
		wp_localize_script( 'makercamp-frontend', 'vars',
			array(
				'ajax_url'   => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce( 'update-camp_day' ),
				'day_label' => __( 'Day', 'makercamp' )
			)
		);
	}
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @uses do_action()
 *
 * @return void
 */
function init() {
	do_action( 'makercamp_init' );
}

/**
 * Register a camp day post type.
 *
 * @uses register_post_type()
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 *
 * @return void
 */
function create_post_types() {

	/**
	 * Register Camp day post type
	 */
	$labels = array(
		'name'               => _x( 'Camp Days', 'post type general name', 'makercamp' ),
		'singular_name'      => _x( 'Camp Day', 'post type singular name', 'makercamp' ),
		'menu_name'          => _x( 'Camp Days', 'admin menu', 'makercamp' ),
		'name_admin_bar'     => _x( 'Camp Day', 'add new on admin bar', 'makercamp' ),
		'add_new'            => _x( 'Add New', 'Camp Day', 'makercamp' ),
		'add_new_item'       => __( 'Add New Camp Day', 'makercamp' ),
		'new_item'           => __( 'New Camp Day', 'makercamp' ),
		'edit_item'          => __( 'Edit Camp Day', 'makercamp' ),
		'view_item'          => __( 'View Camp Day', 'makercamp' ),
		'all_items'          => __( 'All Camp Days', 'makercamp' ),
		'search_items'       => __( 'Search Camp Days', 'makercamp' ),
		'parent_item_colon'  => __( 'Parent Camp Days:', 'makercamp' ),
		'not_found'          => __( 'No Camp Days found.', 'makercamp' ),
		'not_found_in_trash' => __( 'No Camp Days found in Trash.', 'makercamp' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => TRUE,
		'publicly_queryable' => TRUE,
		'show_ui'            => TRUE,
		'show_in_menu'       => TRUE,
		'query_var'          => TRUE,
		'rewrite'            => array( 'slug' => '' ),
		'capability_type'    => 'post',
		'has_archive'        => FALSE,
		'hierarchical'       => FALSE,
		'menu_position'      => NULL,
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'camp_day', $args );

	/**
	 * Add new taxonomy 'weeks, make it hierarchical (like categories)
	 */
	$labels = array(
		'name'              => _x( 'Weeks', 'taxonomy general name', 'makercamp' ),
		'singular_name'     => _x( 'Week', 'taxonomy singular name', 'makercamp' ),
		'search_items'      => __( 'Search Weeks', 'makercamp' ),
		'all_items'         => __( 'All Weeks', 'makercamp' ),
		'parent_item'       => __( 'Parent Week', 'makercamp' ),
		'parent_item_colon' => __( 'Parent Week:', 'makercamp' ),
		'edit_item'         => __( 'Edit Week', 'makercamp' ),
		'update_item'       => __( 'Update Week', 'makercamp' ),
		'add_new_item'      => __( 'Add New Week', 'makercamp' ),
		'new_item_name'     => __( 'New Week Name', 'makercamp' ),
		'menu_name'         => __( 'Weeks', 'makercamp' )
	);

	$args = array(
		'hierarchical'      => TRUE,
		'labels'            => $labels,
		'show_ui'           => TRUE,
		'show_admin_column' => TRUE,
		'query_var'         => TRUE,
		'rewrite'           => array( 'slug' => 'week' ),
	);

	register_taxonomy( 'week', array( 'camp_day' ), $args );
}

/**
 * Remove old taxonomy meta box
 */
function remove_meta_boxes() {
	remove_meta_box( 'weekdiv', 'camp_day', 'normal' );
}

/**
 * Register a camp day post type.
 *
 * @uses add_meta_box()
 * @link https://codex.wordpress.org/Function_Reference/add_meta_box
 *
 * @return void
 */
function add_metabox() {
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_meta_box(
		'makercamp_day_settings',
		__( 'Maker Camp Day Settings', 'makercamp' ),
		$n( 'makercamp_day_meta_box_callback' ),
		'camp_day'
	);

	add_meta_box(
		'weeks_taxonomy',
		__( 'Weeks', 'makercamp' ),
		$n( 'makercamp_weeks_meta_box_callback' ),
		'camp_day',
		'side', 'core' );

}

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function makercamp_day_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'makercamp_day_meta_box', 'makercamp_day_meta_box_nonce' );


	/*
	 * Week day select box
	 */
	$week_day      = get_post_meta( $post->ID, '_week_day', TRUE );
	$options_array = array(
		'1' => __( 'Day 1', 'makercamp' ),
		'2' => __( 'Day 2', 'makercamp' ),
		'3' => __( 'Day 3', 'makercamp' ),
		'4' => __( 'Day 4', 'makercamp' ),
		'5' => __( 'Day 5', 'makercamp' )
	);
	$options       = '<option value="">' . __( 'Choose an option', 'makercamp' ) . '</option>';

	foreach ( $options_array as $key => $value ) {
		$options .= '<option value="' . esc_attr( $key ) . '" ' . selected( $week_day, $key, FALSE ) . '>' . esc_html( $value ) . '</option>';
	}

	echo '<h3>' . __( 'General Settings', 'makercamp' ) . '</h3>';

	echo '<p>';
	echo '<label for="makercamp_week_day">';
	_e( 'Week day', 'makercamp' );
	echo '</label> ';
	echo '<select id="makercamp_week_day" name="makercamp_week_day">' . $options . '</select><br />';
	echo '<span class="description">' . __( 'If you already have same day assigned for another week, it will be unassigned from that day.', 'makercamp' ) . '</span>';
	echo '</p>';

	/**
	 * Day Lock Status
	 */
	$lock_status = get_post_meta( $post->ID, '_lock_status', TRUE );

	echo '<p>';
	echo '<input type="checkbox" id="makercamp_lock_status" name="makercamp_lock_status" ' . checked( $lock_status, TRUE, FALSE ) . ' />';
	echo '<label for="makercamp_lock_status">';
	_e( 'Check to unlock for public', 'makercamp' );
	echo '</label>';
	echo '</p>';

	/**
	 * Current day checkbox
	 */
	$is_current = get_post_meta( $post->ID, '_is_current', TRUE );

	echo '<p>';
	echo '<input type="checkbox" id="makercamp_is_current" name="makercamp_is_current" ' . checked( $is_current, TRUE, FALSE ) . ' />';
	echo '<label for="makercamp_is_current">';
	_e( 'Check to mark this day as current (only for unlocked days)', 'makercamp' );
	echo '</label><br />';
	echo '<span class="description">' . __( 'If you already have current day assigned, it will be unassigned from that day.', 'makercamp' ) . '</span>';
	echo '</p>';

	/**
	 * Youtube sources
	 */
	$youtube_links = get_post_meta( $post->ID, '_youtube_links', TRUE );

	for ( $i = 0; $i < 3; $i++ ) {
		$index = $i + 1;

		echo '<h3>' . sprintf( __( 'YouTube source #%d', 'makercamp' ), $index ) . '</h3>';

		echo '<p>';
		echo '<label for="makercamp_youtube_source_' . $index . '_url">';
		_e( 'URL', 'makercamp' );
		echo '</label> ';
		echo '<input type="text" id="makercamp_youtube_source_' . $index . '_url" name="makercamp_youtube_link[' . $i . '][url]" value="' . ( ! empty( $youtube_links[ $i ][ 'url' ] ) ? esc_attr( $youtube_links[ $i ][ 'url' ] ) : '' ) . '" size="25" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="makercamp_youtube_source_' . $index . '_title">';
		_e( 'Title', 'makercamp' );
		echo '</label> ';
		echo '<input type="text" id="makercamp_youtube_source_' . $index . '_title" name="makercamp_youtube_link[' . $i . '][title]" value="' . ( ! empty( $youtube_links[ $i ][ 'title' ] ) ? esc_attr( $youtube_links[ $i ][ 'title' ] ) : '' ) . '" size="25" />';
		echo '</p>';

		echo '<p>';
		echo '<textarea id="makercamp_youtube_source_' . $index . '_description" placeholder="' . __( 'Description', 'makercamp' ) . '" name="makercamp_youtube_link[' . $i . '][description]" rows="4" cols="50">';
		echo ! empty( $youtube_links[ $i ][ 'description' ] ) ? esc_attr( $youtube_links[ $i ][ 'description' ] ) : '';
		echo '</textarea>';
		echo '</p>';
	}

	echo '<h3>' . __( 'Camp Day Materials', 'makercamp' ) . '</h3>';

	/**
	 * Materials v2.0
	 */
	$materials = get_post_meta( $post->ID, '_materials', TRUE );

	if ( $materials ) {
		echo '<ul class="materials">';
		foreach ( $materials as $index => $value ) {

			echo '<li data-index="' . $index . '">';
			echo '<label for="makercamp_material_' . $index . '"_url>';
			printf( __( 'Material %d', 'makercamp' ), $index + 1 );
			echo '</label> ';
			echo '<input type="text" name="makercamp_materials[' . $index . '][title]" placeholder="' . __( 'Enter readable title', 'makercamp' ) . '" value="' . ( ! empty( $materials[ $index ][ 'title' ] ) ? esc_attr( $materials[ $index ][ 'title' ] ) : '' ) . '" size="25" />';
			echo '<input type="text" class="makercamp-file-uploaded" id="makercamp_material_' . $index . '_url" name="makercamp_materials[' . $index . '][url]" placeholder="' . __( 'Enter a url or...', 'makercamp' ) . '" value="' . ( ! empty( $materials[ $index ][ 'url' ] ) ? esc_attr( $materials[ $index ][ 'url' ] ) : '' ) . '" size="25" />';
			echo '<input type="button" class="button makercamp-file-uploader" value="' . __( 'Choose or Upload a File', 'makercamp' ) . '" />';
			echo '<a class="makercamp-material-delete" href="#">' . __( 'Remove', 'makercamp' ) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	echo '<p>';
	echo '<a class="makercamp-add-material" href="#">' . __( 'Add new material', 'makercamp' ) . '</a>';
	echo '</p>';

	echo '<ul class="new-materials"></ul>';

	echo '<h3>' . __( 'Other projects', 'makercamp' ) . '</h3>';

	/**
	 * Other projects
	 */
	$project_links = get_post_meta( $post->ID, '_project_links', TRUE );

	if ( $project_links ) {
		echo '<ul class="project-sources">';
		foreach ( $project_links as $index => $value ) {

			echo '<li data-index="' . $index . '">';
			echo '<label for="makercamp_project_source_' . $index . '"_url>';
			printf( __( 'Project source %d', 'makercamp' ), $index + 1 );
			echo '</label> ';
			echo '<input type="text" name="makercamp_project_source[' . $index . '][title]" placeholder="' . __( 'Enter readable title', 'makercamp' ) . '" value="' . ( ! empty( $project_links[ $index ][ 'title' ] ) ? esc_attr( $project_links[ $index ][ 'title' ] ) : '' ) . '" size="25" />';
			echo '<input type="text" id="makercamp_project_source_' . $index . '_url" name="makercamp_project_source[' . $index . '][url]" placeholder="' . __( 'Enter a url', 'makercamp' ) . '" value="' . ( ! empty( $project_links[ $index ][ 'url' ] ) ? esc_attr( $project_links[ $index ][ 'url' ] ) : '' ) . '" size="25" />';
			echo '<a class="makercamp-project-delete" href="#">' . __( 'Remove', 'makercamp' ) . '</a>';
			echo '</li>';
		}
		echo '</ul>';
	}

	echo '<p>';
	echo '<a class="makercamp-add-project" href="#">' . __( 'Add new project link', 'makercamp' ) . '</a>';
	echo '</p>';

	echo '<ul class="new-projects"></ul>';
}


/**
 * When the camp day is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_metabox_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST[ 'makercamp_day_meta_box_nonce' ] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST[ 'makercamp_day_meta_box_nonce' ], 'makercamp_day_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST[ 'post_type' ] ) && 'camp_day' == $_POST[ 'post_type' ] ) {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

	}
	else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */

	// Sanitize user inputs.
	$week_day      = sanitize_text_field( $_POST[ 'makercamp_week_day' ] );
	$lock_status   = isset( $_POST[ 'makercamp_lock_status' ] );
	$is_current    = isset( $_POST[ 'makercamp_is_current' ] );
	$youtube_links = '';
	if ( is_array( $_POST[ 'makercamp_youtube_link' ] ) ) {
		foreach ( $_POST[ 'makercamp_youtube_link' ] as $index => $value ) {
			$youtube_links[ $index ][ 'url' ]         = esc_url_raw( $value[ 'url' ] );
			$youtube_links[ $index ][ 'title' ]       = sanitize_text_field( $value[ 'title' ] );
			$youtube_links[ $index ][ 'description' ] = esc_textarea( $value[ 'description' ] );
		}
	}
	$materials_pdf[ 'url' ]     = isset( $_POST[ 'materials-pdf' ][ 'url' ] ) ? esc_url_raw( $_POST[ 'materials-pdf' ][ 'url' ] ) : '';
	$materials_pdf[ 'title' ]   = isset( $_POST[ 'materials-pdf' ][ 'title' ] ) ? sanitize_text_field( $_POST[ 'materials-pdf' ][ 'title' ] ) : '';
	$materials_2_pdf[ 'url' ]   = isset( $_POST[ 'materials-2-pdf' ][ 'url' ] ) ? esc_url_raw( $_POST[ 'materials-2-pdf' ][ 'url' ] ) : '';
	$materials_2_pdf[ 'title' ] = isset( $_POST[ 'materials-2-pdf' ][ 'title' ] ) ? sanitize_text_field( $_POST[ 'materials-2-pdf' ][ 'title' ] ) : '';
	$materials_3_pdf[ 'url' ]   = isset( $_POST[ 'materials-3-pdf' ][ 'url' ] ) ? esc_url_raw( $_POST[ 'materials-3-pdf' ][ 'url' ] ) : '';
	$materials_3_pdf[ 'title' ] = isset( $_POST[ 'materials-3-pdf' ][ 'title' ] ) ? sanitize_text_field( $_POST[ 'materials-3-pdf' ][ 'title' ] ) : '';
	$materials                  = '';
	if ( is_array( $_POST[ 'makercamp_materials' ] ) ) {
		foreach ( $_POST[ 'makercamp_materials' ] as $index => $value ) {
			$temp_array            = array();
			$temp_array[ 'url' ]   = esc_url_raw( $value[ 'url' ] );
			$temp_array[ 'title' ] = sanitize_text_field( $value[ 'title' ] );

			$materials[ ] = $temp_array;
		}
	}
	$project_links = '';
	if ( is_array( $_POST[ 'makercamp_project_source' ] ) ) {
		foreach ( $_POST[ 'makercamp_project_source' ] as $index => $value ) {
			$temp_array            = array();
			$temp_array[ 'url' ]   = esc_url_raw( $value[ 'url' ] );
			$temp_array[ 'title' ] = sanitize_text_field( $value[ 'title' ] );

			$project_links[ ] = $temp_array;
		}
	}

	/**
	 * Check if we already has a week day assigned and remove that day from other camp_day posts
	 */
	$term_id = ! empty( $_POST[ 'tax_input' ] ) ? $_POST[ 'tax_input' ][ 'week' ] : '';
	if ( $term_id ) {
		$posts = get_posts(
			array(
				'post_type'      => 'camp_day',
				'posts_per_page' => -1,
				'post__not_in'   => array( $post_id ),
				'meta_key'       => '_week_day',
				'meta_value'     => $week_day,
				'tax_query'      => array(
					array(
						'taxonomy' => 'week',
						'field'    => 'id',
						'terms'    => $term_id
					)
				)
			)
		);
		foreach ( $posts as $__post ) {
			delete_post_meta( $__post->ID, '_week_day' );
		}
	}

	/**
	 * Check if we already have current day enabled and we have this day unlocked
	 */
	if ( $is_current && $lock_status ) {
		$posts = get_posts(
			array(
				'post_type'      => 'camp_day',
				'post__not_in'   => array( $post_id ),
				'posts_per_page' => -1,
				'meta_key'       => '_is_current',
				'meta_value'     => 1
			)
		);
		foreach ( $posts as $__post ) {
			delete_post_meta( $__post->ID, '_is_current' );
		}

		update_post_meta( $post_id, '_is_current', $is_current );
	}

	if ( ! $lock_status ) {
		delete_post_meta( $post_id, '_is_current' );
	}

	/**
	 * Update meta fields in the database.
	 */
	update_post_meta( $post_id, '_week_day', $week_day );
	update_post_meta( $post_id, '_lock_status', $lock_status );
	update_post_meta( $post_id, '_youtube_links', $youtube_links );
	update_post_meta( $post_id, '_materials_pdf', $materials_pdf );
	update_post_meta( $post_id, '_materials_2_pdf', $materials_2_pdf );
	update_post_meta( $post_id, '_materials_3_pdf', $materials_3_pdf );
	update_post_meta( $post_id, '_project_links', $project_links );
	update_post_meta( $post_id, '_materials', $materials );
}

/**
 * Prints the box content (week taxonomy with radio buttons).
 *
 * @param WP_Post $post The object for the current post/page.
 */
function makercamp_weeks_meta_box_callback( $post ) {

	$taxonomy = 'week';

	//Set up the taxonomy object and get terms
	$tax   = get_taxonomy( $taxonomy );
	$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );

	//Name of the form
	$name = 'tax_input[' . $taxonomy . ']';

	//Get current and popular terms
	$popular   = get_terms( $taxonomy, array(
		'orderby'      => 'count',
		'order'        => 'DESC',
		'number'       => 10,
		'hierarchical' => FALSE
	) );
	$postterms = get_the_terms( $post->ID, $taxonomy );
	$current   = ( $postterms ? array_pop( $postterms ) : FALSE );
	$current   = ( $current ? $current->term_id : 0 );
	?>

	<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
		<!-- Display tabs-->
		<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
			<li class="tabs">
				<a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a>
			</li>
			<li class="hide-if-no-js">
				<a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a>
			</li>
		</ul>

		<!-- Display taxonomy terms -->
		<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
			<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy ?> categorychecklist form-no-clear">
				<?php foreach ( $terms as $term ) {
					$id    = $taxonomy . '-' . $term->term_id;
					$value = ( is_taxonomy_hierarchical( $taxonomy ) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'" );
					echo "<li id='$id'><label class='selectit'>";
					echo "<input type='radio' id='in-$id' name='{$name}'" . checked( $current, $term->term_id, FALSE ) . " {$value} />$term->name<br />";
					echo "</label></li>";
				} ?>
			</ul>
		</div>

		<!-- Display popular taxonomy terms -->
		<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear">
				<?php foreach ( $popular as $term ) {
					$id    = 'popular-' . $taxonomy . '-' . $term->term_id;
					$value = ( is_taxonomy_hierarchical( $taxonomy ) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'" );
					echo "<li id='$id'><label class='selectit'>";
					echo "<input type='radio' id='in-$id'" . checked( $current, $term->term_id, FALSE ) . " {$value} />$term->name<br />";
					echo "</label></li>";
				} ?>
			</ul>
		</div>

		<p id="<?php echo $taxonomy; ?>-add" class="">
			<label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
			<input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true" />
			<input type="button" id="" class="radio-tax-add button" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
			<?php wp_nonce_field( 'radio-tax-add-' . $taxonomy, '_wpnonce_radio-add-tag', FALSE ); ?>
		</p>
	</div>
<?php
}

/**
 * Save new taxonomy terms via ajax
 */
function ajax_add_term() {
	$taxonomy = ! empty( $_POST[ 'taxonomy' ] ) ? $_POST[ 'taxonomy' ] : '';
	$term     = ! empty( $_POST[ 'term' ] ) ? $_POST[ 'term' ] : '';
	$tax      = get_taxonomy( $taxonomy );
	check_ajax_referer( 'radio-tax-add-' . $taxonomy, '_wpnonce_radio-add-tag' );
	if ( ! $tax || empty( $term ) ) {
		exit();
	}
	if ( ! current_user_can( $tax->cap->edit_terms ) ) {
		die( '-1' );
	}
	$tag = wp_insert_term( $term, $taxonomy );
	if ( ! $tag || is_wp_error( $tag ) || ( ! $tag = get_term( $tag[ 'term_id' ], $taxonomy ) ) ) {
		//TODO Error handling
		exit();
	}

	$id    = $taxonomy . '-' . $tag->term_id;
	$name  = 'tax_input[' . $taxonomy . ']';
	$value = ( is_taxonomy_hierarchical( $taxonomy ) ? "value='{$tag->term_id}'" : "value='{$term->tag_slug}'" );
	$html  = '<li id="' . $id . '"><label class="selectit"><input type="radio" id="in-' . $id . '" name="' . $name . '" ' . $value . ' />' . $tag->name . '</label></li>';

	echo json_encode( array( 'term' => $tag->term_id, 'html' => $html ) );
	exit();
}

/**
 * Add multipart upload for week taxonomy
 */
function edit_form_tag() {
	echo ' enctype="multipart/form-data"';
}

/**
 * Add New Field To Week Taxonomy
 *
 * @param $term
 * @param $tax
 */
function additional_week_fields( $term, $tax ) {
	$category_image_url        = get_option( "{$tax}_image_{$term->term_id}" );            // Retrieve our Attachment ID from the Options Database Table
	$category_mobile_image_url = get_option( "{$tax}_mobile_image_{$term->term_id}" );            // Retrieve our Attachment ID from the Options Database Table
	$long_description          = get_option( "{$tax}_long_description_{$term->term_id}" );            // Retrieve our Attachment ID from the Options Database Table
	$animation_coords          = get_option( "{$tax}_anim_coords_{$term->term_id}" );            // Retrieve our Attachment ID from the Options Database Table
	?>
	<!-- Create a nonce to validate against -->
	<input type="hidden" name="upload_meta_nonce" value="<?php echo wp_create_nonce( 'upload-week-data' ); ?>" />

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="makercamp-long-description"><?php _e( 'Long description', 'makercamp' ); ?></label>
		</th>
		<td>
			<textarea id="makercamp-long-description" name="week-long-description"><?php echo esc_textarea( $long_description ); ?></textarea>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="meta-order"><?php _e( 'Category Image' ); ?></label>
		</th>
		<td>
			<div id="catImage">

				<!-- Define our actual upload field -->
				<input type="text" name="week-image" class="makercamp-file-uploaded" value="<?php echo esc_attr( $category_image_url ); ?>" />
				<input type="button" class="button makercamp-file-uploader makercamp-image" value="<?php _e( 'Choose or Upload a File', 'makercamp' ); ?>" />

			</div>
			<span class="description"><?php _e( 'Upload an appropriate image.' ); ?></span>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="meta-order"><?php _e( 'Category Mobile Image' ); ?></label>
		</th>
		<td>
			<div id="catMobileImage">

				<!-- Define our actual upload field -->
				<input type="text" name="week-mobile-image" class="makercamp-file-uploaded" value="<?php echo esc_attr( $category_mobile_image_url ); ?>" />
				<input type="button" class="button makercamp-file-uploader makercamp-image" value="<?php _e( 'Choose or Upload a File', 'makercamp' ); ?>" />

			</div>
			<span class="description"><?php _e( 'Upload an appropriate image.' ); ?></span>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="meta-order"><?php _e( 'Homepage Animation Coordinates' ); ?></label>
		</th>
		<td>
			<!-- Define our actual upload field -->
			<input type="text" name="week-animation-coords" value="<?php echo esc_attr( $animation_coords ); ?>" />

			<br />
			<span class="description"><?php _e( 'Please provide coordinates for homepage interective area animation' ); ?></span>
		</td>
	</tr>
<?php
}

/**
 * Save Week Meta
 *
 * @param $term_id
 */
function save_week_fields( $term_id ) {

	// Make sure that the nonce is set, taxonomy is set, and that our uploaded file is not empty
	if (
		isset( $_POST[ 'upload_meta_nonce' ] ) && wp_verify_nonce( $_POST[ 'upload_meta_nonce' ], 'upload-week-data' ) && isset( $_POST[ 'taxonomy' ] )
	) {
		$tax = $_POST[ 'taxonomy' ];                                                   // Store our taxonomy, used for the option naming convention

		if ( isset( $_POST[ 'week-long-description' ] ) ) {
			$long_description = esc_html( $_POST[ 'week-long-description' ] );

			// Update our category image
			update_option( "{$tax}_long_description_{$term_id}", $long_description );
		}

		if ( isset( $_POST[ 'week-image' ] ) ) {
			// Verify that the url given is what we're expecting
			$category_img = esc_url_raw( $_POST[ 'week-image' ] );

			// Update our category image
			update_option( "{$tax}_image_{$term_id}", $category_img );
		}

		if ( isset( $_POST[ 'week-mobile-image' ] ) ) {
			// Verify that the url given is what we're expecting
			$category_mobile_img = esc_url_raw( $_POST[ 'week-mobile-image' ] );

			// Update our category image
			update_option( "{$tax}_mobile_image_{$term_id}", $category_mobile_img );
		}

		if ( isset( $_POST[ 'week-animation-coords' ] ) ) {
			// Verify that the url given is what we're expecting
			$animation_coords = esc_html( $_POST[ 'week-animation-coords' ] );

			// Update our category image
			update_option( "{$tax}_anim_coords_{$term_id}", $animation_coords );
		}

	}
}

/**
 * Activate the plugin
 *
 * @uses init()
 * @uses flush_rewrite_rules()
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}
