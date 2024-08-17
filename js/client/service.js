getAllServices();

$("#bookingForm").submit(function (e) {
  console.log(1);
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("userid", id);

  // Display the values of the formData
  for (var pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }
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


$('#start').change(function() {
  var startDate = new Date($(this).val());

  if (isNaN(startDate.getTime())) {
      $('#end').val('');
      $('#end').attr('min', '');
      return;
  }

  // Add 5 hours to the start date
  var endDate = new Date(startDate.getTime() + (5 * 60 * 60 * 1000));

  // Manually format the date to YYYY-MM-DDTHH:MM
  var endDateStr = endDate.getFullYear() + '-' +
                   ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' +
                   ('0' + endDate.getDate()).slice(-2) + 'T' +
                   ('0' + endDate.getHours()).slice(-2) + ':' +
                   ('0' + endDate.getMinutes()).slice(-2);

  $('#end').val(endDateStr);
  $('#end').attr('min', endDateStr);
});


$('#end').change(function() {
  var startDate = new Date($('#start').val());
  var endDate = new Date($(this).val());

  if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
      Swal.fire({
          icon: 'error',
          title: 'Invalid Dates',
          text: 'Please select valid start and end dates.',
      });
      return;
  }

  if (endDate < startDate) {
      Swal.fire({
          icon: 'error',
          title: 'Invalid End Date',
          text: 'End date cannot be before the start date.',
      });
      $('#end').val('');
      return;
  }

  var diffHours = (endDate - startDate) / (60 * 60 * 1000);

  if (diffHours < 5) {
      Swal.fire({
          icon: 'error',
          title: 'Minimum Time Not Met',
          text: 'The end date must be at least 5 hours after the start date.',
      });
      $('#end').val('');
  }
});
function getAllServices() {
  $.ajax({
    type: "GET",
    url: url + "api/services/getAll",
    dataType: "json",
    success: function (response) {
      var html1 = "";
      var html2 = "";
      if (response.data.length > 0) {
        for (var i = 0; i < response.data.length; i++) {
          var service = response.data[i];
          html1 += `
          <div class="col-md-3 col-sm-6 ss">
            <div class="card" data-aos="fade-up" data-aos-duration="1000" onClick="openBookNowModal(${
              service.id
            })">
              <img
                src="${url}/img/services/${service.image}"
                class="card-img-top"
                alt="${service.name}" />
              <div class="card-body">
                <div class="row">
                  <div class="col">
                    <h6 class="card-title">${service.name}</h6>
                    <p class="card-title text-secondary">${service.type}</p>
                  </div>
                  <div class="col">
                    <h6 class="card-title text-right">₱ ${service.price.toFixed(
                      2
                    )}</h6>
                    <div class="d-flex justify-content-end">
                      <div class="line" style="width: 5rem"></div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <p class="card-text">
                      ${service.description}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          `;

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
        html1 = `
        <div class="col text-center ss">
          <h2 data-aos="fade-up" data-aos-duration="1000">No services available</h2>
        </div>
        `;
        html2 = `
        <div class="col text-center ss">
          <h2 data-aos="fade-up" data-aos-duration="1000">No services available</h2>
        </div>
        `;
        $("#bookingForm button[type='submit']").prop("disabled", true);
      }

      $("#services").html(html1);
      $("#typeofservice").html(html2);
      AOS.init();
    },
  });
}

function openBookNowModal(serviceId) {
  $("#booknow").modal("show");
  $("input[name='serviceid']").prop("checked", false);
  $(`input[name='serviceid'][value='${serviceId}']`).prop("checked", true);
}
