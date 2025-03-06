jQuery(document).ready(function($) {
    console.log('JavaScript loaded'); // Debugging statement

    $('#cmbsolver-pattern-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted'); // Debugging statement

        var word = $('#word').val();
        console.log('Word:', word); // Debugging statement
        console.log('URL:', cmbsolverPatternApi.ajax_url + word); // Debugging statement
        $.ajax({
            url: cmbsolverPatternApi.ajax_url + word,
            method: 'GET',
            data: {
                action: 'cmbsolver_pattern_request',
                word: word
            },
            success: function(response) {
                console.log('AJAX response:', response); // Debugging statement
                var data = response;

                function renderTable() {
                    var table = '<div style="overflow-x:auto;"><table>';
                    table += '<tr>';
                    table += '<td><b><u>Word</u></b></td>';
                    table += '<td><b><u>Pattern</u></b></td>';
                    table += '</tr>';
                    table += '<tr>';
                    table += '<td>' + data.wordToPattern + '</td>';
                    table += '<td>' + data.pattern + '</td>';
                    table += '</tr>';
                    table += '</table></div>';
                    $('#cmbsolver-pattern-response').html(table);
                }
                renderTable();
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error); // Debugging statement
                $('#cmbsolver-pattern-response').html('<p>AJAX request failed: ' + error + '</p>');
            }
        });
    });
});