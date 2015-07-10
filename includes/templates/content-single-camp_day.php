<?php
/**
 * The template for displaying camp day content in the single-camp_day.php template
 *
 * Override this template by copying it to yourtheme/makercamp-templates/content-single-camp_day.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php

if ( post_password_required() ) {
	echo get_the_password_form();

	return;
}

$is_unlocked = get_post_meta( get_the_ID(), '_lock_status', TRUE );

/**
 * Check if week is_locked and redirect if true
 */
if ( ! $is_unlocked ) {
	// TODO: redirect to last open day of the week
} ?>

<?php
/**
 * makercamp_before_single_camp_day hook
 *
 * @see makercamp_output_single_weeks - 10
 */
do_action( 'makercamp_before_single_camp_day' );
?>

<div class="day-wrapper">

	<?php
	/**
	 * makercamp_single_camp_day_content hook
	 *
	 * @see makercamp_output_single_videos - 10
	 * @see makercamp_single_camp_day_content - 20
	 */
	do_action( 'makercamp_single_camp_day_content' );
	?>

	<?php
	/**
	 * makercamp_after_single_camp_day hook
	 *
	 * @see makercamp_output_single_resources - 10
	 */
	do_action( 'makercamp_after_single_camp_day' ); ?>

</div>