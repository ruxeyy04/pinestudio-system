$(document).ready(function() {
  getAllServices();

  $("#bookingForm").submit(function(e) {
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
          $('#booknow').modal('hide');
        }
      }
    });
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
                  <h6 class="card-title text-right">₱ ${service.price.toFixed(2)}</h6>
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