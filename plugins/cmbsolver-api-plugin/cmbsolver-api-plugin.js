jQuery(document).ready(function($) {
    console.log('JavaScript loaded'); // Debugging statement

    $('#cmbsolver-api-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted'); // Debugging statement

        var word = $('#word').val();
        var endpoint = $('#endpoint').val();
        console.log('Word:', word); // Debugging statement
        console.log('Endpoint:', endpoint); // Debugging statement
        console.log('URL:', cmbsolverApi.ajax_url); // Debugging statement
        $.ajax({
            url: cmbsolverApi.ajax_url + '/' + endpoint + '/' + word,
            method: 'GET',
            success: function(response) {
                console.log('AJAX response:', response); // Debugging statement

                var data = response;
                console.log('Data length:', data.length); // Moved after data definition

                var table = '<table>';
                table += '<tr>';
                table += '<td><b><u>Id</u></b></td>';
                table += '<td><b><u>Word</u></b></td>';
                table += '<td><b><u>Runeglish</u></b></td>';
                table += '<td><b><u>Runes</u></b></td>';
                table += '<td><b><u>Rune (no doublet)</u></b></td>';
                table += '<td><b><u>Gematria Sum</u></b></td>';
                table += '<td><b><u>Gematria Sum Is Prime</u></b></td>';
                table += '<td><b><u>Gematria Product</u></b></td>';
                table += '<td><b><u>Gematria Product Is Prime</u></b></td>';
                table += '<td><b><u>Word Length</u></b></td>';
                table += '<td><b><u>Runeglish Length</u></b></td>';
                table += '<td><b><u>Rune Length</u></b></td>';
                table += '<td><b><u>Rune Pattern</u></b></td>';
                table += '<td><b><u>Rune Pattern (no doublet)</u></b></td>';
                table += '<td><b><u>Language</u></b></td>';
                table += '</tr>';

                for (var i = 0; i < data.length; i++) {
                    table += '<tr>';
                    for (var key in data[i]) {
                        table += '<td>' + data[i][key] + '</td>';
                    }
                    table += '</tr>';
                }
                table += '</table>';
                $('#cmbsolver-api-response').html(table);
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error); // Debugging statement
                $('#cmbsolver-api-response').html('<p>AJAX request failed: ' + error + '</p>');
            }
        });
    });
});