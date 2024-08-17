$(document).ready(function () {
  $("#loginForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      type: "POST",
      url: url + "api/users/login",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.hasOwnProperty("error")) {
          for (var key in response.error) {
            toastr.error(response.error[key]);
          }
        } else {
          Cookies.set("usertype", response.data.usertype)
          Cookies.set("id", response.data.id)

          if (response.data.usertype === "Client") {
            window.location.href =  "client/home.html";
          } else if (response.data.usertype === "Incharge") {
            window.location.href =  "incharge/home.html";
          } else if (response.data.usertype === "Admin") {
            window.location.href =  "admin/home.html";
          }
        }
      },
    });
  });
});
