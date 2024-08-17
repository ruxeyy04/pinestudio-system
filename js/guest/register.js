$(document).ready(function() {
  $("#registerForm").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      type: "POST",
      url: url + "api/users/register",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (!response.hasOwnProperty("success")) {
          for (var err in response.error) {
            toatsr.error(response.error[err]);
          }
        } else {
          toastr.success("User registered!");
          $("#registerForm")[0].reset();
        }
      }
    });
  })
})