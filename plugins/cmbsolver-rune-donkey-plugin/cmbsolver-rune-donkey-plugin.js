jQuery(document).ready(function($) {
    $('#generateExcelForm').on('submit', function(event) {
        event.preventDefault();

        // Get the submit button and change its text
        var $submitButton = $(this).find('button[type="submit"]');
        var originalText = $submitButton.text();
        $submitButton.text('Processing...').prop('disabled', true);


        var formData = $(this).serialize();

        $.ajax({
            url: 'https://cmbsolver.com/cmbsolver-api/runewords.php/generate_excel',
            type: 'POST',
            data: formData,
            success: function(data) {
                console.log(data);
                console.log(data.base64);

                // Check if the data is a valid Base64 string
                if (btoa(atob(data.base64)) !== data.base64) {
                    alert('Error: The received data is not a valid Base64 encoded string.');
                    return;
                }

                var dateString = new Date().toISOString();
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
                a.download = 'generated_excel ' + dateString + '.xlsx';
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                // Reset button state after download completes
                $submitButton.text(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
                // Reset button state after download completes
                $submitButton.text(originalText).prop('disabled', false);
            }
        });
    });
});