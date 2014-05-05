<?php
/**
 * Plugin Name: Better HipChat
 * Plugin URI: https://github.com/gedex/wp-better-hipchat/
 * Description: This plugin allows you to send notifications to HipChat rooms when certain events in WordPress occur.
 * Version: 0.1.0
 * Author: Akeda Bagus
 * Author URI: http://gedex.web.id
 * Text Domain: better-hipchat
 * Domain Path: /languages
 * License: GPL v2 or later
 * Requires at least: 3.6
 * Tested up to: 3.9
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

require_once __DIR__ . '/includes/autoloader.php';

// Register the autoloader.
WP_Better_HipChat_Autoloader::register( 'WP_Better_HipChat', trailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );

// Runs this plugin.
$GLOBALS['better_hipchat'] = new WP_Better_HipChat_Plugin();
$GLOBALS['better_hipchat']->run( __FILE__ );
