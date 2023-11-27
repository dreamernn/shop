// login.js

$(document).ready(function () {
    handleLogin();
});

function handleLogin() {
    var loginForm = $('#loginForm');
    if (authorizationData) {
        // If it exists, send data to backend and redirect based on returned redirect_url
        $.ajax({
            url: apiUrl + 'user/login',
            type: 'GET',
            headers: {
                'Authorization': authorizationData
            },
            success: function (response) {
                if (response.errCode == 2002){
                    var redirectURL = response.data.redirect_url;
                    window.location.href = redirectURL;
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                // Handle error situations
            }
        });
    } else {
        // If Authorization doesn't exist, display the login form
        loginForm.submit(function (event) {
            event.preventDefault(); // Prevent default form submission behavior

            // Get the username and password
            var username = $('#username').val();
            var password = $('#password').val();

            // Check if the username and password are valid
            if (username.trim() === '' || password.trim() === '') {
                alert('Please enter a valid username and password');
                return;
            }

            // Send username and password to the backend for validation
            $.ajax({
                url: apiUrl + 'user/doLogin',
                type: 'POST',
                data: {
                    username: username,
                    password: password
                },
                success: function (response) {
                    if (response.errCode != 200){
                        alert(response.message);
                        return false;
                    }
                    // On successful login, store the returned Authorization in localStorage
                    if (response.data){
                        var responseData = response.data;
                        localStorage.setItem('Authorization', responseData.authorization);
                        alert('login successful!')
                        window.location.href = responseData.redirect_url;
                    } else {
                        alert('Login failed. Please try again.');
                    }

                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('Login failed. Please try again.');
                }
            });
        });
    }
}
