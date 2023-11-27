$(document).ready(function () {
    doCheckAuth();
    getList();
})
var cartItems = {}
function getList() {
    $.ajax({
        url: apiUrl + 'customer/cart-list',
        type: 'GET',
        headers: {
            'Authorization': authorizationData
        },
        success: function (response) {
            if (response.errCode == 200) {
                renderCartItems(response.data)  //Render initial cart items and total price
                calculateTotalPrice(response.data);
                cartItems = response.data;
                $('.cart-details').wrapInner('<a href='+domainUrl+'customer/cart-list.html>');
            } else if (response.errCode == 2001) {
                toLogin();
            } else {
                alert(response.message);
                return false;
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            // Handle error situations
        }
    });
}

// Function to render cart items
function renderCartItems(items) {
    var $cartItems = $('.cart-items');
    $cartItems.empty();
    if (items == ''){
        $('#checkout-btn').prop('disabled', true);
        return;
    }
    items.forEach(function(item) {
        var $cartItem = $('<div>').addClass('cart-item').data('cart_id', item.cart_id);
        var $title = $('<h2>').text(item.name);
        var $sku = $('<p>').addClass('product-sku').text('SKU: ' + item.sku);
        var $description = $('<p>').addClass('product-description').text(item.description);
        var $price = $('<p>').addClass('product-price').text('$' + item.price);
        var $quantity = $('<div>').addClass('quantity-wrapper');
        var $quantityWrapper = $('<div>').addClass('quantity-wrapper').addClass('quantity-input').text('Quantity:' + item.quantity).data('item-id', item.id);

/*        var $quantityInput = $('<input>').attr('type', 'number').attr('min', '0').val(item.quantity).addClass('quantity-input').data('item-id', item.id);
        var $increaseBtn = $('<button>').text('+').addClass('increase-btn').data('item-id', item.id);
        var $decreaseBtn = $('<button>').text('-').addClass('decrease-btn').data('item-id', item.id);

        $quantityWrapper.append($decreaseBtn, $quantityInput, $increaseBtn);*/

        $cartItem.append($title, $sku, $description, $price, $quantityWrapper);
        $cartItems.append($cartItem);
    });
}

// Function to calculate total price
function calculateTotalPrice(items) {
    var totalPrice = items.reduce(function(total, item) {
        return total + (item.price * item.quantity);
    }, 0);
    $('#total-price').text(totalPrice.toFixed(2));
}

// Handle quantity increase
$('.cart-items').on('click', '.increase-btn', function() {
    var itemId = $(this).data('item-id');
    var $quantityInput = $(this).siblings('.quantity-input');
    var quantity = parseInt($quantityInput.val());
    $quantityInput.val(quantity + 1);
    cartItems.find(item => item.id === itemId).quantity++;
    calculateTotalPrice(cartItems);
});

// Handle quantity decrease
$('.cart-items').on('click', '.decrease-btn', function() {
    var itemId = $(this).data('item-id');
    var $quantityInput = $(this).siblings('.quantity-input');
    var quantity = parseInt($quantityInput.val());
    if (quantity > 0) {
        $quantityInput.val(quantity - 1);
        cartItems.find(item => item.id === itemId).quantity--;
        calculateTotalPrice(cartItems);
    }
});

// Handle checkout button click
$('#checkout-btn').click(function() {
    $('.checkout-modal').css('display', 'flex');
});

// Handle close button in modal
$('.close').click(function() {
    $('.checkout-modal').css('display', 'none');
});

// Handle form submission
$('#checkout-form').submit(function(event) {
    event.preventDefault();
    var userData = {
        first_name: $('#first_name').val(),
        last_name: $('#last_name').val(),
        email: $('#email').val(),
        cart_list: cartItems.filter(item => item.quantity > 0)
    };
    console.log(cartItems);
    // Perform AJAX request to send data to the server
    $.ajax({
        url: apiUrl + 'customer/order_add',
        type: 'PUT',
        headers: {
            'Authorization': authorizationData
        },
        data: JSON.stringify(userData),
        contentType: 'application/json',
        success: function(response) {
            if (response.errCode != 200) {
                alert(response.message);
                return false;
            } else {
                alert('add order successful!');
                $('.checkout-modal').css('display', 'none');
                window.location.href = domainUrl + 'customer/product-list.html';
                // console.log('Data submitted successfully:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('add order failed. Please try again.');
        }
    });
});
