<?php
/**
 * List of videos
 *
 * Override this template by copying it to yourtheme/makercamp-templates/single/camp_day/videos.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$youtube_links = get_post_meta( get_the_ID(), '_youtube_links', TRUE ); ?>

<section class="daily-camp-videos-wrapper">
	<article <?php post_class(); ?>>
		<ul class="dayly-camp-videos container">
			<?php foreach ( $youtube_links as $key => $array ) {
				echo '<li class="col-md-4">';
				if ( ! empty( $array[ 'url' ] ) ) {
					$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:(?:&list=)?([\w-]*))(?:.+)?$#x';
					if ( preg_match( $pattern, $array[ 'url' ], $match ) ) {
						$video_id = $match[ 1 ];
						$playlist = '';
						if ( isset( $match[ 2 ] ) && ! empty( $match[ 2 ] ) ) {
							$playlist = '&loop=1&listType=playlist&list=' . $match[ 2 ];
						}
						echo '<a class="fancybox" data-fancybox-type="iframe" href="https://www.youtube.com/embed/' . $video_id . '?autoplay=1' . $playlist . '"><img src="http://img.youtube.com/vi/' . $video_id . '/0.jpg" alt="Camp video"><span class="fancybox-video-play-button"></span></a>';
					}
				}
				echo ! empty( $array[ 'title' ] ) ? '<h3>' . $array[ 'title' ] . '</h3>' : '';
				echo ! empty( $array[ 'description' ] ) ? '<p>' . esc_html( $array[ 'description' ] ) . '</p>' : '';
				echo '</li>';
			} ?>
		</ul>
	</article>
</section>