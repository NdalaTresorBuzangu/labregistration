$(document).ready(function () {
    $('#login-form').submit(function (e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val().trim();

        if (email === '' || password === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in both fields!'
            });
            return;
        }

        $.ajax({
            url: '../actions/login-action.php',  // ✅ matches your file name
            type: 'POST',
            dataType: 'json',
            data: { email: email, password: password },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php';  // ✅ redirect here
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong. Please try again later.'
                });
            }
        });
    });
});




