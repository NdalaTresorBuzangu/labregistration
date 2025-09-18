document.getElementById('loginForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('../actions/login-action.php', {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            window.location.href = data.redirect; // ✅ goes to index.php
        } else {
            document.getElementById('passwordError').textContent = data.message;
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

