jQuery(document).ready(function($) {
    let $importButton = $('#import_products_button');
    $importButton.on('click', function() {
        //dissable the button to prevent multiple clicks
        $importButton.prop('disabled', true);
        $importButton.text('Importing...');
        var apiUrl = $('#api_url').val();
        $.post(href_import_script.ajax_url, {
            action: 'href-import-script',
            api_url: apiUrl,
            nonce: href_import_script.nonce
        }, function(response) {
            // re-enable the button
            $importButton.prop('disabled', false);
            $importButton.text('Import Products');
            // check if the response is successful
            if (response.success) {
                console.log(response.data);
                alert(response.data.length + ' Products imported successfully.');
            } else {
                alert('failed to import products');
            }
        });
    });
});