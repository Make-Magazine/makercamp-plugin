<?php
/**
 * Plugin Name: Maker Camp
 * Plugin URI:  http://quorstudio.com
 * Description: Maker Camp extension that allows have locked/unlocked content for campers
 * Version:     0.1.0
 * Author:      Alex T (Quorstudio)
 * Author URI:  http://quorstudio.com
 * License:     GPLv2+
 * Text Domain: makercamp
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 10up (email : info@10up.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using yo wp-make:plugin
 * Copyright (c) 2015 10up, LLC
 * https://github.com/10up/generator-wp-make
 */

// Useful global constants
define( 'MAKERCAMP_VERSION', '0.1.0' );
define( 'MAKERCAMP_URL',     plugin_dir_url( __FILE__ ) );
define( 'MAKERCAMP_PATH',    dirname( __FILE__ ) . '/' );
define( 'MAKERCAMP_THEME_TEMPLATE_PATH', 'makercamp-templates/' );
define( 'MAKERCAMP_INC',     MAKERCAMP_PATH . 'includes/' );

// Include files
require_once MAKERCAMP_INC . 'functions/core.php';
require_once MAKERCAMP_INC . 'functions/template-functions.php';


// Activation/Deactivation
register_activation_hook( __FILE__, '\QuorStudio\Maker_Camp\Core\activate' );
register_deactivation_hook( __FILE__, '\QuorStudio\Maker_Camp\Core\deactivate' );

// Bootstrap
QuorStudio\Maker_Camp\Core\setup();
QuorStudio\Maker_Camp\TemplateFunctions\setup();