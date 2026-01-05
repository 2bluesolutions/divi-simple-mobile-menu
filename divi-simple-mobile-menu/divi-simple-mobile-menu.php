<?php
/**
 * Plugin Name: Divi Simple Mobile Menu
 * Plugin URI: https://2bluesolutions.com
 * Description: A simple, customizable mobile burger menu for Divi themes.
 * Version: 1.0.0
 * Author: 2BlueSolutions
 * Author URI: https://2bluesolutions.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: divi-simple-mobile-menu
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Constants
define('DSMM_VERSION', '1.0.0');
define('DSMM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DSMM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DSMM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Divi_Simple_Mobile_Menu {

    /**
     * Instance of this class.
     */
    private static $instance = null;

    /**
     * Plugin options
     */
    private $options;

    /**
     * Default options
     */
    private $defaults = [
        'enabled'           => true,
        'breakpoint'        => 981,
        'position'          => 'right',
        'animation'         => 'slide',
        'header_height'     => 114,
        'top_header_height' => 0,
        'menu_width'        => '100',
        'burger_style'      => 'hamburger',
        'show_close_button' => false,
        'show_overlay'      => true,
        'overlay_color'     => '#000000',
        'overlay_opacity'   => 50,
        'background_color'  => '#cd1480',
        'link_color'        => '#ffffff',
        'link_hover_color'  => '#ffc107',
        'burger_color'       => '#cd1480',
        'burger_open_color'  => '#ffffff',
        'fixed_header_class' => 'et-fixed-header',
        'burger_fixed_color' => '#ffffff',
        'menu_location'      => 'primary-menu',
        'show_contact_info' => true,
        'phone_number'      => '',
        'email_address'     => '',
    ];

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->options = wp_parse_args(
            get_option('dsmm_options', []),
            $this->defaults
        );

        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once DSMM_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once DSMM_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once DSMM_PLUGIN_DIR . 'includes/class-walker-nav-menu.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Admin
        if (is_admin()) {
            new DSMM_Admin_Settings($this->options, $this->defaults);
        }

        // Frontend
        if (!is_admin() && $this->options['enabled']) {
            new DSMM_Frontend($this->options);
        }

        // Activation hook
        register_activation_hook(__FILE__, [$this, 'activate']);
        
        // Deactivation hook
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
    }

    /**
     * Get options
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * Get single option
     */
    public function get_option($key, $default = null) {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if not already set
        if (!get_option('dsmm_options')) {
            update_option('dsmm_options', $this->defaults);
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Initialize plugin
 */
function dsmm_init() {
    return Divi_Simple_Mobile_Menu::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'dsmm_init');

/**
 * Helper function to get plugin options
 */
function dsmm_get_option($key, $default = null) {
    $plugin = dsmm_init();
    return $plugin->get_option($key, $default);
}
