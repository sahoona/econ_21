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

    // Base GeneratePress Style
    wp_enqueue_style('generatepress-style', get_template_directory_uri() . '/style.css');

    // Child theme style.css (for theme information, can be empty)
    wp_enqueue_style('gp-child-style',
        get_stylesheet_uri(),
        ['generatepress-style'],
        file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : $theme_version
    );

    // --- Bundled CSS Files ---
    $last_style_handle = 'gp-child-style'; // Start dependency chain from child theme's style.css

    // Core Bundle
    $core_bundle_path = '/assets/dist/core.bundle.css';
    if (file_exists($theme_dir . $core_bundle_path)) {
        wp_enqueue_style(
            'gp-core-bundle',
            get_stylesheet_directory_uri() . $core_bundle_path,
            [$last_style_handle],
            filemtime($theme_dir . $core_bundle_path)
        );
        $last_style_handle = 'gp-core-bundle';
    }

    // Layout Bundle
    $layout_bundle_path = '/assets/dist/layout.bundle.css';
    if (file_exists($theme_dir . $layout_bundle_path)) {
        wp_enqueue_style(
            'gp-layout-bundle',
            get_stylesheet_directory_uri() . $layout_bundle_path,
            [$last_style_handle],
            filemtime($theme_dir . $layout_bundle_path)
        );
        $last_style_handle = 'gp-layout-bundle';
    }

    // Components Bundle
    $components_bundle_path = '/assets/dist/components.bundle.css';
    if (file_exists($theme_dir . $components_bundle_path)) {
        wp_enqueue_style(
            'gp-components-bundle',
            get_stylesheet_directory_uri() . $components_bundle_path,
            [$last_style_handle],
            filemtime($theme_dir . $components_bundle_path)
        );
        $last_style_handle = 'gp-components-bundle';
    }

    // Content Bundle
    $content_bundle_path = '/assets/dist/content.bundle.css';
    if (file_exists($theme_dir . $content_bundle_path)) {
        wp_enqueue_style(
            'gp-content-bundle',
            get_stylesheet_directory_uri() . $content_bundle_path,
            [$last_style_handle],
            filemtime($theme_dir . $content_bundle_path)
        );
        $last_style_handle = 'gp-content-bundle';
    }


    // --- Remaining Individual CSS Files (should be none after all bundling is complete) ---
    $css_files = [
        // All files should be moved to bundles. This array can be removed once all are bundled.
    ];

    foreach ($css_files as $handle => $path) {
        if (file_exists($theme_dir . $path)) {
            $handle = 'gp-' . $handle . '-style';
            wp_enqueue_style(
                $handle,
                get_stylesheet_directory_uri() . $path,
                [$last_style_handle],
                filemtime($theme_dir . $path)
            );
            $last_style_handle = $handle;
        }
    }

    // --- Conditionally Enqueued CSS ---

    // TOC CSS
    if (is_singular('post')) {
        $toc_path = '/assets/css/components/table-of-contents.css';
        if (file_exists($theme_dir . $toc_path)) {
            wp_enqueue_style(
                'gp-toc-style',
                get_stylesheet_directory_uri() . $toc_path,
                [$last_style_handle],
                filemtime($theme_dir . $toc_path)
            );
        }
    }

    // Series & YARPP CSS
    if (is_singular('post') || is_singular('series') || is_tax('series_category')) {
        $series_path = '/assets/css/components/series.css';
        if (file_exists($theme_dir . $series_path)) {
            wp_enqueue_style(
                'gp-series-style',
                get_stylesheet_directory_uri() . $series_path,
                [$last_style_handle],
                filemtime($theme_dir . $series_path)
            );
            $last_style_handle = 'gp-series-style'; // YARPP depends on this
        }

        $yarpp_custom_css_path = '/yarpp-custom.css';
        if (file_exists($theme_dir . $yarpp_custom_css_path)) {
            wp_enqueue_style(
                'gp-yarpp-custom-style',
                get_stylesheet_directory_uri() . $yarpp_custom_css_path,
                [$last_style_handle], // Now correctly depends on the latest handle
                filemtime($theme_dir . $yarpp_custom_css_path)
            );
        }
    }

    // Comments CSS
    if (is_singular() && comments_open()) {
        $comments_path = '/assets/css/components/comments.css';
        if (file_exists($theme_dir . $comments_path)) {
            wp_enqueue_style(
                'gp-comments-style',
                get_stylesheet_directory_uri() . $comments_path,
                [$last_style_handle],
                filemtime($theme_dir . $comments_path)
            );
        }
    }

    // --- JavaScript Files ---

    if (file_exists($theme_dir . '/assets/js/vendor/clamp.min.js')) {
        wp_enqueue_script('clamp-js',
            get_stylesheet_directory_uri() . '/assets/js/vendor/clamp.min.js',
            [],
            '0.5.1',
            true
        );
    }

    if (file_exists($theme_dir . '/assets/js/main.js')) {
        wp_enqueue_script('gp-main-script',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            ['jquery', 'clamp-js'],
            filemtime($theme_dir . '/assets/js/main.js'),
            true
        );
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
