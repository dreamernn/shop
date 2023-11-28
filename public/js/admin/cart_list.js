$(document).ready(function () {
    doCheckAuth();
    getList();
});

function getList() {
    $.ajax({
        url: apiUrl + 'admin/cart-list',
        type: 'GET',
        headers: {
            'Authorization': authorizationData
        },

        success: function (response) {
            if (response.errCode == 200) {
                renderProducts(response.data)  //Render initial cart items and total price
            } else if (response.errCode == 2001) {
                toLogin();
            } else {
                alert(response.message);
                return false;
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });

}

// render
function renderProducts(data) {
    var cartList = $('.cart-list');
    data.forEach(function (cart) {
        var cartItem = `
      <div class="cart">
        <div class="cart-info">
           <p class="cart-id">${cart.cart_id}</p>
           User:<p class="user-name">${cart.username}</p>
           Product Name:<p class="product-name">${cart.name}</p>
           Price:<p class="product-price">${cart.price}</p>
           Quantity:<p class="product-quantity">${cart.quantity}</p>
           Sku:<p class="product-sku">${cart.sku}</p>
           created_at:<p class="created_at">${cart.created_at}</p>
        </div>
        <button class="view-button">View</button>
      </div>
    `;
        cartList.append(cartItem);
    });
}

// Click VIEW button to display edit layer
$('.cart-list').on('click', '.view-button', function () {
    var cart = $(this).closest('.cart');
    var cartId = cart.find('.cart-id').text();
    var username = cart.find('.user-name').text();
    var productName = cart.find('.product-name').text();
    var price = cart.find('.product-price').text();
    var quantity = cart.find('.product-quantity').text();
    var sku = cart.find('.product-sku').text();
    var createdAt = cart.find('.created_at').text();

    $('#cartId').text(cartId);
    $('#username').text(username);
    $('#productName').text(productName);
    $('#price').text(price);
    $('#quantity').text(quantity);
    $('#sku').text(sku);
    $('#createdAt').text(createdAt);
    $('.view-modal').show();
});


$('.close, .view-modal').on('click', function () {
    $('.view-modal').hide();
});

// Prevent the editing pop-up from closing upon clicking its content
$('.modal-content').on('click', function (event) {
    event.stopPropagation();
});

