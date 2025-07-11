<?php
/*
Plugin Name: CMB Solver Rune Donkey Plugin
Description: A plugin to generate Excel files based on user input.
Version: 1.2
Author: THE CMBSOLVER
*/

// Enqueue scripts and styles
function cmbsolver_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('cmbsolver-rune-donkey-plugin', plugins_url('/cmbsolver-rune-donkey-plugin.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'cmbsolver_enqueue_scripts');

// Create shortcode for the form
function cmbsolver_generate_excel_form() {
    ob_start();
    ?>
    <form id="generateExcelForm">
        <label for="text">Text:</label>
        <textarea id="text" name="text" required ></textarea><br><br>

        <label for="textType">Text Type:</label>
        <select id="textType" name="text_type" required>
            <option value="latin">Latin</option>
            <option value="runeglish">Runeglish</option>
            <option value="runes">Runes</option>
        </select><br><br>

        <label for="action">Action:</label>
        <select id="action" name="action" required>
            <option value="gem_sum">Gematria Sum</option>
            <option value="dict_word_length">Word Length</option>
            <option value="dict_rune_length">Rune Length</option>
            <option value="dict_runeglish_length">Runeglish Length</option>
            <option value="rune_pattern">Rune Pattern</option>
            <option value="rune_pattern_no_doublet">Rune Pattern (no doublet)</option>
        </select><br><br>

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
        </select><br><br>

        <input type="checkbox" id="reverse" name="reverse">
        <label for="reverse">Reverse Words For Calculation</label><br><br>

        <button type="submit">Download</button>
    </form>
    <div id="cmbsolver-rune-donkey-plugin-api-response"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('cmbsolver_generate_excel', 'cmbsolver_generate_excel_form');