<?php
/**
 * The Template for displaying single camp day.
 *
 * Override this template by copying it to yourtheme/makercamp-templates/single-camp_day.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

get_header( 'makercamp' ); ?>

<?php
/**
 * makercamp_before_main_content hook
 *
 * @see makercamp_output_content_container - 10
 */
do_action( 'makercamp_before_main_content' );
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php do_action('makercamp_main_content_template'); ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * makercamp_after_main_content hook
 *
 * @see makercamp_output_content_container_end - 10
 */
do_action( 'makercamp_after_main_content' );
?>

<?php
/**
 * makercamp_sidebar hook
 *
 * @see makercamp_get_sidebar - 10
 */
do_action( 'makercamp_sidebar' );
?>

<?php get_footer( 'makercamp' ); ?>