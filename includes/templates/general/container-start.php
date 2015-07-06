<?php
/**
 * Content containers
 *
 * @author        QuorStudio
 * @package       QuorStudio/Maker_Camp/Templates
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$template = get_option( 'template' );

switch ( $template ) {
	case 'twentyeleven' :
		echo '<div id="primary"><div id="content" role="main" class="makercamp">';
		break;
	case 'twentytwelve' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="makercamp">';
		break;
	case 'twentythirteen' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen makercamp">';
		break;
	case 'twentyfourteen' :
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen makercamp">';
		break;
	default :
		echo '<div id="container"><div id="content" role="main" class="makercamp">';
		break;
}