$(document).ready(function () {
  getAllServices();
  getUser(id);

  bookings = $("#bookings").DataTable({
    ajax: {
      url: url + "api/bookings/getOfUser",
      data: function(d) {
        d.userid = id;
        d.status = $('#statusFilter').val(); // Add status filter to the AJAX request
      },
      dataSrc: "data",
      dataType: "json",
    },
    columns: [
      {
        data: null,
        render: function (data) {
          var buttons = `
            <button
              class="btn btn-danger btn-sm cancelBtn"
              data-toggle="modal"
              data-bookingid=${data.id}
              data-target="#cancel">
              Cancel
            </button>
          `;

          if (data.status != "Pending") {
            var color = "text-success";
            if (data.status === "Cancelled" || data.status === "Rejected") {
              color = "text-danger";
            } 
            buttons = `
              <span class="${color}">${data.status}</span>
            `
          }

          return buttons;
        },
      },
      {
        data: null,
        render: function (data) {
          return `
            ${data.name} || ${data.servicetype}
          `;
        },
      },
      {
        data: "bookingtype",
      },
      {
        data: "address",
      },
      {
        data: null,
        render: function (data) {
          return `
            ${data.startdate} - ${data.enddate}
          `;
        },
      },
      {
        data: null,
        render: function (data) {
          return `
            ₱${data.price.toFixed(2)}
          `;
        },
      },
      {
        data: null,
        visible: false,
        render: function (data) {
          if (data.status === "Pending") return 1;
          if (data.status === "Accepted") return 2;
          if (data.status === "Cancelled") return 3;
          return 4;
        },
      },
    ],
    order: [[6, "asc"]],
  });

  $("#bookingForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("userid", id);

    $.ajax({
      type: "POST",
      url: url + "api/bookings/create",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (!response.hasOwnProperty("success")) {
          for (var e in response.error) {
            toastr.error(response.error[e]);
          }
        } else {
          toastr.success("Waiting for acceptance");
          $("#bookingForm")[0].reset();
          $("#booknow").modal("hide");
        }
      },
    });
  });

  $("#updateUser").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("userid", id);
    console.log(1)
    $.ajax({
      type: "PUT",
      url: url + "api/users/update",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        if (response.hasOwnProperty("error")) {
          for (var err in response.error) {
            toastr.error(response.error[err]);
          }
        } else {
          toastr.success("User updated");
          getUser(id);
        }
      },
    });
  });

  $(document).on("click", ".cancelBtn", function() {
    var bookingid = $(this).attr("data-bookingid");
    $("#bookingid").val(bookingid);
  });

  $("#cancelStatus").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      type: "PUT",
      url: url + "api/bookings/updateStatus",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (!response.hasOwnProperty("success")) {
          for (var error in response.error) {
            toastr.error(response.error[error]);
          }
        } else {
          toastr.success("Booking cancelled");
          bookings.ajax.reload();
          $("#cancel").modal("hide");
        }
      }
    });
  });
  
  $('#statusFilter').change(function() {
    bookings.ajax.reload();
  });

});

function getAllServices() {
  $.ajax({
    type: "GET",
    url: url + "api/services/getAll",
    dataType: "json",
    success: function (response) {
      var html2 = "";
      if (response.data.length > 0) {
        for (var i = 0; i < response.data.length; i++) {
          var service = response.data[i];
          html2 += `
          <div class="col-md-12 mt-1">
            <div class="sel">
              <div class="row">
                <div class="col d-flex align-items-center">
                  <input
                    type="radio"
                    name="serviceid"
                    value=${service.id}
                    required
                    aria-label="Radio button for following text input" />
                </div>
                <div class="col d-flex align-items-center">
                  <h6 class="card-title">
                    ${service.name}
                    <br />
                    <span
                      class="text-reset font-italic"
                      style="font-size: smaller"
                      >(${service.type})
                    </span>
                  </h6>
                </div>
                <div
                  class="col d-flex align-items-center justify-content-end">
                  <h6 class="card-title text-right">₱ ${service.price.toFixed(
                    2
                  )}</h6>
                </div>
              </div>
            </div>
          </div>
          `;
        }
      } else {
        html2 = `
        <div class="col text-center ss">
          <h2 data-aos="fade-up" data-aos-duration="1000">No services available</h2>
        </div>
        `;
        $("#bookingForm button[type='submit']").prop("disabled", true);
      }

      $("#typeofservice").html(html2);
      AOS.init();
    },
  });
}

function getUser(userid) {
  $.ajax({
    type: "GET",
    url: url + "api/users/getOne",
    data: { userid: userid },
    dataType: "json",
    success: function (response) {
      var user = response.data;
      $(".profile").attr("src", `${url}/img/profiles/${user.image}`);
      $(".name").text(`${user.firstname} ${user.lastname}`);
      $(".gender").text(user.gender);
      $(".email").text(user.email);
      $(".address").text(user.address);

      $(".firstname").val(user.firstname);
      $(".lastname").val(user.lastname);
      $("input[name='gender'][value='" + user.gender + "']").prop(
        "checked",
        true
      );
      $(".email").val(user.email);
      $(".address").val(user.address);
      $(".password").val(user.password);
    },
  });
}
