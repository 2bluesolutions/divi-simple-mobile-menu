/**
 * Divi Simple Mobile Menu - JavaScript
 *
 * @package Divi_Simple_Mobile_Menu
 */

(function() {
    'use strict';

    const DSMM = {
        // Settings from WordPress
        settings: {
            menuSelector: '#dsmm-mobile-menu',
            burgerSelector: '#dsmm-burger',
            overlaySelector: '#dsmm-overlay',
            closeSelector: '#dsmm-close',
            parentButtonSelector: '.dsmm-parent-button',
            submenuSelector: '.dsmm-sub-menu',
            openClass: 'dsmm-open',
            menuOpenClass: 'dsmm-menu-open',
            bodyOpenClass: 'dsmm-body-menu-open',
            overlayVisibleClass: 'dsmm-overlay-visible',
            burgerFixedClass: 'dsmm-burger-fixed',
            breakpoint: 981,
            position: 'right',
            animation: 'slide',
            fixedHeaderClass: 'et-fixed-header',
        },

        // DOM elements
        elements: {
            menu: null,
            burger: null,
            overlay: null,
            closeBtn: null,
            body: null,
        },

        /**
         * Initialize the mobile menu
         */
        init() {
            // Merge with WordPress options if available
            if (typeof dsmmOptions !== 'undefined') {
                this.settings.breakpoint = dsmmOptions.breakpoint || this.settings.breakpoint;
                this.settings.position = dsmmOptions.position || this.settings.position;
                this.settings.animation = dsmmOptions.animation || this.settings.animation;
                this.settings.menuSelector = '#' + (dsmmOptions.menuId || 'dsmm-mobile-menu');
                this.settings.burgerSelector = '#' + (dsmmOptions.burgerId || 'dsmm-burger');
                this.settings.fixedHeaderClass = dsmmOptions.fixedHeaderClass || this.settings.fixedHeaderClass;
            }

            // Get DOM elements
            this.elements.menu = document.querySelector(this.settings.menuSelector);
            this.elements.burger = document.querySelector(this.settings.burgerSelector);
            this.elements.overlay = document.querySelector(this.settings.overlaySelector);
            this.elements.closeBtn = document.querySelector(this.settings.closeSelector);
            this.elements.body = document.body;

            if (!this.elements.menu || !this.elements.burger) {
                console.warn('DSMM: Required elements not found');
                return;
            }

            this.bindEvents();
            this.handleResize();
            this.watchFixedHeader();
        },

        /**
         * Bind all event listeners
         */
        bindEvents() {
            // Burger toggle
            this.elements.burger.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleMenu();
            });

            // Close button
            if (this.elements.closeBtn) {
                this.elements.closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.closeMenu();
                });
            }

            // Overlay click closes menu
            if (this.elements.overlay) {
                this.elements.overlay.addEventListener('click', () => {
                    this.closeMenu();
                });
            }

            // Submenu toggles
            const parentButtons = this.elements.menu.querySelectorAll(this.settings.parentButtonSelector);
            parentButtons.forEach((button) => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleSubmenu(button);
                });
            });

            // Menu links - close menu on click
            const menuLinks = this.elements.menu.querySelectorAll('a');
            menuLinks.forEach((link) => {
                link.addEventListener('click', () => {
                    this.closeMenu();
                });
            });

            // Outside click closes menu
            document.addEventListener('click', (e) => {
                if (this.isMenuOpen()) {
                    const isClickInside = this.elements.menu.contains(e.target) || 
                                         this.elements.burger.contains(e.target);
                    if (!isClickInside) {
                        this.closeMenu();
                    }
                }
            });

            // Escape key closes menu
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isMenuOpen()) {
                    this.closeMenu();
                    this.elements.burger.focus();
                }
            });

            // Handle window resize
            window.addEventListener('resize', this.debounce(() => {
                this.handleResize();
            }, 150));

            // Close submenus on scroll (optional)
            window.addEventListener('scroll', this.debounce(() => {
                if (window.scrollY > 100) {
                    this.closeAllSubmenus();
                }
            }, 100));
        },

        /**
         * Toggle menu open/closed
         */
        toggleMenu() {
            if (this.isMenuOpen()) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        },

        /**
         * Open the menu
         */
        openMenu() {
            this.elements.menu.classList.add(this.settings.menuOpenClass);
            this.elements.burger.classList.add(this.settings.openClass);
            this.elements.body.classList.add(this.settings.bodyOpenClass);
            this.elements.burger.setAttribute('aria-expanded', 'true');
            
            // Show overlay
            if (this.elements.overlay) {
                this.elements.overlay.classList.add(this.settings.overlayVisibleClass);
            }
            
            // Trap focus inside menu
            this.trapFocus();
        },

        /**
         * Close the menu
         */
        closeMenu() {
            this.elements.menu.classList.remove(this.settings.menuOpenClass);
            this.elements.burger.classList.remove(this.settings.openClass);
            this.elements.body.classList.remove(this.settings.bodyOpenClass);
            this.elements.burger.setAttribute('aria-expanded', 'false');
            
            // Hide overlay
            if (this.elements.overlay) {
                this.elements.overlay.classList.remove(this.settings.overlayVisibleClass);
            }
            
            // Close all submenus too
            this.closeAllSubmenus();
        },

        /**
         * Check if menu is open
         */
        isMenuOpen() {
            return this.elements.menu.classList.contains(this.settings.menuOpenClass);
        },

        /**
         * Toggle a submenu
         */
        toggleSubmenu(button) {
            const parentLi = button.closest('.menu-item-has-children');
            const isOpen = parentLi.classList.contains(this.settings.openClass);

            // Close other open submenus at the same level
            const siblings = parentLi.parentElement.querySelectorAll(':scope > .menu-item-has-children.' + this.settings.openClass);
            siblings.forEach((sibling) => {
                if (sibling !== parentLi) {
                    sibling.classList.remove(this.settings.openClass);
                    const siblingButton = sibling.querySelector(this.settings.parentButtonSelector);
                    if (siblingButton) {
                        siblingButton.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // Toggle current submenu
            if (isOpen) {
                parentLi.classList.remove(this.settings.openClass);
                button.setAttribute('aria-expanded', 'false');
            } else {
                parentLi.classList.add(this.settings.openClass);
                button.setAttribute('aria-expanded', 'true');
            }
        },

        /**
         * Close all submenus
         */
        closeAllSubmenus() {
            const openSubmenus = this.elements.menu.querySelectorAll('.menu-item-has-children.' + this.settings.openClass);
            openSubmenus.forEach((submenu) => {
                submenu.classList.remove(this.settings.openClass);
                const button = submenu.querySelector(this.settings.parentButtonSelector);
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        },

        /**
         * Handle window resize - close menu if above breakpoint
         */
        handleResize() {
            const windowWidth = window.innerWidth;
            
            if (windowWidth > this.settings.breakpoint) {
                this.closeMenu();
            }
        },

        /**
         * Trap focus inside menu when open (accessibility)
         */
        trapFocus() {
            const focusableElements = this.elements.menu.querySelectorAll(
                'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            // Focus first element
            firstElement.focus();

            // Trap focus on Tab
            this.elements.menu.addEventListener('keydown', (e) => {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            });
        },

        /**
         * Debounce helper
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Watch for fixed header class on the header element
         * Uses MutationObserver to detect when Divi adds/removes the fixed header class
         */
        watchFixedHeader() {
            const fixedClass = this.settings.fixedHeaderClass;
            if (!fixedClass) return;

            // Find the header element - try common Divi selectors
            const header = document.querySelector('#main-header') || 
                          document.querySelector('.et-l--header') ||
                          document.querySelector('header');
            
            if (!header) {
                console.warn('DSMM: Header element not found for fixed header watching');
                return;
            }

            // Check initial state
            this.updateFixedState(header.classList.contains(fixedClass));

            // Watch for class changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        this.updateFixedState(header.classList.contains(fixedClass));
                    }
                });
            });

            observer.observe(header, {
                attributes: true,
                attributeFilter: ['class']
            });
        },

        /**
         * Update burger button fixed state
         */
        updateFixedState(isFixed) {
            if (!this.elements.burger) return;
            
            if (isFixed) {
                this.elements.burger.classList.add(this.settings.burgerFixedClass);
            } else {
                this.elements.burger.classList.remove(this.settings.burgerFixedClass);
            }
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => DSMM.init());
    } else {
        DSMM.init();
    }

    // Export for external access if needed
    window.DSMM = DSMM;

})();
