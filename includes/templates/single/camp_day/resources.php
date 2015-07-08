<?php
/**
 * Content
 *
 * Override this template by copying it to yourtheme/makercamp-templates/single/camp_day/resources.php
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


$materials = get_post_meta( get_the_ID(), '_materials', TRUE );
$project_sources = get_post_meta( get_the_ID(), '_project_links', TRUE );

?>

<section class="camp-resources-wrapper">
	<article <?php post_class(); ?>>
		<ul class="camp-resources container">

			<?php if ($materials) : ?>

			<li>
				<img src="<?php echo MAKERCAMP_URL . 'images/src/crafts.png'; ?>" alt="<?php _e( 'List of Materials', 'makercamp' ); ?>">

				<h3><?php _e( 'Recommended Projects', 'makercamp' ); ?></h3>

				<ul class="materials">
					<?php foreach ( $materials as $material ) { ?>
						<li>
							<a href="<?php echo esc_url( $material[ 'url' ] ) ?>" class="read-more" target="_blank"><?php echo esc_html( $material[ 'title' ] ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>

			<?php endif; ?>

			<?php if ($project_sources) : ?>

			<li>
				<img src="<?php echo MAKERCAMP_URL . 'images/src/cap.png'; ?>" alt="<?php _e( 'Other project', 'makercamp' ); ?>">

				<h3><?php _e( 'Additional Suggested Projects', 'makercamp' ); ?></h3>

				<ul class="other-projects">
					<?php foreach ( $project_sources as $source ) { ?>
						<li>
							<a href="<?php echo esc_url( $source[ 'url' ] ) ?>" class="read-more" target="_blank"><?php echo esc_html( $source[ 'title' ] ); ?></a>
						</li>
					<?php } ?>
				</ul>
			</li>

			<?php endif; ?>
		</ul>
	</article>
</section>