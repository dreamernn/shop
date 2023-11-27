$(document).ready(function() {
    // 在文档加载完成后调用函数
    doCheckAuth();
    checkAuthorizationAndRedirect();
});

function checkAuthorizationAndRedirect() {
    $.ajax({
        url: apiUrl,
        type: "POST",
        headers: {
            "Authorization": authorizationData
        },
        success: function(response) {
            // Retrieve the redirect_url from the returned array and perform a redirection
            var redirectURL = response.redirect_url;
            window.location.href = redirectURL;
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            // Handling errors
        }
    });
}