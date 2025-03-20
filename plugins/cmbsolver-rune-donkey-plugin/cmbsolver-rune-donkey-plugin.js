jQuery(document).ready(function($) {
    $('#generateExcelForm').on('submit', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'https://cmbsolver.com/cmbsolver-api/runewords.php/generate_excel',
            type: 'POST',
            data: formData,
            success: function(data) {
                console.log(data.base64);

                // Check if the data is a valid Base64 string
                if (btoa(atob(data.base64)) !== data.base64) {
                    alert('Error: The received data is not a valid Base64 encoded string.');
                    return;
                }

                var byteCharacters = atob(data.base64);
                var byteNumbers = new Array(byteCharacters.length);
                for (var i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                var byteArray = new Uint8Array(byteNumbers);
                var blob = new Blob([byteArray], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = 'generated_excel.xlsx';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });
});