jQuery(document).ready(function($) {
    $('.cut-user-link').on('click', function(e) {
        e.preventDefault();

        const userId = $(this).data('id');

        $.ajax({
            url: cut_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cut_get_user',
                nonce: cut_ajax.nonce,
                user_id: userId
            },
            beforeSend: function() {
                $('#user-detail').html('<p><em>Loading...</em></p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#user-detail').html(response.data.html);
                } else {
                    $('#user-detail').html('<p><strong>Error:</strong> ' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('#user-detail').html('<p><strong>Error:</strong> AJAX request failed.</p>');
            }
        });
    });
});