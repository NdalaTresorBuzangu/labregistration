$(document).ready(function () {
    $('#register-form').submit(function (e) {
        e.preventDefault();

        let name = $('#name').val().trim();
        let email = $('#email').val().trim();
        let password = $('#password').val();
        let phone_number = $('#phone_number').val().trim();
        let role = $('input[name="role"]:checked').val();

        // Form validation
        if (name === '' || email === '' || password === '' || phone_number === '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });
            return;
        }

        if (password.length < 6 || 
            !/[a-z]/.test(password) || 
            !/[A-Z]/.test(password) || 
            !/[0-9]/.test(password)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });
            return;
        }

        // Send request via AJAX
        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                name: name,
                email: email,
                password: password,
                phone_number: phone_number,
                role: role
            },
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});
