$(document).ready(function () {
  $("#show-password").on("click", function () {
    console.log("Enter password");
    const typePassword = $('input[name="password"]').prop("type");
    if (typePassword == "password") {
      $("#show-password").html("Hide Password");
      $('input[name="password"]').prop("type", "text");
    } else {
      $("#show-password").html("Show Password");
      $('input[name="password"]').prop("type", "password");
    }
  });

  $("#form-login").on("submit", function (e) {
    e.preventDefault();
    let formData = new FormData($(this)[0]);

    const reloadCaptcha = () => {
      $.ajax({
        url: "/captcha/reload",
        type: "POST",
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (result) {
          $("#captcha").html(result.captcha);
          $('input[name="captcha"]').val("");
        },
      });
    };

    ajax_post({
      url: "/login/authenticate",
      data: formData,
      processData: false,
      contentType: false,
      success: function (result) {
        Swal.close();
        if (result.code == 200) {
          show_success({
            html: result.message ?? "Login Success",
          });
          setTimeout(() => {
            window.location.href = "/home";
          }, 400);
        } else {
          // reloadCaptcha();
          show_error({
            html: result.message ?? "User identity not found.",
          });
        }
      },
      error: function (xhr) {
        Swal.close();
        const error = xhr.responseJSON;
        const message =
          error == undefined ? "Failed processing data" : error.message;
        show_error({
          html: message,
        });
        reloadCaptcha();
      },
    });
  });
});
