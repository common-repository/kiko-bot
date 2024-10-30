<?php
/*
Plugin Name: Kiko Chatbot
Plugin URI: https://www.kiko.bot
Description: Kiko Chatbot Integration
Version: 1.0.3
Author: 1000° DIGITAL GmbH
Author URI: https://www.1000grad.de
License: MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: kiko-wp-plugin
Domain Path: /assets/lang
Requires PHP: 5.4
*/
//------------------------------------------------------------
//
// NOTE:
//
// Try NOT to add any code line in this file.
//
// Use "app\Main.php" to add your hooks.
//
//------------------------------------------------------------
defined('KIKO_BOT_WP_PLUGIN_BASE_URL')
|| define('KIKO_BOT_WP_PLUGIN_BASE_URL', plugin_dir_url( __FILE__ ));
require_once( __DIR__ . '/app/Boot/bootstrap.php' );