/**
 * Checkout JavaScript
 * Handles simulated payment modal and checkout process
 */

$(document).ready(function() {
    // Load cart summary on page load
    loadCartSummary();

    // Simulate Payment button handler
    $(document).on('click', '.simulate-payment-btn', function(e) {
        e.preventDefault();
        showPaymentModal();
    });
});

/**
 * Load cart summary for checkout page
 */
function loadCartSummary() {
    $.ajax({
        url: 'actions/get_cart_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.cart.length > 0) {
                displayCheckoutSummary(response.cart, response.total);
            } else {
                // Cart is empty, redirect to products
                Swal.fire({
                    icon: 'warning',
                    title: 'Cart Empty',
                    text: 'Your cart is empty. Redirecting to products...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'all_product.php';
                });
            }
        },
        error: function() {
            showMessage('error', 'Failed to load cart. Please refresh the page.');
        }
    });
}

/**
 * Display checkout summary
 */
function displayCheckoutSummary(items, total) {
    const $summaryContainer = $('#checkout-summary');
    if (!$summaryContainer.length) return;

    let html = '<div class="table-responsive"><table class="table">';
    html += '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>';
    html += '<tbody>';

    items.forEach(function(item) {
        const image = item.product_image || 'https://via.placeholder.com/60x60?text=No+Image';
        const price = parseFloat(item.product_price).toFixed(2);
        const subtotal = parseFloat(item.subtotal).toFixed(2);

        html += '<tr>';
        html += '<td>';
        html += '<div class="d-flex align-items-center">';
        html += '<img src="' + image + '" alt="' + item.product_title + '" class="me-2" style="width:60px;height:60px;object-fit:cover;">';
        html += '<div><h6 class="mb-0">' + item.product_title + '</h6>';
        html += '<small class="text-muted">' + item.cat_name + '</small></div>';
        html += '</div></td>';
        html += '<td>$' + price + '</td>';
        html += '<td>' + item.qty + '</td>';
        html += '<td>$' + subtotal + '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += '<div class="d-flex justify-content-end mt-3">';
    html += '<div class="text-end">';
    html += '<h5>Total: <span class="checkout-total">$' + parseFloat(total).toFixed(2) + '</span></h5>';
    html += '</div></div>';

    $summaryContainer.html(html);
}

/**
 * Show simulated payment modal
 */
function showPaymentModal() {
    Swal.fire({
        title: 'Simulate Payment',
        html: `
            <div class="text-start">
                <p>This is a <strong>simulated payment</strong> for demonstration purposes.</p>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    In a real application, this would redirect to a payment gateway (PayPal, Stripe, etc.)
                </div>
                <p>Click "Yes, I've paid" to complete the checkout process.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: "Yes, I've paid",
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            processCheckout();
        }
    });
}

/**
 * Process checkout
 */
function processCheckout() {
    // Show loading
    Swal.fire({
        title: 'Processing...',
        html: 'Please wait while we process your order.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: 'actions/process_checkout_action.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    html: `
                        <div class="text-start">
                            <p><strong>Order ID:</strong> #${response.order_id}</p>
                            <p><strong>Invoice No:</strong> ${response.invoice_no}</p>
                            <p><strong>Total Amount:</strong> $${parseFloat(response.total_amount).toFixed(2)} ${response.currency}</p>
                            <p><strong>Items:</strong> ${response.items_count}</p>
                            <hr>
                            <p class="text-muted">Thank you for your purchase! Your order has been confirmed.</p>
                        </div>
                    `,
                    confirmButtonText: 'View Orders',
                    showCancelButton: true,
                    cancelButtonText: 'Continue Shopping'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'orders.php';
                    } else {
                        window.location.href = 'all_product.php';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Checkout Failed',
                    text: response.message || 'Failed to process checkout. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to process checkout. Please try again.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Checkout Error',
                text: errorMessage,
                confirmButtonText: 'OK'
            });
        }
    });
}

/**
 * Show message
 */
function showMessage(type, message) {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'Success!' : 'Error',
        text: message,
        timer: type === 'success' ? 2000 : 3000,
        showConfirmButton: type !== 'success'
    });
}

