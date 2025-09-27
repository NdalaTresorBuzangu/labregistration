$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      type: "POST",
      url: "actions/login_action.php",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        console.log("AJAX response:", response); // 🔥 debug

        if (response.success) {
          // SweetAlert success popup
          Swal.fire({
            icon: "success",
            title: "Success!",
            html: response.message,
            timer: 1500,
            showConfirmButton: false,
            allowOutsideClick: false
          }).then(() => {
            // Redirect after popup
            if (response.redirect) {
              window.location.href = response.redirect;
            } else if (response.role) {
              // Fallback role-based redirect
              if (response.role == 2) {
                window.location.href = "../category.php";
              } else {
                window.location.href = "../index.php";
              }
            } else {
              console.warn("No redirect or role found, staying on login page");
            }
          });

        } else {
          // SweetAlert error popup
          Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: response.message,
            confirmButtonColor: "#d33"
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Something went wrong. Try again.",
          confirmButtonColor: "#d33"
        });
      }
    });
  });
});
