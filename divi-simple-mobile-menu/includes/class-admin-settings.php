<?php
/**
 * Admin Settings Page
 *
 * @package Divi_Simple_Mobile_Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class DSMM_Admin_Settings {

    private $options;
    private $defaults;
    private $option_name = 'dsmm_options';

    public function __construct($options, $defaults) {
        $this->options = $options;
        $this->defaults = $defaults;
        
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_options_page(
            __('Divi Simple Mobile Menu', 'divi-simple-mobile-menu'),
            __('Mobile Menu', 'divi-simple-mobile-menu'),
            'manage_options',
            'divi-simple-mobile-menu',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'dsmm_options_group',
            $this->option_name,
            [$this, 'sanitize_options']
        );

        // General Section
        add_settings_section(
            'dsmm_general_section',
            __('General Settings', 'divi-simple-mobile-menu'),
            [$this, 'render_general_section'],
            'divi-simple-mobile-menu'
        );

        add_settings_field(
            'enabled',
            __('Enable Mobile Menu', 'divi-simple-mobile-menu'),
            [$this, 'render_checkbox_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'enabled', 'description' => 'Enable or disable the mobile menu functionality.']
        );

        add_settings_field(
            'breakpoint',
            __('Breakpoint Width (px)', 'divi-simple-mobile-menu'),
            [$this, 'render_number_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'breakpoint', 'min' => 320, 'max' => 1920, 'description' => 'Screen width at which the mobile menu activates. Default is 981px (Divi default).']
        );

        add_settings_field(
            'menu_location',
            __('Menu Location', 'divi-simple-mobile-menu'),
            [$this, 'render_menu_select_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'menu_location', 'description' => 'Select which menu to display in the mobile menu.']
        );

        add_settings_field(
            'position',
            __('Menu Position', 'divi-simple-mobile-menu'),
            [$this, 'render_select_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            [
                'field' => 'position',
                'options' => [
                    'right' => __('Right', 'divi-simple-mobile-menu'),
                    'left'  => __('Left', 'divi-simple-mobile-menu'),
                ],
                'description' => 'Which side the menu slides in from.'
            ]
        );

        add_settings_field(
            'animation',
            __('Animation Style', 'divi-simple-mobile-menu'),
            [$this, 'render_select_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            [
                'field' => 'animation',
                'options' => [
                    'slide'  => __('Slide', 'divi-simple-mobile-menu'),
                    'push'   => __('Push (moves page content)', 'divi-simple-mobile-menu'),
                    'reveal' => __('Reveal (page slides to show menu)', 'divi-simple-mobile-menu'),
                    'fade'   => __('Fade', 'divi-simple-mobile-menu'),
                ],
                'description' => 'How the menu appears when opened.'
            ]
        );

        add_settings_field(
            'header_height',
            __('Header Height (px)', 'divi-simple-mobile-menu'),
            [$this, 'render_number_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'header_height', 'min' => 40, 'max' => 200, 'description' => 'Height of your main header at mobile. Used to position the burger and maintain header height when Divi nav is hidden.']
        );

        add_settings_field(
            'top_header_height',
            __('Top Header Height (px)', 'divi-simple-mobile-menu'),
            [$this, 'render_number_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'top_header_height', 'min' => 0, 'max' => 100, 'description' => 'If you have a secondary top header (#top-header) above the main header, enter its height here. Set to 0 if not using.']
        );

        add_settings_field(
            'menu_width',
            __('Menu Width', 'divi-simple-mobile-menu'),
            [$this, 'render_select_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            [
                'field' => 'menu_width',
                'options' => [
                    '100' => __('100% (Full width)', 'divi-simple-mobile-menu'),
                    '80'  => __('80%', 'divi-simple-mobile-menu'),
                    '70'  => __('70%', 'divi-simple-mobile-menu'),
                    '50'  => __('50%', 'divi-simple-mobile-menu'),
                ],
                'description' => 'Width of the mobile menu panel.'
            ]
        );

        add_settings_field(
            'burger_style',
            __('Burger Icon Style', 'divi-simple-mobile-menu'),
            [$this, 'render_select_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            [
                'field' => 'burger_style',
                'options' => [
                    'hamburger' => __('Hamburger (3 lines)', 'divi-simple-mobile-menu'),
                    'dots'      => __('Dots (3 dots)', 'divi-simple-mobile-menu'),
                    'arrow'     => __('Arrow', 'divi-simple-mobile-menu'),
                ],
                'description' => 'Style of the burger menu icon.'
            ]
        );

        add_settings_field(
            'show_close_button',
            __('Show Close Button', 'divi-simple-mobile-menu'),
            [$this, 'render_checkbox_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'show_close_button', 'description' => 'Show an X close button inside the menu panel.']
        );

        add_settings_field(
            'show_overlay',
            __('Show Overlay', 'divi-simple-mobile-menu'),
            [$this, 'render_checkbox_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'show_overlay', 'description' => 'Show a dark overlay behind the menu when open.']
        );

        add_settings_field(
            'overlay_color',
            __('Overlay Color', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'overlay_color']
        );

        add_settings_field(
            'overlay_opacity',
            __('Overlay Opacity (%)', 'divi-simple-mobile-menu'),
            [$this, 'render_number_field'],
            'divi-simple-mobile-menu',
            'dsmm_general_section',
            ['field' => 'overlay_opacity', 'min' => 0, 'max' => 100, 'description' => 'Opacity of the overlay (0 = transparent, 100 = solid).']
        );

        // Colors Section
        add_settings_section(
            'dsmm_colors_section',
            __('Color Settings', 'divi-simple-mobile-menu'),
            [$this, 'render_colors_section'],
            'divi-simple-mobile-menu'
        );

        add_settings_field(
            'background_color',
            __('Background Color', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'background_color']
        );

        add_settings_field(
            'link_color',
            __('Link Color', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'link_color']
        );

        add_settings_field(
            'link_hover_color',
            __('Link Hover Color', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'link_hover_color']
        );

        add_settings_field(
            'burger_color',
            __('Burger Icon Color', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'burger_color']
        );

        add_settings_field(
            'burger_open_color',
            __('Burger Icon Color (Open)', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'burger_open_color']
        );

        add_settings_field(
            'fixed_header_class',
            __('Fixed Header Class', 'divi-simple-mobile-menu'),
            [$this, 'render_text_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'fixed_header_class', 'placeholder' => 'et-fixed-header', 'description' => 'CSS class added to body or header when scrolled (without the dot). Default: et-fixed-header']
        );

        add_settings_field(
            'burger_fixed_color',
            __('Burger Icon Color (Fixed Header)', 'divi-simple-mobile-menu'),
            [$this, 'render_color_field'],
            'divi-simple-mobile-menu',
            'dsmm_colors_section',
            ['field' => 'burger_fixed_color', 'description' => 'Burger color when the fixed header class is active.']
        );

        // Contact Info Section
        add_settings_section(
            'dsmm_contact_section',
            __('Contact Information (Optional)', 'divi-simple-mobile-menu'),
            [$this, 'render_contact_section'],
            'divi-simple-mobile-menu'
        );

        add_settings_field(
            'show_contact_info',
            __('Show Contact Info', 'divi-simple-mobile-menu'),
            [$this, 'render_checkbox_field'],
            'divi-simple-mobile-menu',
            'dsmm_contact_section',
            ['field' => 'show_contact_info', 'description' => 'Display contact information in the mobile menu.']
        );

        add_settings_field(
            'phone_number',
            __('Phone Number', 'divi-simple-mobile-menu'),
            [$this, 'render_text_field'],
            'divi-simple-mobile-menu',
            'dsmm_contact_section',
            ['field' => 'phone_number', 'placeholder' => '+1 234 567 8900']
        );

        add_settings_field(
            'email_address',
            __('Email Address', 'divi-simple-mobile-menu'),
            [$this, 'render_text_field'],
            'divi-simple-mobile-menu',
            'dsmm_contact_section',
            ['field' => 'email_address', 'placeholder' => 'info@example.com']
        );
    }

    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        $sanitized = [];

        $sanitized['enabled'] = isset($input['enabled']) ? (bool) $input['enabled'] : false;
        $sanitized['breakpoint'] = isset($input['breakpoint']) ? absint($input['breakpoint']) : $this->defaults['breakpoint'];
        $sanitized['background_color'] = isset($input['background_color']) ? sanitize_hex_color($input['background_color']) : $this->defaults['background_color'];
        $sanitized['link_color'] = isset($input['link_color']) ? sanitize_hex_color($input['link_color']) : $this->defaults['link_color'];
        $sanitized['link_hover_color'] = isset($input['link_hover_color']) ? sanitize_hex_color($input['link_hover_color']) : $this->defaults['link_hover_color'];
        $sanitized['burger_color'] = isset($input['burger_color']) ? sanitize_hex_color($input['burger_color']) : $this->defaults['burger_color'];
        $sanitized['burger_open_color'] = isset($input['burger_open_color']) ? sanitize_hex_color($input['burger_open_color']) : $this->defaults['burger_open_color'];
        $sanitized['fixed_header_class'] = isset($input['fixed_header_class']) ? sanitize_html_class($input['fixed_header_class']) : $this->defaults['fixed_header_class'];
        $sanitized['burger_fixed_color'] = isset($input['burger_fixed_color']) ? sanitize_hex_color($input['burger_fixed_color']) : $this->defaults['burger_fixed_color'];
        $sanitized['menu_location'] = isset($input['menu_location']) ? sanitize_text_field($input['menu_location']) : $this->defaults['menu_location'];
        $sanitized['position'] = isset($input['position']) && in_array($input['position'], ['left', 'right']) ? $input['position'] : $this->defaults['position'];
        $sanitized['animation'] = isset($input['animation']) && in_array($input['animation'], ['slide', 'push', 'reveal', 'fade']) ? $input['animation'] : $this->defaults['animation'];
        $sanitized['header_height'] = isset($input['header_height']) ? absint($input['header_height']) : $this->defaults['header_height'];
        $sanitized['top_header_height'] = isset($input['top_header_height']) ? absint($input['top_header_height']) : $this->defaults['top_header_height'];
        $sanitized['menu_width'] = isset($input['menu_width']) && in_array($input['menu_width'], ['100', '80', '70', '50']) ? $input['menu_width'] : $this->defaults['menu_width'];
        $sanitized['burger_style'] = isset($input['burger_style']) && in_array($input['burger_style'], ['hamburger', 'dots', 'arrow']) ? $input['burger_style'] : $this->defaults['burger_style'];
        $sanitized['show_close_button'] = isset($input['show_close_button']) ? (bool) $input['show_close_button'] : false;
        $sanitized['show_overlay'] = isset($input['show_overlay']) ? (bool) $input['show_overlay'] : false;
        $sanitized['overlay_color'] = isset($input['overlay_color']) ? sanitize_hex_color($input['overlay_color']) : $this->defaults['overlay_color'];
        $sanitized['overlay_opacity'] = isset($input['overlay_opacity']) ? min(100, max(0, absint($input['overlay_opacity']))) : $this->defaults['overlay_opacity'];
        $sanitized['show_contact_info'] = isset($input['show_contact_info']) ? (bool) $input['show_contact_info'] : false;
        $sanitized['phone_number'] = isset($input['phone_number']) ? sanitize_text_field($input['phone_number']) : '';
        $sanitized['email_address'] = isset($input['email_address']) ? sanitize_email($input['email_address']) : '';

        return $sanitized;
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('settings_page_divi-simple-mobile-menu' !== $hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_add_inline_script('wp-color-picker', '
            jQuery(document).ready(function($) {
                $(".dsmm-color-picker").wpColorPicker({
                    change: function(event, ui) {
                        updatePreview();
                    }
                });
                
                // Update preview on any input change
                $(document).on("change", "select, input", function() {
                    setTimeout(updatePreview, 100);
                });
                
                function updatePreview() {
                    var bgColor = $("input[name=\"dsmm_options[background_color]\"]").val();
                    var linkColor = $("input[name=\"dsmm_options[link_color]\"]").val();
                    var linkHoverColor = $("input[name=\"dsmm_options[link_hover_color]\"]").val();
                    var burgerColor = $("input[name=\"dsmm_options[burger_color]\"]").val();
                    var burgerOpenColor = $("input[name=\"dsmm_options[burger_open_color]\"]").val();
                    var burgerFixedColor = $("input[name=\"dsmm_options[burger_fixed_color]\"]").val();
                    var burgerStyle = $("select[name=\"dsmm_options[burger_style]\"]").val();
                    var menuWidth = $("select[name=\"dsmm_options[menu_width]\"]").val();
                    var position = $("select[name=\"dsmm_options[position]\"]").val();
                    
                    // Update burger preview
                    $(".dsmm-preview-burger-closed").css("background", "transparent");
                    $(".dsmm-preview-burger-open").css("background", bgColor);
                    $(".dsmm-preview-burger-fixed").css("background", "#333");
                    
                    // Update burger icons based on style - use dsmm-colorable for bars/dots
                    $(".dsmm-preview-burger-closed .dsmm-colorable").css("background", burgerColor);
                    $(".dsmm-preview-burger-open .dsmm-colorable").css("background", burgerOpenColor);
                    $(".dsmm-preview-burger-fixed .dsmm-colorable").css("background", burgerFixedColor);
                    
                    // Arrow uses border-color
                    $(".dsmm-preview-burger-closed .arrow").css("border-color", burgerColor);
                    $(".dsmm-preview-burger-open .arrow").css("border-color", burgerOpenColor);
                    $(".dsmm-preview-burger-fixed .arrow").css("border-color", burgerFixedColor);
                    
                    // Show/hide burger styles
                    $(".dsmm-burger-hamburger, .dsmm-burger-dots, .dsmm-burger-arrow").hide();
                    $(".dsmm-burger-" + burgerStyle).show();
                    
                    // Update menu preview
                    $(".dsmm-preview-menu").css({
                        "background": bgColor,
                        "width": menuWidth + "%"
                    });
                    $(".dsmm-preview-menu a").css("color", linkColor);
                    $(".dsmm-preview-menu").attr("data-position", position);
                }
                
                // Initial update
                updatePreview();
            });
        ');

        wp_add_inline_style('wp-admin', '
            .dsmm-settings-wrap {
                max-width: 900px;
            }
            .dsmm-settings-wrap .form-table th {
                width: 200px;
            }
            .dsmm-field-description {
                color: #666;
                font-style: italic;
                margin-top: 4px;
            }
            
            /* Preview Section */
            .dsmm-preview-section {
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                padding: 20px;
                margin: 20px 0;
            }
            .dsmm-preview-section h3 {
                margin-top: 0;
            }
            
            /* Burger Previews */
            .dsmm-preview-burgers {
                display: flex;
                gap: 30px;
                flex-wrap: wrap;
                margin-bottom: 30px;
            }
            .dsmm-preview-burger-wrap {
                text-align: center;
                position: relative;
            }
            .dsmm-preview-burger-wrap p {
                margin: 8px 0 0;
                font-size: 12px;
                color: #666;
            }
            .dsmm-preview-burger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 60px;
                height: 60px;
                border-radius: 4px;
                border: 1px solid #ddd;
            }
            
            /* Hamburger style */
            .dsmm-burger-hamburger {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .dsmm-burger-hamburger .bar {
                display: block;
                width: 28px;
                height: 3px;
                margin: 3px 0;
                border-radius: 2px;
            }
            
            /* Dots style */
            .dsmm-burger-dots {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .dsmm-burger-dots .dot {
                display: block;
                width: 6px;
                height: 6px;
                margin: 2px 0;
                border-radius: 50%;
            }
            
            /* Arrow style */
            .dsmm-burger-arrow {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .dsmm-burger-arrow .arrow {
                display: block;
                width: 12px;
                height: 12px;
                border-style: solid;
                border-width: 0 3px 3px 0;
                transform: rotate(-45deg);
                background: transparent !important;
                position: static !important;
                margin: 0 !important;
                bottom: auto !important;
                left: auto !important;
                z-index: auto !important;
                overflow: visible !important;
            }
            .dsmm-burger-arrow .arrow::after {
                display: none !important;
            }
            
            /* Menu Preview */
            .dsmm-preview-menu-container {
                position: relative;
                height: 200px;
                background: #e0e0e0;
                border-radius: 4px;
                overflow: hidden;
                border: 1px solid #ccc;
            }
            .dsmm-preview-menu-container::before {
                content: "Page Content";
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #999;
                font-size: 14px;
            }
            .dsmm-preview-menu {
                position: absolute;
                top: 0;
                height: 100%;
                padding: 15px;
                box-sizing: border-box;
                transition: all 0.3s;
            }
            .dsmm-preview-menu[data-position="right"] {
                right: 0;
            }
            .dsmm-preview-menu[data-position="left"] {
                left: 0;
            }
            .dsmm-preview-menu ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .dsmm-preview-menu li {
                margin-bottom: 8px;
            }
            .dsmm-preview-menu a {
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
            }
        ');
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        $bg_color = $this->options['background_color'];
        $link_color = $this->options['link_color'];
        $burger_color = $this->options['burger_color'];
        $burger_open_color = $this->options['burger_open_color'];
        $burger_fixed_color = $this->options['burger_fixed_color'];
        $burger_style = $this->options['burger_style'];
        $menu_width = $this->options['menu_width'];
        $position = $this->options['position'];
        ?>
        <div class="wrap dsmm-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('dsmm_options_group');
                do_settings_sections('divi-simple-mobile-menu');
                submit_button(__('Save Settings', 'divi-simple-mobile-menu'));
                ?>
            </form>

            <div class="dsmm-preview-section">
                <h3><?php _e('Live Preview', 'divi-simple-mobile-menu'); ?></h3>
                
                <h4><?php _e('Burger Icon States', 'divi-simple-mobile-menu'); ?></h4>
                <div class="dsmm-preview-burgers">
                    <!-- Closed State -->
                    <div class="dsmm-preview-burger-wrap">
                        <div class="dsmm-preview-burger dsmm-preview-burger-closed">
                            <div class="dsmm-burger-hamburger" style="display:<?php echo $burger_style !== 'hamburger' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-dots" style="display:<?php echo $burger_style !== 'dots' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-arrow" style="display:<?php echo $burger_style !== 'arrow' ? 'none' : 'flex'; ?>; align-items:center; justify-content:center; width:100%; height:100%;">
                                <span class="arrow" style="display:block; width:12px; height:12px; border-style:solid; border-width:0 3px 3px 0; transform:rotate(-45deg); border-color:<?php echo esc_attr($burger_color); ?>;"></span>
                            </div>
                        </div>
                        <p><?php _e('Closed', 'divi-simple-mobile-menu'); ?></p>
                    </div>
                    
                    <!-- Open State -->
                    <div class="dsmm-preview-burger-wrap">
                        <div class="dsmm-preview-burger dsmm-preview-burger-open" style="background: <?php echo esc_attr($bg_color); ?>;">
                            <div class="dsmm-burger-hamburger" style="display:<?php echo $burger_style !== 'hamburger' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-dots" style="display:<?php echo $burger_style !== 'dots' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_open_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-arrow" style="display:<?php echo $burger_style !== 'arrow' ? 'none' : 'flex'; ?>; align-items:center; justify-content:center; width:100%; height:100%;">
                                <span class="arrow" style="display:block; width:12px; height:12px; border-style:solid; border-width:0 3px 3px 0; transform:rotate(-45deg); border-color:<?php echo esc_attr($burger_open_color); ?>;"></span>
                            </div>
                        </div>
                        <p><?php _e('Open (on menu bg)', 'divi-simple-mobile-menu'); ?></p>
                    </div>
                    
                    <!-- Fixed Header State -->
                    <div class="dsmm-preview-burger-wrap">
                        <div class="dsmm-preview-burger dsmm-preview-burger-fixed" style="background: #333;">
                            <div class="dsmm-burger-hamburger" style="display:<?php echo $burger_style !== 'hamburger' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                                <span class="bar dsmm-colorable" style="display:block; width:28px; height:3px; margin:3px 0; border-radius:2px; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-dots" style="display:<?php echo $burger_style !== 'dots' ? 'none' : 'flex'; ?>; flex-direction:column; align-items:center; justify-content:center;">
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                                <span class="dot dsmm-colorable" style="display:block; width:6px; height:6px; margin:2px 0; border-radius:50%; background:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                            </div>
                            <div class="dsmm-burger-arrow" style="display:<?php echo $burger_style !== 'arrow' ? 'none' : 'flex'; ?>; align-items:center; justify-content:center; width:100%; height:100%;">
                                <span class="arrow" style="display:block; width:12px; height:12px; border-style:solid; border-width:0 3px 3px 0; transform:rotate(-45deg); border-color:<?php echo esc_attr($burger_fixed_color); ?>;"></span>
                            </div>
                        </div>
                        <p><?php _e('Fixed Header', 'divi-simple-mobile-menu'); ?></p>
                    </div>
                </div>
                
                <h4><?php _e('Menu Panel Preview', 'divi-simple-mobile-menu'); ?></h4>
                <div class="dsmm-preview-menu-container">
                    <div class="dsmm-preview-menu" data-position="<?php echo esc_attr($position); ?>" style="background: <?php echo esc_attr($bg_color); ?>; width: <?php echo esc_attr($menu_width); ?>%;">
                        <ul>
                            <li><a href="#" style="color: <?php echo esc_attr($link_color); ?>;">HOME</a></li>
                            <li><a href="#" style="color: <?php echo esc_attr($link_color); ?>;">ABOUT US</a></li>
                            <li><a href="#" style="color: <?php echo esc_attr($link_color); ?>;">SERVICES</a></li>
                            <li><a href="#" style="color: <?php echo esc_attr($link_color); ?>;">PORTFOLIO</a></li>
                            <li><a href="#" style="color: <?php echo esc_attr($link_color); ?>;">CONTACT</a></li>
                        </ul>
                    </div>
                </div>
                <p class="description"><?php _e('Note: Save settings and refresh to see updated preview, or changes update live as you modify color pickers.', 'divi-simple-mobile-menu'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Section callbacks
     */
    public function render_general_section() {
        echo '<p>' . __('Configure the general settings for your mobile menu.', 'divi-simple-mobile-menu') . '</p>';
    }

    public function render_colors_section() {
        echo '<p>' . __('Customize the colors of your mobile menu.', 'divi-simple-mobile-menu') . '</p>';
    }

    public function render_contact_section() {
        echo '<p>' . __('Add contact information to display at the bottom of the mobile menu.', 'divi-simple-mobile-menu') . '</p>';
    }

    /**
     * Field renderers
     */
    public function render_checkbox_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : false;
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
                   value="1" 
                   <?php checked($value, true); ?>>
            <?php if ($description): ?>
                <span class="dsmm-field-description"><?php echo esc_html($description); ?></span>
            <?php endif; ?>
        </label>
        <?php
    }

    public function render_number_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : $this->defaults[$field];
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 9999;
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <input type="number" 
               name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($min); ?>"
               max="<?php echo esc_attr($max); ?>"
               class="small-text">
        <?php if ($description): ?>
            <p class="dsmm-field-description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }

    public function render_text_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        ?>
        <input type="text" 
               name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($placeholder); ?>"
               class="regular-text">
        <?php
    }

    public function render_color_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : $this->defaults[$field];
        ?>
        <input type="text" 
               name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>" 
               value="<?php echo esc_attr($value); ?>"
               class="dsmm-color-picker"
               data-default-color="<?php echo esc_attr($this->defaults[$field]); ?>">
        <?php
    }

    public function render_menu_select_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        $description = isset($args['description']) ? $args['description'] : '';
        $menus = get_registered_nav_menus();
        ?>
        <select name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>">
            <?php foreach ($menus as $location => $name): ?>
                <option value="<?php echo esc_attr($location); ?>" <?php selected($value, $location); ?>>
                    <?php echo esc_html($name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description): ?>
            <p class="dsmm-field-description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }

    public function render_select_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : $this->defaults[$field];
        $options = isset($args['options']) ? $args['options'] : [];
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <select name="<?php echo esc_attr($this->option_name . '[' . $field . ']'); ?>">
            <?php foreach ($options as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description): ?>
            <p class="dsmm-field-description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php
    }
}
