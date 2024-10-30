<?php

namespace KikoBot;

use WPMVC\Bridge;
/**
 * Main class.
 * Bridge between WordPress and App.
 * Class contains declaration of hooks and filters.
 *
 * @author 1000° Digital GmbH <https://www.1000grad.de>
 * @package kiko-wp-plugin2
 * @version 1.0.0
 */
class Main extends Bridge
{
    /**
     * Declaration of public WordPress hooks.
     */
    public function init()
    {
        $this->add_action('parse_request', 'IndexController@setApiKey');
        if(!is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php') {
            // embed chat [app, widget]
            $this->add_action('wp_print_scripts', 'IndexController@embedChat');
            // Shortcode "[kiko-wp-plugin-chat]" added. Controller as handler. embed chat [innerHtml]
            $this->add_shortcode('kiko-wp-plugin-chat', 'IndexController@shortCode');
        }
    }
    /**
     * Declaration of admin only WordPress hooks.
     * For WordPress admin dashboard.
     */
    public function on_admin()
    {
        $this->add_action('admin_menu', 'IndexController@addAdminPage', [__DIR__]);
    }
}