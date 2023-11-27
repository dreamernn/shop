$(document).ready(function () {
    doCheckAuth();
    getList();
});

function getList() {
    $.ajax({
        url: apiUrl + 'admin/product-list',
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
    var productList = $('.product-list');
    data.forEach(function (product) {
        var productItem = `
      <div class="product">
        <div class="product-info">
           <span class="product-id">${product.product_id}</span><br />
           Name:<h2 class="product-name">${product.name}</h2>
           Description:<p class="product-description">${product.description}</p>
           Price:<p class="product-price">${product.price}</p>
           Sku:<p class="product-sku">${product.sku}</p>
        </div>
        <button class="edit-button">Edit</button>
      </div>
    `;
        productList.append(productItem);
    });
}

// 点击编辑按钮显示编辑弹层
$('.product-list').on('click', '.edit-button', function () {
    var product = $(this).closest('.product');
    var productId = product.find('.product-id').text();
    var productName = product.find('.product-name').text();
    var productDescription = product.find('.product-description').text();
    var productPrice = product.find('.product-price').text();
    var productSku = product.find('.product-sku').text();

    // 填充编辑表单数据
    $('#productId').val(productId);
    $('#productName').val(productName);
    $('#productDescription').val(productDescription);
    $('#productPrice').val(productPrice);
    $('#productSku').val(productSku);
    $('.edit-modal').show();
});

// 关闭编辑弹层
$('.close, .edit-modal').on('click', function () {
    $('.edit-modal').hide();
});

// Prevent the editing pop-up from closing upon clicking its content
$('.modal-content').on('click', function (event) {
    event.stopPropagation();
});

// 表单提交事件 - 编辑完成后的操作
$('#editForm').on('submit', function (event) {
    event.preventDefault();

    // 获取编辑后的数据
    var editedData = {
        product_id: $('#productId').val(),
        name: $('#productName').val(),
        description: $('#productDescription').val(),
        price: $('#productPrice').val(),
        sku: $('#productSku').val()
        // 添加其他字段
    };

    // 向服务器提交编辑后的数据
    $.ajax({
        url: apiUrl + 'admin/product-set',
        type: 'POST',
        headers: {
            'Authorization': authorizationData
        },
        data: editedData,
        success: function (response) {
            if (response.errCode != 200) {
                alert(response.message);
                return false;
            } else {
                $('.edit-modal').hide(); // 隐藏编辑弹层
                alert('Product edited successful')
                window.location.href = domainUrl + 'admin/product-list.html';
                // console.log('Data submitted successfully:', response);
            }
            // console.log('Product edited successfully:', response);


        },
        error: function (xhr, status, error) {
            console.error('Error editing product:', error);
            // 处理编辑失败的情况
        }
    });
});

