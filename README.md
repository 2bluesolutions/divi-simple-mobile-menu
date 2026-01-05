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

Divi Simple Mobile Menu provides a clean, customizable mobile navigation experience for your Divi website. It replaces the default Divi mobile menu with a slide-out menu that's both beautiful and accessible.

**Features:**

* Slide-out mobile menu with multiple animation styles
* Customizable breakpoint width
* Left or right position options
* Multiple burger icon styles (hamburger, dots, arrow)
* Four animation styles (slide, push, reveal, fade)
* Color customization (background, links, hover states, burger icon)
* Fixed header support with separate burger color
* Customizable overlay with color and opacity controls
* Support for nested submenus with smooth animations
* Optional contact information display
* Header height management to prevent layout shift
* Admin preview of all settings
* Accessibility-friendly with ARIA attributes and keyboard navigation
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
* Menu position (left or right)
* Animation style (slide, push, reveal, fade)
* Menu width percentage
* Burger icon style (hamburger, dots, arrow)

**Header Settings:**
* Header height (maintains layout when Divi's mobile nav is hidden)
* Top header height (for secondary headers)
* Fixed header class (watches for Divi's fixed header state)

**Color Settings:**
* Background color of the menu panel
* Link color
* Link hover color
* Burger icon color (closed state)
* Burger icon color (open/active state)
* Burger icon color (fixed header state)
* Overlay color
* Overlay opacity

**Contact Information (Optional):**
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
    --dsmm-link-hover-color: #your-color;
    --dsmm-burger-color: #your-color;
    --dsmm-burger-open-color: #your-color;
    --dsmm-burger-fixed-color: #your-color;
    --dsmm-overlay-color: #your-color;
    --dsmm-overlay-opacity: 0.8;
    --dsmm-transition-speed: 0.3s;
    --dsmm-menu-width: 80%;
}
```

= How do I change the menu position? =

Go to Settings > Mobile Menu and select either "Left" or "Right" from the Position dropdown.

= What animation styles are available? =

* **Slide** - Menu slides in from the side over the content
* **Push** - Menu pushes the page content as it opens
* **Reveal** - Page slides away to reveal the menu underneath
* **Fade** - Menu fades in from the side

= How does the fixed header color work? =

The plugin watches for Divi's `.et-fixed-header` class (or a custom class you specify) and automatically changes the burger icon color when the fixed header is active. This works even though the burger is rendered outside the header element.

= Why is there a header height setting? =

When the plugin hides Divi's default mobile navigation, the header can collapse. The header height setting maintains the header's height to prevent layout shift.

== Changelog ==

= 1.0.0 =
* Initial release
* Multiple burger icon styles (hamburger, dots, arrow)
* Left/right position options
* Four animation styles (slide, push, reveal, fade)
* Fixed header color support with MutationObserver
* Customizable overlay
* Header height management
* Live admin preview
* Full accessibility support

== Upgrade Notice ==

= 1.0.0 =
Initial release of Divi Simple Mobile Menu.
