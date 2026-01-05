<?php
/**
 * Custom Walker for Mobile Menu Navigation
 *
 * @package Divi_Simple_Mobile_Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class DSMM_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Track parent IDs for submenu associations
     */
    private $parent_ids = [];

    /**
     * Start element output
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        
        // Check if item has children
        $has_children = !empty($args->has_children) || in_array('menu-item-has-children', $classes, true);

        if ($has_children && !in_array('menu-item-has-children', $classes, true)) {
            $classes[] = 'menu-item-has-children';
        }
        
        $classes[] = 'dsmm-menu-item';
        $classes[] = 'dsmm-depth-' . $depth;

        // Build class string
        $class_string = implode(' ', array_filter(array_map('sanitize_html_class', $classes)));

        $output .= sprintf(
            '<li id="dsmm-menu-item-%d" class="%s">',
            (int) $item->ID,
            esc_attr($class_string)
        );

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        if ($has_children) {
            // Track parent at this depth
            $this->parent_ids[$depth] = $item->ID;
            $submenu_id = 'dsmm-submenu-' . $item->ID;

            // Render as button for accessibility
            $output .= sprintf(
                '<button type="button" class="dsmm-parent-button" aria-haspopup="true" aria-expanded="false" aria-controls="%s">',
                esc_attr($submenu_id)
            );
            $output .= esc_html($title);
            $output .= '<span class="dsmm-dropdown-arrow" aria-hidden="true"></span>';
            $output .= '</button>';
        } else {
            // Build link attributes
            $atts = [];
            $atts['href']   = !empty($item->url) ? $item->url : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
            $atts['class']  = 'dsmm-menu-link';

            // Filter link attributes
            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

            // Build attribute string
            $attr_string = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $attr_string .= sprintf(' %s="%s"', esc_attr($attr), esc_attr($value));
                }
            }

            $output .= '<a' . $attr_string . '>';
            $output .= esc_html($title);
            $output .= '</a>';
        }
    }

    /**
     * End element output
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }

    /**
     * Start submenu level
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $parent_id = isset($this->parent_ids[$depth]) ? $this->parent_ids[$depth] : 0;
        $submenu_id = 'dsmm-submenu-' . $parent_id;
        $indent = str_repeat("\t", $depth);
        
        $output .= sprintf(
            "\n%s<ul class=\"dsmm-sub-menu dsmm-sub-menu-depth-%d\" id=\"%s\">\n",
            $indent,
            $depth,
            esc_attr($submenu_id)
        );
    }

    /**
     * End submenu level
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "{$indent}</ul>\n";
        unset($this->parent_ids[$depth]);
    }
}
