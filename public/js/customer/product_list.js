$(document).ready(function () {
    doCheckAuth();
    getList();
    getCartTotalAdnPrice();
})

function getList() {
    $.ajax({
        url: apiUrl + 'customer/product-list',
        type: 'GET',
        headers: {
            'Authorization': authorizationData
        },
        success: function (response) {
            if (response.errCode == 200) {
                renderProducts(response.data)
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

function getCartTotalAdnPrice() {
    $.ajax({
        url: apiUrl + 'customer/cart-total-price',
        type: 'GET',
        headers: {
            'Authorization': authorizationData
        },
        success: function (response) {
            if (response.errCode == 200) {
                $('#cart-count').text(response.data.total);
                $('#total-price').text(response.data.total_price);
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

// render
function renderProducts(products) {
    var $productsContainer = $('.products-container');
    $productsContainer.empty();

    products.forEach(function (product) {
        var $productDiv = $('<div>').addClass('product').data('product_id', product.product_id);
        var $productTitle = $('<h2>').addClass('product-title').text(product.name);
        var $skuSelect = $('<select>').addClass('sku-select');

        product.skuList.forEach(function (sku) {
            var $option = $('<option>').attr('value', sku).text(sku);
            $skuSelect.append($option);
        });
        var $productDescription = $('<p>').addClass('product-description').text(product.description);
        var $productPrice = $('<p>').addClass('product-price').text('$' + product.price);
        var $quantityInput = $('<input>').attr('type', 'number').attr('min', '0').val(product.cart_info.quantity > 0 ? product.cart_info.quantity : 0).addClass('quantity-input');
        var $addToCartBtn = $('<button>').addClass('add-to-cart').text('add cart');

        $productDiv.append($productTitle, $skuSelect, $productDescription, $productPrice, $quantityInput, $addToCartBtn);
        $productsContainer.append($productDiv);
    });
}

function updateCartCountAndTotalPrice() {
    var totalCount = 0;
    var totalPrice = 0;

    $('.quantity-input').each(function () {
        var quantity = parseInt($(this).val());
        totalCount += quantity;

        var price = parseFloat($(this).closest('.product').find('.product-price').text().replace('$', ''));
        var subtotal = quantity * price;
        totalPrice += subtotal;
    });

    $('#cart-count').text(totalCount);
    $('#total-price').text(totalPrice.toFixed(2));
}

function syncQuantityInputs() {
    var $listInputs = $('.list-view .quantity-input');
    var $gridInputs = $('.grid-view .quantity-input');

    $listInputs.each(function (index) {
        var quantity = $(this).val();
        $gridInputs.eq(index).val(quantity);
    });
}

$('.grid-view').click(function () {
    $('.products-container').removeClass('list-view').addClass('grid-view');
    syncQuantityInputs();
});

$('.list-view').click(function () {
    $('.products-container').removeClass('grid-view').addClass('list-view');
    syncQuantityInputs();
});

$(document).on('click', '.add-to-cart', function () {
    var $input = $(this).siblings('.quantity-input');
    var $count = parseInt($input.val());
    var $productId = $(this).closest('.product').data('product_id')
    var $sku = $(this).siblings('.sku-select').val();
    if ($count > 0) {
        $.ajax({
            url: apiUrl + 'customer/cart_add',
            type: 'POST',
            headers: {
                'Authorization': authorizationData
            },
            data: {
                product_id: $productId,
                quantity: $count,
                sku: $sku
            },
            success: function (response) {
                if (response.errCode != 200) {
                    alert(response.message);
                    return false;
                } else {
                    alert('add cart successful!');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('add cart failed. Please try again.');
            }
        });
    }
    updateCartCountAndTotalPrice();
});


// Bind quantity input box change event.
/*$(document).on('change', '.quantity-input', function() {
    //updateCartCountAndTotalPrice();
});*/



