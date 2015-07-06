<?php
/**
 * List of weeks
 *
 * Override this template by copying it to yourtheme/makercamp-templates/single/camp_day/weeks.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Get all weeks and current week (for enabling correct current slide)
 */
$current_week = get_the_terms( get_the_ID(), 'week' );
$all_weeks    = get_terms( 'week' );
$i = 0; // Index for flexslider to know where to start

$current_week_day = get_post_meta( get_the_ID(), '_week_day', TRUE );
echo '<div class="flexslider"><ul class="taxonomy-weeks slides">';

/**
 * Loop through all weeks to build a slider
 */
foreach ( $all_weeks as $week ) {
	$posts_unlocked = get_posts(array(
		'post_type'   => 'camp_day',
		'meta_key' => '_lock_status',
		'meta_value' => 1,
		'showposts' => -1,
		'tax_query'   => array(
			array(
				'taxonomy'         => 'week',
				'field'            => 'id',
				'terms'            => $week->term_id,
				'include_children' => FALSE
			)
		)
	));

	if ( ! $posts_unlocked ) {
		continue;
	}

	$camp_days = get_posts( array(
		'post_type'   => 'camp_day',
		'numberposts' => -1,
		'order' => 'ASC',
		'meta_key' => '_week_day',
		'orderby'   => 'meta_value_num',
		'meta_query' => array(
			array(
				'key' => '_week_day',
				'value'=> array(1, 2, 3, 4, 5),
				'compare' => 'IN'
			)
		),
		'tax_query'   => array(
			array(
				'taxonomy'         => 'week',
				'field'            => 'id',
				'terms'            => $week->term_id,
				'include_children' => FALSE
			)
		)
	) );

	/**
	 * Week's cover image
	 */
	$week_cover = get_option( "week_image_{$week->term_id}" );
	$week_mobile_image = get_option( "week_mobile_image_{$week->term_id}" );

	/**
	 * Week's title
	 */
	$week_title = $week->name;

	/**
	 * Week's subtitle
	 */
	$week_subtitle = $week->description;

	echo '<li class="taxonomy-week" data-is-current="' . ( $current_week[0]->term_id == $week->term_id ) . '" data-index="' . $i . '" style="background: url(' . $week_cover . ') center no-repeat; background-size: cover;">';

	echo '<div class="container">';
	echo '<h2 class="week-title" data-title="' . $week_title . '">' . $week_title . ': ' . $week_subtitle . '</h2>';
	if ( $current_week[0]->term_id == $week->term_id ) {
		echo '<h3 class="day-title"><span class="label">' . sprintf( __( 'Day %d', 'makercamp' ), $current_week_day ) . '</span>: ' . get_the_title() . '</h3>';
	}
	echo $week_mobile_image ? '<img class="week-mobile-image" src="' . $week_mobile_image . '" alt="' . $week_title . '" />' : '';

	echo '<span class="list-label">' . __( 'Day', 'makercamp' ) . '</span>';
	echo '<ul class="camp_days">';

	/**
	 * Through all camp days of current week and iterate with them
	 */
	foreach ( $camp_days as $camp_day ) {

		/**
		 * Get day title
		 */
		$__title = get_the_title( $camp_day->ID );

		/**
		 * Get day to check if it's current day
		 */
		$__week_day = get_post_meta( $camp_day->ID, '_week_day', TRUE );

		/**
		 * Get day lock_status
		 */
		$__is_locked = get_post_meta( $camp_day->ID, '_lock_status', TRUE ) == 1 ? FALSE : TRUE;

		/**
		 * Get is_current day status
		 */
		$__is_current = get_post_meta( $camp_day->ID, '_is_current', TRUE );

		/**
		 * Get camp day url
		 */
		$__permalink = get_permalink( $camp_day->ID );

		echo '<li class="camp_day-number ' . ($current_week_day == $__week_day && $current_week[0]->term_id == $week->term_id ? 'opened-day' : ''). '">';

		if ( $__is_current ) {
			echo '<span class="icon-current"></span>';
		}

		if ( $__is_locked ) {
			echo '<span class="icon-locked"></span>';
		}

		echo '<a href="' . ( $__is_locked ? '#' : $__permalink ) . '">' . $__week_day . '</a>';

		echo '</li>';
	}

	echo '</ul>';

	if ( $current_week[0]->term_id == $week->term_id ) {
		the_content();
		echo '<a class="play-first-video-button" href="#">' . __( 'Play first video!', 'makercamp' ) . '</a>';
	}

	echo '</div>';
	echo '</li>';

	$i++;
}

echo '</ul></div>';