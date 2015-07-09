<?php
/**
 * Content
 *
 * Override this template by copying it to yourtheme/makercamp-templates/single/camp_day/content.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $post;
?>

<section class="content-wrapper">
	<article id="camp_day-<?php the_ID(); ?>" <?php post_class( 'container' ); ?>>

		<div class="entry-content">
			<?php setup_postdata( $post );
			the_content();
			wp_reset_postdata( $post ); ?>
		</div>

		<?php
		/**
		 * makercamp_single_camp_day_content_inner hook
		 */
		do_action( 'makercamp_single_camp_day_content_inner' );
		?>

	</article>
</section>