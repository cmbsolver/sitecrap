<?php
/*
Plugin Name: CMB Solver Pattern Plugin
Description: A plugin to interact with the CMB Solver Pattern API.
Version: 1.0
Author: THE CMBSOLVER
*/

function cmbsolver_pattern_plugin_enqueue_scripts() {
    wp_enqueue_script('cmbsolver-pattern-plugin-script', plugin_dir_url(__FILE__) . 'cmbsolver-pattern-plugin.js', array('jquery'), null, true);
    wp_localize_script('cmbsolver-pattern-plugin-script', 'cmbsolverPatternApi', array('ajax_url' => 'https://cmbsolver.com/cmbsolver-api/runewords.php/wordpattern/'));
}
add_action('wp_enqueue_scripts', 'cmbsolver_pattern_plugin_enqueue_scripts');

function cmbsolver_pattern_plugin_shortcode() {
    ob_start();
    ?>
    <form id="cmbsolver-pattern-form">
        <label for="word">Enter Word:</label>
        <input type="text" id="word" name="word" required>
        <button type="submit">Get Pattern</button>
    </form>
    <div id="cmbsolver-pattern-response"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('cmbsolver_pattern_form', 'cmbsolver_pattern_plugin_shortcode');

function cmbsolver_pattern_plugin_handle_request() {
    $word = sanitize_text_field($_POST['word']);
    $pattern_url = "https://cmbsolver.com/cmbsolver-api/runewords.php/wordpattern/{$word}";

    $response = wp_remote_get($pattern_url);
    if (is_wp_error($response)) {
        wp_send_json_error('API request failed.');
    } else {
        $body = wp_remote_retrieve_body($response);
        wp_send_json_success(json_decode($body, true));
    }
}
add_action('wp_ajax_nopriv_cmbsolver_pattern_request', 'cmbsolver_pattern_plugin_handle_request');
add_action('wp_ajax_cmbsolver_pattern_request', 'cmbsolver_pattern_plugin_handle_request');