<?php
/**
 * Enqueue assets
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Enqueue scripts and styles
function gp_child_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');
    $theme_dir = get_stylesheet_directory();
    $is_dev_mode = defined('WP_DEBUG') && WP_DEBUG;

    // --- Base Styles ---
    wp_enqueue_style('generatepress-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('gp-child-style', get_stylesheet_uri(), ['generatepress-style'], file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : $theme_version);

    // --- CSS Loading Strategy ---
    if ($is_dev_mode) {
        // DEVELOPMENT MODE: Load all individual CSS files for easier debugging.
        $dev_css_files = [
            // Core
            'variables'   => '/assets/css/components/variables.css',
            'fonts'       => '/assets/css/components/fonts.css',
            'main'        => '/assets/css/main.css',
            // Layout
            'layout'      => '/assets/css/layout.css',
            'c-layout'    => '/assets/css/components/layout.css',
            'header'      => '/assets/css/components/header.css',
            'sidebar'     => '/assets/css/components/sidebar.css',
            'responsive'  => '/assets/css/components/responsive.css',
            // Components
            'dark_mode'   => '/assets/css/components/dark_mode.css',
            'lang-switcher' => '/assets/css/components/language-switcher.css',
            'lang-switcher-p' => '/assets/css/components/language-switcher-partial.css',
            'back-to-top' => '/assets/css/components/back-to-top.css',
            'ads'         => '/components/ads/ads.css',
            // Content
            'content'     => '/assets/css/components/content.css',
            'post-nav'    => '/assets/css/components/post-navigation.css',
            // Conditional
            'toc'         => '/assets/css/components/table-of-contents.css',
            'series'      => '/assets/css/components/series.css',
            'comments'    => '/assets/css/components/comments.css',
            'yarpp'       => '/yarpp-custom.css',
        ];

        $last_handle = 'gp-child-style';
        foreach ($dev_css_files as $handle => $path) {
            if (file_exists($theme_dir . $path)) {
                wp_enqueue_style('gp-dev-' . $handle, get_stylesheet_directory_uri() . $path, [$last_handle], filemtime($theme_dir . $path));
                $last_handle = 'gp-dev-' . $handle;
            }
        }

    } else {
        // PRODUCTION MODE: Load bundled and minified CSS files.
        $base_dependency = 'gp-child-style';
        $bundles = [
            'gp-core-bundle'        => '/assets/dist/core.bundle.css',
            'gp-layout-bundle'      => '/assets/dist/layout.bundle.css',
            'gp-components-bundle'  => '/assets/dist/components.bundle.css',
            'gp-content-bundle'     => '/assets/dist/content.bundle.css',
        ];

        foreach ($bundles as $handle => $path) {
            wp_enqueue_style($handle, get_stylesheet_directory_uri() . $path, [$base_dependency], $theme_version);
        }

        if (is_singular()) {
            wp_enqueue_style('gp-conditional-bundle', get_stylesheet_directory_uri() . '/assets/dist/conditional.bundle.css', [$base_dependency], $theme_version);
        }
    }

    // --- JavaScript Files ---
    if (file_exists($theme_dir . '/assets/js/vendor/clamp.min.js')) {
        wp_enqueue_script('clamp-js', get_stylesheet_directory_uri() . '/assets/js/vendor/clamp.min.js', [], '0.5.1', true);
    }
    if (file_exists($theme_dir . '/assets/js/main.js')) {
        wp_enqueue_script('gp-main-script', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery', 'clamp-js'], filemtime($theme_dir . '/assets/js/main.js'), true);
    }

    // --- Localized Data for JS ---
    $localized_data = [
		'ajax_url'        => admin_url('admin-ajax.php'),
		'reactions_nonce' => wp_create_nonce('gp_reactions_nonce'),
		'star_rating_nonce' => wp_create_nonce('gp_star_rating_nonce'),
        'load_more_posts_nonce' => wp_create_nonce('load_more_posts_nonce'),
        'load_more_series_nonce' => wp_create_nonce('load_more_series_nonce'),
        'currentPageId' => 0,
        'currentPostType' => 'unknown',
        'isFrontPage' => is_front_page(),
        'isHome' => is_home(),
        'ads_enabled' => get_theme_mod('econarc_ads_enabled', false),
        'top_ad_enabled' => get_theme_mod('econarc_top_ad_enabled', false),
        'infeed_ad_enabled' => get_theme_mod('econarc_infeed_ad_enabled', false),
        'ad_client' => get_theme_mod('econarc_ad_client'),
        'ad_slot' => get_theme_mod('econarc_ad_slot'),
        'top_ad_slot' => get_theme_mod('econarc_top_ad_slot'),
        'infeed_ad_slot' => get_theme_mod('econarc_infeed_ad_slot')
	];

    $current_post_object = get_queried_object();
    if ( $current_post_object instanceof WP_Post ) {
        $localized_data['currentPostType'] = $current_post_object->post_type;
        if ( $localized_data['currentPostType'] === 'page' ) {
            $localized_data['currentPageId'] = $current_post_object->ID;
        }
    } elseif ( is_home() ) {
        $localized_data['currentPostType'] = 'home';
    } elseif ( is_front_page() && !is_home() ) {
        $localized_data['currentPostType'] = 'front-page';
        if ( $current_post_object instanceof WP_Post && $current_post_object->post_type === 'page' ) {
            $localized_data['currentPageId'] = $current_post_object->ID;
        }
    } elseif ( is_archive() ) {
        $localized_data['currentPostType'] = 'archive';
    } elseif ( is_search() ) {
        $localized_data['currentPostType'] = 'search';
    }

    if (is_singular('post')) {
        $post_id = get_the_ID();
        $toc_settings = [];
        $levels = ['h2', 'h3', 'h4', 'h5', 'h6'];
        $defaults = ['h2' => 'on', 'h3' => 'on', 'h4' => 'off', 'h5' => 'off', 'h6' => 'off'];

        foreach ($levels as $level) {
            $meta_key = '_gp_toc_include_' . $level;
            $saved_value = get_post_meta($post_id, $meta_key, true);
            if ($saved_value === '') {
                $toc_settings[$level] = $defaults[$level];
            } else {
                $toc_settings[$level] = $saved_value;
            }
        }
        $localized_data['toc_settings'] = $toc_settings;
    } else {
        $default_toc_settings = [];
        $levels = ['h2', 'h3', 'h4', 'h5', 'h6'];
        $defaults = ['h2' => 'on', 'h3' => 'on', 'h4' => 'off', 'h5' => 'off', 'h6' => 'off'];
        foreach ($levels as $level) {
            $default_toc_settings[$level] = $defaults[$level];
        }
        $localized_data['toc_settings'] = $default_toc_settings;
    }

    wp_localize_script('gp-main-script', 'gp_settings', $localized_data);

    // --- Inline & Async Scripts ---
    $custom_css = ':root { --theme-version: "' . esc_attr($theme_version) . '"; }';
    wp_add_inline_style('gp-child-style', $custom_css);

    $ad_client = get_theme_mod('econarc_ad_client');
    if ( ! empty( trim( $ad_client ) ) ) {
        wp_enqueue_script(
            'google-adsense',
            'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-' . esc_attr( trim( $ad_client ) ),
            [],
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'gp_child_enqueue_assets');

function gp_child_dark_mode_flicker_prevention() {
    ?>
    <script>
        (function() {
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) {
                document.documentElement.classList.add('dark-mode-active');
            }
        })();
    </script>
    <?php
}
add_action( 'wp_head', 'gp_child_dark_mode_flicker_prevention', 0 );

function gp_add_script_attributes( $tag, $handle, $src ) {
    if ( 'google-adsense' === $handle ) {
        return str_replace( ' src', ' async src', $tag );
    }
    if ( 'gp-main-script' === $handle ) {
        return '<script type="module" src="' . esc_url( $src ) . '" id="gp-main-script-js"></script>';
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'gp_add_script_attributes', 10, 3 );
