<?php
/**
 * Plugin Name:       Disable Search
 * Plugin URI:        https://letowp.de/
 * Description:       Disable the search capabilities of WordPress.
 * Version:           1.0.1
 * Author:            Thomas Lellesch
 * Requires at least: 6.0.0
 * Requires PHP:      8.0.0
 * Tested up to:      6.2.0
 * Author URI:        https://letowp.de/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leto-disable-search
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if(file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

class Leto_Disable_Search
{
    /**
     * The single instance of the class.
     *
     * @var Leto_Disable_Search|null
     */
    private static ?Leto_Disable_Search $instance = null;

    /**
     * The absolute path to the plugin directory.
     *
     * @var string
     */
    private string $plugin_path;

    /**
     * The URL of the plugin directory.
     *
     * @var string
     */
    private string $plugin_url;

    /**
     * The text domain for localization.
     *
     * @var string
     */
    const TEXT_DOMAIN = 'leto-disable-search';

    /**
     * The version of this plugin.
     *
     * @var string
     */
    const PLUGIN_VERSION = '1.0.1';

    /**
     * Protected constructor to prevent creating a new instance of the Singleton
     * via the 'new' operator from outside of this class.
     */
    protected function __construct()
    {


        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);

        // Initialize the plugin
        $this->init();
    }

    /**
     * Prevent instance cloning.
     */
    private function __clone()
    {
    }

    /**
     * Prevent instance unserialization.
     */
    public function __wakeup()
    {
    }

    /**
     * Returns the Singleton instance of this class.
     *
     * @return Leto_Disable_Search|null The Singleton instance.
     */
    public static function get_instance(): ?Leto_Disable_Search
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initializes the plugin by setting up localization and registering hooks.
     */
    private function init(): void
    {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        $this->setup_hooks();


		if(is_admin())
		{
			$myUpdateChecker = PucFactory::buildUpdateChecker(
				'https://files.letowp.de/plugins/leto-disable-search/update.json',
				__FILE__,
				self::TEXT_DOMAIN
			);
		}
    }

    /**
     * Registers all hooks for the plugin.
     */
    private function setup_hooks(): void
    {
        add_action('widgets_init', [$this, 'disable_widgets_init']);
        add_action('get_search_form', '__return_empty_string', 999);
        add_action('init', [$this, 'disable_search_block']);
        add_action('parse_query', [$this, 'disable_parse_query'], 99);
        add_action('admin_bar_menu', [$this, 'remove_admin_bar_menu_search'], 99);
    }


    /**
     * Loads the plugin's textdomain for localization.
     */
    public function load_textdomain(): void
    {
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Gets the plugin path.
     *
     * @return string The plugin directory path.
     */
    public function get_plugin_path(): string
    {
        return $this->plugin_path;
    }

    /**
     * Gets the plugin URL.
     *
     * @return string The plugin directory URL.
     */
    public function get_plugin_url(): string
    {
        return $this->plugin_url;
    }

    /**
     * Gets the plugin version.
     *
     * @return string The plugin version.
     */
    public function get_plugin_version(): string
    {
        return self::PLUGIN_VERSION;
    }

    public function disable_widgets_init(): void
    {
        unregister_widget('WP_Widget_Search');
    }

    public function disable_search_block(): void
    {
        if (function_exists('unregister_block_type') && WP_Block_Type_Registry::get_instance()->is_registered('core/search')) {

            unregister_block_type('core/search');
        }
    }

    public function disable_parse_query($query): void
    {

        if (is_search() & !is_admin()) {

            $query->is_search = false;
            $query->query_vars['s'] = false;
            $query->query['s'] = false;
            $query->set_404();
            status_header(404);
            nocache_headers();

        }
    }

    public function remove_admin_bar_menu_search($wp_admin_bar): void
    {

        if ($wp_admin_bar->get_node('search')) {
            $wp_admin_bar->remove_menu('search');
        }

    }

}

Leto_Disable_Search::get_instance();
