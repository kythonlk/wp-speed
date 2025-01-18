<?php
function enqueue_vite_assets() {
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        error_log("Manifest file not found: " . $manifest_path);
        return;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);

    if (!$manifest || !isset($manifest['index.html']['file'])) {
        error_log("Invalid manifest file structure.");
        return;
    }

    // Enqueue the main JS file
    $main_js = $manifest['index.html']['file'];
    wp_enqueue_script(
        'vite-main',
        get_template_directory_uri() . '/dist/' . $main_js,
        [],
        null,
        true
    );

    // Enqueue the CSS file if available
    if (isset($manifest['index.html']['css'][0])) {
        $main_css = $manifest['index.html']['css'][0];
        wp_enqueue_style(
            'vite-style',
            get_template_directory_uri() . '/dist/' . $main_css,
            [],
            null
        );
    }

    // Enqueue images and other assets
    if (isset($manifest['index.html']['assets'])) {
        foreach ($manifest['index.html']['assets'] as $asset) {
            wp_enqueue_script(
                'vite-asset-' . basename($asset),
                get_template_directory_uri() . '/dist/' . $asset,
                [],
                null,
                false
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_vite_assets');
