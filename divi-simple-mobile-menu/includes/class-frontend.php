<?php
/**
 * Frontend Output Class
 *
 * @package Divi_Simple_Mobile_Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class DSMM_Frontend {

    private $options;

    public function __construct($options) {
        $this->options = $options;
        
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_head', [$this, 'output_dynamic_css']);
        add_action('wp_body_open', [$this, 'render_burger_button'], 5);
        add_action('wp_body_open', [$this, 'render_mobile_menu'], 10);
        add_action('wp_footer', [$this, 'render_mobile_menu_fallback'], 10);
        
        // Add body class for mobile menu active state
        add_filter('body_class', [$this, 'add_body_classes']);
    }

    /**
     * Add body classes
     */
    public function add_body_classes($classes) {
        $classes[] = 'dsmm-enabled';
        $classes[] = 'dsmm-position-' . $this->options['position'];
        $classes[] = 'dsmm-animation-' . $this->options['animation'];
        $classes[] = 'dsmm-style-' . $this->options['burger_style'];
        if ($this->options['show_overlay']) {
            $classes[] = 'dsmm-has-overlay';
        }
        return $classes;
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        // Main CSS
        wp_enqueue_style(
            'dsmm-style',
            DSMM_PLUGIN_URL . 'assets/css/mobile-menu.css',
            [],
            DSMM_VERSION
        );

        // Main JS
        wp_enqueue_script(
            'dsmm-script',
            DSMM_PLUGIN_URL . 'assets/js/mobile-menu.js',
            [],
            DSMM_VERSION,
            true
        );

        // Pass options to JS
        wp_localize_script('dsmm-script', 'dsmmOptions', [
            'breakpoint'       => $this->options['breakpoint'],
            'position'         => $this->options['position'],
            'animation'        => $this->options['animation'],
            'menuId'           => 'dsmm-mobile-menu',
            'burgerId'         => 'dsmm-burger',
            'fixedHeaderClass' => $this->options['fixed_header_class'],
        ]);
    }

    /**
     * Output dynamic CSS based on settings
     */
    public function output_dynamic_css() {
        $bg_color           = $this->options['background_color'];
        $link_color         = $this->options['link_color'];
        $link_hover_color   = $this->options['link_hover_color'];
        $burger_color       = $this->options['burger_color'];
        $burger_open        = $this->options['burger_open_color'];
        $fixed_header_class = $this->options['fixed_header_class'];
        $burger_fixed_color = $this->options['burger_fixed_color'];
        $breakpoint         = $this->options['breakpoint'];
        $position           = $this->options['position'];
        $header_height      = $this->options['header_height'];
        $top_header_height  = $this->options['top_header_height'];
        $menu_width         = $this->options['menu_width'];
        $overlay_color      = $this->options['overlay_color'];
        $overlay_opacity    = $this->options['overlay_opacity'] / 100;
        ?>
        <style id="dsmm-dynamic-css">
            :root {
                --dsmm-bg-color: <?php echo esc_attr($bg_color); ?>;
                --dsmm-link-color: <?php echo esc_attr($link_color); ?>;
                --dsmm-link-hover-color: <?php echo esc_attr($link_hover_color); ?>;
                --dsmm-burger-color: <?php echo esc_attr($burger_color); ?>;
                --dsmm-burger-open-color: <?php echo esc_attr($burger_open); ?>;
                --dsmm-burger-fixed-color: <?php echo esc_attr($burger_fixed_color); ?>;
                --dsmm-breakpoint: <?php echo esc_attr($breakpoint); ?>px;
                --dsmm-header-height: <?php echo esc_attr($header_height); ?>px;
                --dsmm-top-header-height: <?php echo esc_attr($top_header_height); ?>px;
                --dsmm-menu-width: <?php echo esc_attr($menu_width); ?>%;
                --dsmm-overlay-color: <?php echo esc_attr($overlay_color); ?>;
                --dsmm-overlay-opacity: <?php echo esc_attr($overlay_opacity); ?>;
            }
            
            /* Fixed header burger color - via JS class on burger */
            .dsmm-burger.dsmm-burger-fixed .dsmm-burger-bar,
            .dsmm-burger.dsmm-burger-fixed .dsmm-burger-dot {
                background-color: var(--dsmm-burger-fixed-color);
            }
            
            .dsmm-burger.dsmm-burger-fixed .dsmm-burger-arrow {
                border-right-color: var(--dsmm-burger-fixed-color);
                border-bottom-color: var(--dsmm-burger-fixed-color);
            }
            
            /* Legacy: Fixed header burger color - via parent class (if burger is inside header) */
            <?php if (!empty($fixed_header_class)): ?>
            .<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-bar,
            .<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-dot,
            body.<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-bar,
            body.<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-dot {
                background-color: var(--dsmm-burger-fixed-color);
            }
            
            .<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-arrow,
            body.<?php echo esc_attr($fixed_header_class); ?> .dsmm-burger .dsmm-burger-arrow {
                border-right-color: var(--dsmm-burger-fixed-color);
                border-bottom-color: var(--dsmm-burger-fixed-color);
            }
            <?php endif; ?>

            /* Show burger only at breakpoint */
            .dsmm-burger {
                display: none;
            }
            
            /* Position burger based on menu position */
            @media screen and (max-width: <?php echo esc_attr($breakpoint); ?>px) {
                .dsmm-burger {
                    display: flex;
                    <?php echo $position === 'left' ? 'left: 20px; right: auto;' : 'right: 20px; left: auto;'; ?>
                }
                
                /* Hide Divi's default mobile menu toggle */
                .mobile_menu_bar,
                .mobile_nav,
                #et_mobile_nav_menu {
                    display: none !important;
                }
                
                /* Maintain header height when Divi nav is hidden */
                #main-header .container {
                    min-height: <?php echo esc_attr($header_height); ?>px !important;
                }
                
                #main-header {
                    min-height: <?php echo esc_attr($header_height); ?>px !important;
                }
                
                /* Ensure logo container doesn't collapse */
                #main-header .logo_container {
                    height: <?php echo esc_attr($header_height); ?>px;
                    display: flex;
                    align-items: center;
                }
            }
        </style>
        <?php
    }

    /**
     * Render burger button
     */
    public function render_burger_button() {
        $style = $this->options['burger_style'];
        ?>
        <button class="dsmm-burger dsmm-burger-style-<?php echo esc_attr($style); ?>" id="dsmm-burger" aria-label="<?php esc_attr_e('Toggle mobile menu', 'divi-simple-mobile-menu'); ?>" aria-expanded="false" aria-controls="dsmm-mobile-menu">
            <?php if ($style === 'hamburger'): ?>
                <span class="dsmm-burger-bar"></span>
                <span class="dsmm-burger-bar"></span>
                <span class="dsmm-burger-bar"></span>
            <?php elseif ($style === 'dots'): ?>
                <span class="dsmm-burger-dot"></span>
                <span class="dsmm-burger-dot"></span>
                <span class="dsmm-burger-dot"></span>
            <?php elseif ($style === 'arrow'): ?>
                <span class="dsmm-burger-arrow"></span>
            <?php endif; ?>
        </button>
        <?php if ($this->options['show_overlay']): ?>
        <div class="dsmm-overlay" id="dsmm-overlay"></div>
        <?php endif;
    }

    /**
     * Render mobile menu
     */
    public function render_mobile_menu() {
        static $rendered = false;
        if ($rendered) {
            return;
        }
        $rendered = true;
        
        $menu_location = $this->options['menu_location'];
        $show_close = $this->options['show_close_button'];
        ?>
        <nav class="dsmm-mobile-menu" id="dsmm-mobile-menu" aria-label="<?php esc_attr_e('Mobile menu', 'divi-simple-mobile-menu'); ?>">
            <div class="dsmm-menu-inner">
                <?php if ($show_close): ?>
                <button class="dsmm-close-button" id="dsmm-close" aria-label="<?php esc_attr_e('Close menu', 'divi-simple-mobile-menu'); ?>">
                    <span class="dsmm-close-icon">&times;</span>
                </button>
                <?php endif; ?>
                
                <?php
                wp_nav_menu([
                    'theme_location' => $menu_location,
                    'container'      => false,
                    'menu_class'     => 'dsmm-menu-list',
                    'walker'         => new DSMM_Walker_Nav_Menu(),
                    'fallback_cb'    => [$this, 'fallback_menu'],
                ]);
                ?>

                <?php if ($this->options['show_contact_info']): ?>
                    <div class="dsmm-contact-info">
                        <hr>
                        <?php if (!empty($this->options['phone_number'])): ?>
                            <div class="dsmm-contact-item">
                                <strong><?php _e('Phone:', 'divi-simple-mobile-menu'); ?></strong>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $this->options['phone_number'])); ?>">
                                    <?php echo esc_html($this->options['phone_number']); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($this->options['email_address'])): ?>
                            <div class="dsmm-contact-item">
                                <strong><?php _e('Email:', 'divi-simple-mobile-menu'); ?></strong>
                                <a href="mailto:<?php echo esc_attr($this->options['email_address']); ?>">
                                    <?php echo esc_html($this->options['email_address']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
        <?php
    }

    /**
     * Fallback render for themes that don't support wp_body_open
     */
    public function render_mobile_menu_fallback() {
        // Check if menu was already rendered
        static $rendered = false;
        if ($rendered) {
            return;
        }
        
        // If wp_body_open wasn't called, render in footer
        if (!did_action('wp_body_open')) {
            $this->render_burger_button();
            $this->render_mobile_menu();
        }
        
        $rendered = true;
    }

    /**
     * Fallback menu if no menu is assigned
     */
    public function fallback_menu() {
        echo '<ul class="dsmm-menu-list">';
        echo '<li><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . __('Add a menu', 'divi-simple-mobile-menu') . '</a></li>';
        echo '</ul>';
    }
}
