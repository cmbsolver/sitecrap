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

                var rowsPerPage = 25;
                var currentPage = 1;
                var totalPages = Math.ceil(data.length / rowsPerPage);

                function renderTable(page) {
                    var start = (page - 1) * rowsPerPage;
                    var end = start + rowsPerPage;
                    var paginatedData = data.slice(start, end);

                    var table = '<div style="overflow-x:auto;"><table id="data-table">';
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

                    for (var i = 0; i < paginatedData.length; i++) {
                        table += '<tr>';
                        for (var key in paginatedData[i]) {
                            if (key === 'dict_word') {
                                table += '<td><a href="https://www.google.com/search?q=define+' + paginatedData[i][key] + '" target="new">' + paginatedData[i][key] + '</a></td>';
                            } else {
                                table += '<td>' + paginatedData[i][key] + '</td>';
                            }
                        }
                        table += '</tr>';
                    }
                    table += '</table></div>';
                    $('#cmbsolver-api-response').html(table);
                }

                function renderPagination() {
                    var pagination = '<div style="text-align:center;">';
                    pagination += '<label for="page-select">Page: </label>';
                    pagination += '<select id="page-select">';
                    for (var i = 1; i <= totalPages; i++) {
                        pagination += '<option value="' + i + '">' + i + '</option>';
                    }
                    pagination += '</select>';
                    pagination += '</div>';
                    $('#cmbsolver-api-pagination').html(pagination);
                }

                $(document).off('change', '#page-select').on('change', '#page-select', function() {
                    currentPage = $(this).val();
                    renderTable(currentPage);
                });

                renderTable(currentPage);
                renderPagination();
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error); // Debugging statement
                $('#cmbsolver-api-response').html('<p>AJAX request failed: ' + error + '</p>');
            }
        });
    });

    $('#download-csv').on('click', function() {
        var word = $('#word').val();
        var endpoint = $('#endpoint').val();
        $.ajax({
            url: cmbsolverApi.ajax_url + '/' + endpoint + '/' + word,
            method: 'GET',
            success: function(response) {
                var data = response;
                var csv = [];
                var headers = Object.keys(data[0]);
                csv.push(headers.join(','));

                data.forEach(function(row) {
                    var values = headers.map(function(header) {
                        var value = row[header];
                        if (typeof value === 'string' && value.includes(',')) {
                            value = '"' + value.replace(/"/g, '""') + '"';
                        }
                        return value;
                    });
                    csv.push(values.join(','));
                });

                var csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
                var encodedUri = encodeURI(csvContent);
                var link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'data.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error); // Debugging statement
                alert('Failed to download CSV: ' + error);
            }
        });
    });
});