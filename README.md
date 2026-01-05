=== Divi Simple Mobile Menu ===

Contributors: 2bluesolutions
Tags: divi, mobile menu, burger menu, navigation, responsive
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple, customizable mobile burger menu for Divi themes.

== Description ==

Divi Simple Mobile Menu provides a clean, customizable mobile navigation experience for your Divi website. It replaces the default Divi mobile menu with a full-screen slide-out menu that's both beautiful and accessible.

**Features:**

* Full-screen slide-out mobile menu
* Customizable breakpoint width
* Color customization (background, links, hover states, burger icon)
* Support for nested submenus with smooth animations
* Optional contact information display
* Accessibility-friendly with ARIA attributes and keyboard navigation
* Compatible with Divi's fixed header
* Admin bar aware
* Lightweight and performant

== Installation ==

1. Upload the `divi-simple-mobile-menu` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Mobile Menu to configure your options

== Configuration ==

Navigate to **Settings > Mobile Menu** in your WordPress admin to configure:

**General Settings:**
* Enable/Disable the mobile menu
* Set the breakpoint width (default: 981px - same as Divi)
* Select which menu location to display

**Color Settings:**
* Background color of the menu panel
* Link color
* Link hover color
* Burger icon color (closed state)
* Burger icon color (open state)

**Contact Information:**
* Toggle contact info display
* Phone number
* Email address

== Frequently Asked Questions ==

= Does this work with child themes? =

Yes! The plugin works with any Divi theme or child theme.

= Can I customize the styling further? =

Yes, the plugin uses CSS custom properties (variables) that you can override in your theme's stylesheet:

```css
:root {
    --dsmm-bg-color: #your-color;
    --dsmm-link-color: #your-color;
    --dsmm-transition-speed: 0.3s;
}
```

= How do I change the menu position? =

Currently the menu slides in from the right. Future versions may include position options.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Divi Simple Mobile Menu.
