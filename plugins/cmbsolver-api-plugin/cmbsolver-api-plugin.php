<?php
/*
Plugin Name: CMB Solver API Plugin
Description: A plugin to interact with the CMB Solver API.
Version: 1.3
Author: THE CMBSOLVER
*/

function cmbsolver_api_plugin_enqueue_scripts() {
    wp_enqueue_script('cmbsolver-api-plugin-script', plugin_dir_url(__FILE__) . 'cmbsolver-api-plugin.js', array('jquery'), null, true);
    wp_localize_script('cmbsolver-api-plugin-script', 'cmbsolverApi', array('ajax_url' => 'https://cmbsolver.com/cmbsolver-api/runewords.php'));
}
add_action('wp_enqueue_scripts', 'cmbsolver_api_plugin_enqueue_scripts');

function cmbsolver_api_plugin_shortcode() {
    ob_start();
    ?>
    <form id="cmbsolver-api-form">
        <label for="word">Enter Value:</label>
        <input type="text" id="word" name="word" required>
        <label for="endpoint">Lookup By Field:</label>
        <select id="endpoint" name="endpoint">
            <option value="gem_sum">Gematria Sum</option>
            <option value="gem_product">Gematria Product</option>
            <option value="dict_word_length">Word Length</option>
            <option value="dict_runeglish_length">Runeglish Length</option>
            <option value="dict_rune_length">Rune Length</option>
            <option value="rune_pattern">Rune Pattern</option>
            <option value="rune_pattern_no_doublet">Rune Pattern (no doublet)</option>
        </select>
        <label for="dataset">Dataset:</label>
        <select id="dataset" name="dataset">
            <option value="db">Default</option>
            <option value="norvig">Norvig</option>
            <option value="10k">10k</option>
            <option value="20k">20k</option>
            <option value="db_reversed">Default (Reversed)</option>
            <option value="norvig_reversed">Norvig (Reversed)</option>
            <option value="10k_reversed">10k (Reversed)</option>
            <option value="20k_reversed">20k (Reversed)</option>
        </select>
        <button type="submit">Submit</button>
        <button type="button" id="download-csv">Download CSV</button>
    </form>
    <div id="cmbsolver-api-response"></div>
    <div id="cmbsolver-api-pagination"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('cmbsolver_api_form', 'cmbsolver_api_plugin_shortcode');

function cmbsolver_api_plugin_handle_request() {
    $word = sanitize_text_field($_POST['word']);
    $endpoint = sanitize_text_field($_POST['endpoint']);
    $database = sanitize_text_field($_POST['dataset']);
    $api_url = "https://cmbsolver.com/cmbsolver-api/runewords.php/{$endpoint}/{$database}/{$word}";

    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        wp_send_json_error('API request failed.');
    } else {
        $body = wp_remote_retrieve_body($response);
        wp_send_json_success(json_decode($body, true));
    }
}
add_action('wp_ajax_nopriv_cmbsolver_api_request', 'cmbsolver_api_plugin_handle_request');
add_action('wp_ajax_cmbsolver_api_request', 'cmbsolver_api_plugin_handle_request');