/**
 * Cart Management JavaScript
 * Handles all cart UI interactions and AJAX calls
 */

$(document).ready(function() {
    // Update cart count on page load
    updateCartCount();

    // Add to cart button handler (can be on any product page)
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const productId = $btn.data('product-id');
        const quantity = parseInt($btn.data('quantity') || 1);

        if (!productId) {
            showMessage('error', 'Invalid product');
            return;
        }

        // Disable button during request
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

        $.ajax({
            url: 'actions/add_to_cart_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    showMessage('success', response.message);
                    updateCartCount();
                    
                    // If on cart page, reload cart
                    if ($('body').data('page') === 'cart') {
                        loadCart();
                    }
                } else {
                    showMessage('error', response.message);
                }
            },
            error: function() {
                showMessage('error', 'Failed to add to cart. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Add to Cart');
            }
        });
    });

    // Remove from cart
    $(document).on('click', '.remove-from-cart-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const productId = $btn.data('product-id');

        Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it'
        }).then((result) => {
            if (result.isConfirmed) {
                removeFromCart(productId);
            }
        });
    });

    // Update quantity
    $(document).on('change', '.cart-quantity-input', function() {
        const $input = $(this);
        const productId = $input.data('product-id');
        const quantity = parseInt($input.val());

        if (quantity <= 0) {
            $input.val(1);
            showMessage('error', 'Quantity must be at least 1');
            return;
        }

        updateQuantity(productId, quantity);
    });

    // Empty cart button
    $(document).on('click', '.empty-cart-btn', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Empty Cart?',
            text: 'Are you sure you want to remove all items from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, empty cart'
        }).then((result) => {
            if (result.isConfirmed) {
                emptyCart();
            }
        });
    });

    // Load cart if on cart page
    if ($('body').data('page') === 'cart') {
        loadCart();
    }
});

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    $.ajax({
        url: 'actions/remove_from_cart_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: productId
        },
        success: function(response) {
            if (response.success) {
                showMessage('success', response.message);
                updateCartCount();
                loadCart(); // Reload cart display
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            showMessage('error', 'Failed to remove item. Please try again.');
        }
    });
}

/**
 * Update quantity in cart
 */
function updateQuantity(productId, quantity) {
    const $input = $(`.cart-quantity-input[data-product-id="${productId}"]`);
    $input.prop('disabled', true);

    $.ajax({
        url: 'actions/update_quantity_action.php',
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update item subtotal
                const $row = $input.closest('tr, .cart-item');
                const $subtotal = $row.find('.item-subtotal');
                if ($subtotal.length) {
                    $subtotal.text('$' + parseFloat(response.item_subtotal).toFixed(2));
                }

                // Update cart total
                const $cartTotal = $('.cart-total');
                if ($cartTotal.length) {
                    $cartTotal.text('$' + parseFloat(response.cart_total).toFixed(2));
                }

                updateCartCount();
            } else {
                showMessage('error', response.message);
                // Revert input value
                loadCart();
            }
        },
        error: function() {
            showMessage('error', 'Failed to update quantity. Please try again.');
            loadCart(); // Reload to get correct values
        },
        complete: function() {
            $input.prop('disabled', false);
        }
    });
}

/**
 * Empty cart
 */
function emptyCart() {
    $.ajax({
        url: 'actions/empty_cart_action.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showMessage('success', response.message);
                updateCartCount();
                loadCart(); // Reload cart display
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            showMessage('error', 'Failed to empty cart. Please try again.');
        }
    });
}

/**
 * Load and display cart
 */
function loadCart() {
    $.ajax({
        url: 'actions/get_cart_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCart(response.cart, response.total);
            } else {
                displayEmptyCart();
            }
        },
        error: function() {
            showMessage('error', 'Failed to load cart. Please refresh the page.');
        }
    });
}

/**
 * Display cart items
 */
function displayCart(items, total) {
    const $cartContainer = $('#cart-items-container');
    if (!$cartContainer.length) return;

    if (items.length === 0) {
        displayEmptyCart();
        return;
    }

    let html = '<div class="table-responsive"><table class="table">';
    html += '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr></thead>';
    html += '<tbody>';

    items.forEach(function(item) {
        const image = item.product_image || 'https://via.placeholder.com/80x80?text=No+Image';
        const price = parseFloat(item.product_price).toFixed(2);
        const subtotal = parseFloat(item.subtotal).toFixed(2);

        html += '<tr class="cart-item" data-product-id="' + item.product_id + '">';
        html += '<td>';
        html += '<div class="d-flex align-items-center">';
        html += '<img src="' + image + '" alt="' + item.product_title + '" class="me-3" style="width:80px;height:80px;object-fit:cover;">';
        html += '<div>';
        html += '<h6 class="mb-0">' + item.product_title + '</h6>';
        html += '<small class="text-muted">' + item.cat_name + ' · ' + item.brand_name + '</small>';
        html += '</div></div></td>';
        html += '<td>$' + price + '</td>';
        html += '<td>';
        html += '<input type="number" class="form-control cart-quantity-input" data-product-id="' + item.product_id + '" value="' + item.qty + '" min="1" style="width:80px;">';
        html += '</td>';
        html += '<td class="item-subtotal">$' + subtotal + '</td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-danger remove-from-cart-btn" data-product-id="' + item.product_id + '">';
        html += '<i class="fa fa-trash"></i> Remove';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += '<div class="d-flex justify-content-between align-items-center mt-4">';
    html += '<div><h5>Total: <span class="cart-total">$' + parseFloat(total).toFixed(2) + '</span></h5></div>';
    html += '<div>';
    html += '<a href="all_product.php" class="btn btn-outline-secondary me-2">Continue Shopping</a>';
    html += '<a href="checkout.php" class="btn app-button-primary">Proceed to Checkout</a>';
    html += '</div></div>';

    $cartContainer.html(html);
}

/**
 * Display empty cart message
 */
function displayEmptyCart() {
    const $cartContainer = $('#cart-items-container');
    if (!$cartContainer.length) return;

    let html = '<div class="app-empty-state text-center py-5">';
    html += '<i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>';
    html += '<h5 class="fw-semibold mb-2">Your cart is empty</h5>';
    html += '<p class="text-muted mb-4">Add some products to your cart to get started!</p>';
    html += '<a href="all_product.php" class="btn app-button-primary">Browse Products</a>';
    html += '</div>';

    $cartContainer.html(html);

    // Hide checkout button if exists
    $('.proceed-to-checkout').hide();
}

/**
 * Update cart count in navigation
 */
function updateCartCount() {
    $.ajax({
        url: 'actions/get_cart_count_action.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const count = response.count || 0;
                const $cartBadge = $('.cart-count-badge');
                
                if ($cartBadge.length) {
                    if (count > 0) {
                        $cartBadge.text(count).show();
                    } else {
                        $cartBadge.hide();
                    }
                }
            }
        }
    });
}

/**
 * Show message using SweetAlert
 */
function showMessage(type, message) {
    const config = {
        icon: type,
        title: type === 'success' ? 'Success!' : 'Error',
        text: message,
        timer: type === 'success' ? 2000 : 3000,
        showConfirmButton: type !== 'success'
    };

    Swal.fire(config);
}

