<?php

namespace Pickups\Helpers;

class Asset {
    // Class implementation goes here

    public static function enqueue_styles() {
        wp_enqueue_style('pickups-styles', plugins_url('../assets/css/pickups-styles.css', __FILE__));
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('pickups-scripts', plugins_url('../assets/js/pickups-scripts.js', __FILE__), ['jquery'], null, true);
    }

    public static function enqueue_all() {
        self::enqueue_styles();
        self::enqueue_scripts();
    }
}