$(document).ready(function() {
    // Ensure jQuery is loaded
    if (typeof jQuery == 'undefined') {
        console.error('jQuery is not loaded');
    } else {
        console.log('jQuery is loaded');
    }

    // Form validation (client-side)
    $('#registerForm').on('submit', function(e) {
        let password = $('#password').val();
        if (password.length < 6 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
            e.preventDefault();
            alert('Password must be at least 6 characters, include one uppercase letter and one number.');
        }
    });

    // Wishlist button handling
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let button = $(this);
        let productId = button.attr('data-product-id');
        
        // Validate product ID
        if (!productId || productId === '' || productId === '0') {
            alert('Error: Product ID not found.');
            return false;
        }
        
        productId = parseInt(productId, 10);
        if (isNaN(productId) || productId <= 0) {
            alert('Error: Invalid product ID.');
            return false;
        }
        
        // Disable button during request
        button.prop('disabled', true);
        let originalText = button.html();
        
        // Determine the correct path based on current location
        let basePath = window.location.pathname.includes('/buyer/') ? 'add_to_wishlist.php' : 'buyer/add_to_wishlist.php';
        
        $.ajax({
            url: basePath,
            method: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                button.prop('disabled', false);
                
                if (response.success) {
                    if (response.action === 'added') {
                        button.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
                        button.html('❤️ Remove from Wishlist');
                        if (typeof response.message !== 'undefined') {
                            alert(response.message);
                        }
                    } else if (response.action === 'removed') {
                        button.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
                        button.html('🤍 Add to Wishlist');
                        if (typeof response.message !== 'undefined') {
                            alert(response.message);
                        }
                        // If on wishlist page, remove the item
                        if (window.location.pathname.includes('wishlist.php')) {
                            button.closest('.col-md-3, .col-md-4, .col-md-6').fadeOut(300, function() {
                                $(this).remove();
                                if ($('.row').children().length === 0) {
                                    $('.row').html('<div class="col-12"><p class="text-center">Your wishlist is empty.</p></div>');
                                }
                            });
                        }
                    }
                } else {
                    alert(response.message || 'Something went wrong.');
                }
            },
            error: function(xhr, status, error) {
                button.prop('disabled', false);
                console.error('AJAX error:', status, error);
                console.error('Response:', xhr.responseText);
                try {
                    let errorResponse = JSON.parse(xhr.responseText);
                    alert(errorResponse.message || 'Failed to update wishlist.');
                } catch(e) {
                    alert('Failed to update wishlist. Please try again.');
                }
            }
        });
        
        return false;
    });

    // Add product form validation
    $('#addProductForm').on('submit', function(e) {
        let images = $('#images')[0].files;
        let laundryMemo = $('#laundry_memo')[0].files;
        let allowedImageTypes = ['image/jpeg', 'image/png'];
        let maxFileSize = 5 * 1024 * 1024; // 5MB

        if (images.length === 0) {
            e.preventDefault();
            alert('At least one image is required.');
        }

        for (let file of images) {
            if (file && file.size > maxFileSize) {
                e.preventDefault();
                alert('Image size must be less than 5MB: ' + file.name);
                return;
            }
            if (file && !allowedImageTypes.includes(file.type)) {
                e.preventDefault();
                alert('Image must be JPG or PNG: ' + file.name);
                return;
            }
        }
        if (laundryMemo.length > 0) {
            if (laundryMemo[0].size > maxFileSize) {
                e.preventDefault();
                alert('Laundry memo size must be less than 5MB.');
                return;
            }
            if (!allowedImageTypes.includes(laundryMemo[0].type)) {
                e.preventDefault();
                alert('Laundry memo must be JPG or PNG.');
                return;
            }
        }
    });

    // Seller document upload validation
    $('#documentUploadForm').on('submit', function(e) {
        let nid = $('#nid')[0].files;
        let certificate = $('#certificate')[0].files;
        let allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        let maxFileSize = 5 * 1024 * 1024; // 5MB

        if (nid.length === 0 || certificate.length === 0) {
            e.preventDefault();
            alert('Both NID and certificate are required.');
            console.error('Missing files: NID:', nid.length, 'Certificate:', certificate.length);
        }
        if (nid.length > 0 && nid[0].size > maxFileSize) {
            e.preventDefault();
            alert('NID file size must be less than 5MB.');
            console.error('NID size too large:', nid[0].size);
        }
        if (certificate.length > 0 && certificate[0].size > maxFileSize) {
            e.preventDefault();
            alert('Certificate file size must be less than 5MB.');
            console.error('Certificate size too large:', certificate[0].size);
        }
        if (nid.length > 0 && !allowedTypes.includes(nid[0].type)) {
            e.preventDefault();
            alert('NID must be JPG, PNG, or PDF.');
            console.error('Invalid NID type:', nid[0].type);
        }
        if (certificate.length > 0 && !allowedTypes.includes(certificate[0].type)) {
            e.preventDefault();
            alert('Certificate must be JPG, PNG, or PDF.');
            console.error('Invalid certificate type:', certificate[0].type);
        }
    });
});