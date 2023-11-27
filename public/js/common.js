// common.js
document.write('<script src="http://local.shop_api.com/js/jquery-3.6.0.min.js"></script>');

var domainUrl         = "http://local.shop_api.com/html/"; // Define Domain address.
var apiUrl            = "http://local.shop_api.com/"; // Define API address.
var authorizationData = localStorage.getItem('Authorization');

function doCheckAuth() {
    if (!authorizationData) {
        toLogin();
    }
}

function toLogin() {
    localStorage.removeItem("Authorization");
    window.location.href = domainUrl + 'login.html';
}