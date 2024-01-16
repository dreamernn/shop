$(document).ready(function() {
    doCheckAuth();
    checkAuthorizationAndRedirect();
});

function checkAuthorizationAndRedirect() {
    $.ajax({
        url: apiUrl,
        type: "GET",
        headers: {
            "Authorization": authorizationData
        },
        success: function(response) {
            // Retrieve the redirect_url from the returned array and perform a redirection
            window.location.href = response.data.redirect_url;
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            // Handling errors
        }
    });
}