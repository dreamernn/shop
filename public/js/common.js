// common.js
document.write('<script src="http://localhost:8001/js/jquery-3.6.0.min.js"></script>');

var domainUrl         = "http://localhost:8001/html/"; // Define Domain address.
var apiUrl            = "http://localhost:8001/"; // Define API address.
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