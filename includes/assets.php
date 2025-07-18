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

    // Enqueue bundled core CSS
    $core_bundle_path = '/assets/dist/core.bundle.css';
    if (file_exists($theme_dir . $core_bundle_path)) {
        wp_enqueue_style(
            'gp-core-bundle',
            get_stylesheet_directory_uri() . $core_bundle_path,
            [],
            filemtime($theme_dir . $core_bundle_path)
        );
    }

    wp_enqueue_style('generatepress-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('gp-child-style',
        get_stylesheet_uri(),
        array('generatepress-style', 'gp-core-bundle'),
        file_exists($theme_dir . '/style.css') ? filemtime($theme_dir . '/style.css') : $theme_version
    );

    // Enqueue layout bundle
    $layout_bundle_path = '/assets/dist/layout.bundle.css';
    if (file_exists($theme_dir . $layout_bundle_path)) {
        wp_enqueue_style(
            'gp-layout-bundle',
            get_stylesheet_directory_uri() . $layout_bundle_path,
            ['gp-core-bundle'],
            filemtime($theme_dir . $layout_bundle_path)
        );
    }

    // Enqueue remaining individual CSS files
    $css_files = [
        'components-content' => '/assets/css/components/content.css',
        'components-dark_mode' => '/assets/css/components/dark_mode.css',
        'components-language-switcher-partial' => '/assets/css/components/language-switcher-partial.css',
        'components-language-switcher' => '/assets/css/components/language-switcher.css',
        'components-post-navigation' => '/assets/css/components/post-navigation.css',
        'ads' => '/components/ads/ads.css',
        'back-to-top' => '/assets/css/components/back-to-top.css',
    ];

    $last_style_handle = 'gp-layout-bundle';

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

    if (file_exists($theme_dir . '/assets/js/vendor/clamp.min.js')) {
        wp_enqueue_script('clamp-js',
            get_stylesheet_directory_uri() . '/assets/js/vendor/clamp.min.js',
            array(),
            '0.5.1',
            true
        );
    }

    if (file_exists($theme_dir . '/assets/js/main.js')) {
        wp_enqueue_script('gp-main-script',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            array('jquery', 'clamp-js'),
            filemtime($theme_dir . '/assets/js/main.js'),
            true
        );
    }

    // Conditionally enqueue TOC CSS
    if (is_singular('post')) {
        $toc_path = '/assets/css/components/table-of-contents.css';
        if (file_exists($theme_dir . $toc_path)) {
            wp_enqueue_style(
                'gp-toc-style',
                get_stylesheet_directory_uri() . $toc_path,
                [$last_style_handle],
                filemtime($theme_dir . $toc_path)
            );
            $last_style_handle = 'gp-toc-style';
        }
    }

    // Conditionally enqueue Series CSS
    if (is_singular('post') || is_singular('series') || is_tax('series_category')) {
        $series_path = '/assets/css/components/series.css';
        if (file_exists($theme_dir . $series_path)) {
            wp_enqueue_style(
                'gp-series-style',
                get_stylesheet_directory_uri() . $series_path,
                [$last_style_handle],
                filemtime($theme_dir . $series_path)
            );
        }
    }

    // Conditionally enqueue Comments CSS
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

    // Enqueue YARPP custom CSS
    if (is_singular()) {
        $yarpp_custom_css_path = '/yarpp-custom.css';
        if (file_exists($theme_dir . $yarpp_custom_css_path)) {
            wp_enqueue_style(
                'gp-yarpp-custom-style',
                get_stylesheet_directory_uri() . $yarpp_custom_css_path,
                ['gp-series-style'],
                filemtime($theme_dir . $yarpp_custom_css_path)
            );
        }
    }



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
        // Ad settings
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


    $custom_css = ':root { --theme-version: "' . esc_attr($theme_version) . '"; }';
    wp_add_inline_style('gp-child-style', $custom_css);

    // Google AdSense 스크립트 비동기 로드
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

/**
 * Add inline script to head for dark mode flickering prevention.
 */
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

/**
 * 스크립트 태그에 async 또는 module type 속성을 추가합니다.
 *
 * @param string $tag    The <script> tag for the enqueued script.
 * @param string $handle The script's handle.
 * @param string $src    The script's source URL.
 * @return string Modified <script> tag.
 */
function gp_add_script_attributes( $tag, $handle, $src ) {
    // 애드센스 스크립트에 async 속성 추가
    if ( 'google-adsense' === $handle ) {
        return str_replace( ' src', ' async src', $tag );
    }
    // 메인 스크립트에 module 타입 추가
    if ( 'gp-main-script' === $handle ) {
        return '<script type="module" src="' . esc_url( $src ) . '" id="gp-main-script-js"></script>';
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'gp_add_script_attributes', 10, 3 );
