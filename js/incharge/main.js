$(document).ready(function () {
  getServices();

  // ADD GALERRY -> GALLERY.HTML
  $('#addGallery').submit(function (e) {
    e.preventDefault();

    const formData = new FormData();
    const fileInput = $('#addImage')[0];
    if (fileInput.files.length > 0) {
      formData.append('addImage', fileInput.files[0]);
    }

    formData.append('name', $('#name').val());
    formData.append('serviceID', $('#serviceGroup').val());
    $.ajax({
      url: `${url}api/gallery/add`, // Replace with your API endpoint
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        location.reload()
        table.ajax.reload();
        alert(`${response.message}`)
      },
      error: function (xhr, status, error) {
        console.log('Request failed:', error);
        // Handle error
      }
    });
  })

  // ADD SERVICES -> SERVICES.HTML
  $("#addServiceForm").on("submit", function (event) {
    event.preventDefault();

    // Create a FormData object to store form data
    const formData = new FormData();
    const fileInput = $('#serviceImage')[0];
    if (fileInput.files.length > 0) {
      formData.append('image', fileInput.files[0]);
    }
    formData.append('serviceName', $('#serviceName').val())
    formData.append('servicePrice', $('#servicePrice').val())
    formData.append('serviceDescription', $('#serviceDescription').val())
    // Send AJAX request to add service
    $.ajax({
      url: `${url}api/services/add`,
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.status === "success") {
          table.ajax.reload();
          alert(response.message);
        } else {
          // Optionally show an error message
          alert("Failed to add service!");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Optionally show an error message
        alert("Error: " + textStatus + " - " + errorThrown);
      },
    });
  });

  // AADD INCHARGE -> INCHARGE.HTML
  $("#addIncharge").on("submit", function (event) {
    event.preventDefault();
    // Create a FormData object from the form
    const formData = new FormData(this);

    $.ajax({
      url: `${url}api/users/add`,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response)
        if (response.success) {
          // Optionally show a success message
          table.ajax.reload();
          alert("User added successfully!");
          // Refresh the page or the data table
          // location.reload(); // or use a function to refresh the data table
        } else {
          // Optionally show an error message
          alert("Failed to add user: " + response.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Optionally show an error message
        alert("Error: " + textStatus + " - " + errorThrown);
      }
    });
  });

  // AADD USER -> USER.HTML
  $("#addUser").on("submit", function (event) {
    event.preventDefault();
    // Create a FormData object from the form
    const formData = new FormData(this);

    $.ajax({
      url: `${url}api/users/add`,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response)
        if (response.success) {
          // Optionally show a success message
          table.ajax.reload();
          alert("User added successfully!");
          // Refresh the page or the data table
          // location.reload(); // or use a function to refresh the data table
        } else {
          // Optionally show an error message
          alert("Failed to add user: " + response.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Optionally show an error message
        alert("Error: " + textStatus + " - " + errorThrown);
      }
    });
  });

  // EDIT PROFILE -> profile.html
  $("#updateProfile").on("submit", function (event) {
    event.preventDefault();
    // Create a FormData object from the form
    const formData = new FormData(this);
    formData.append("userid", id);

    $.ajax({
      url: `${url}api/users/updateProfile`,
      type: 'PUT',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response)
        if (response.success) {
          // Optionally show a success message
          // table.ajax.reload();
          alert("Profile Update!");
          // Refresh the page or the data table
          // location.reload(); // or use a function to refresh the data table
        } else {
          // Optionally show an error message
          alert("Failed to edit profile: " + response.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Optionally show an error message
        alert("Error: " + textStatus + " - " + errorThrown);
      },
      complete: () => {
        getUserInfo(id)
      }
    });
  });
  // CHANGE PASSWORD -> profile.hhtml
  $('#changePass').submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append("userid", id);
    $.ajax({
      url: `${url}api/users/changePassword`,
      type: 'PUT',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log(response)
        if (response.success) {
          // Optionally show a success message
          // table.ajax.reload();
          alert("Password Updated!");
          // Refresh the page or the data table
          // location.reload(); // or use a function to refresh the data table
        } else {
          // Optionally show an error message
          alert("Failed to edit password: " + response.message);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Optionally show an error message
        alert("Error: " + textStatus + " - " + errorThrown);
      },
      complete: () => {
        location.reload();
      }
    });
  })

  // GET PROFILE DATA -> PROFILE.HTML
  getUserInfo(id)
})

function getUserInfo(id) {
  // console.log(id)
  $.ajax({
    url: `${url}api/users/getOne?userid=${id}`, // Replace with your API endpoint
    type: 'GET',
    success: function (response) {
      console.log(response)
      const data = response.data
      // ** TEXTFIELDS
      $('.userFullname').text(`${data.firstname} ${data.lastname}`)
      $('.userGender').text(data.gender)
      $('.userAge').text(data.age)
      $('.userEmail').text(data.email)
      $('.userAddress').text(data.address)
      $('.userType').text(data.usertype)
      $('.prof-img').empty()
      $('.prof-img').append(` <img src="${url}/img/profiles/${data.image}?_=${Math.floor(new Date().getTime() / 1000)}" alt="" />`)

      //** EDIT PROFILE FIELDS
      $('#editFirstName').val(data.firstname);
      $('#editLastName').val(data.lastname);
      $('#editEmail').val(data.email);
      $('#editAddress').val(data.address);
    },
    error: function (xhr, status, error) {
      console.log('Request failed:', error);
    }
  });
}


function getServices() {
  $.ajax({
    url: `${url}api/services/getAll`, // Replace with your API endpoint
    type: 'GET',
    success: function (response) {
      // Assuming response is an array of service objects
      const select = $('#serviceGroup');
      const selectEdit = $('#editServiceGroup');
      // Clear existing options (except the first one)
      // select.find('option:not(:first)').remove();
      const service_on_booking = $('.service_on_booking');
      // ** GALLERY
      $.each(response.data, function (index, service) {
        const option = $('<option>', {
          value: service.id,
          text: service.name
        });
        select.append(option);
        selectEdit.append(option)
        service_on_booking.append(`
          <div class="col-md-12 mt-1">
            <div class="sel">
              <div class="row">
                <div class="col d-flex align-items-center">
                  <input
                    type="radio"
                    aria-label="Radio button for following text input"
                  />
                </div>
                <div class="col d-flex align-items-center">
                  <h6 class="card-title">
                    ${service.name}
                    <br /><span
                      class="text-reset font-italic"
                      style="font-size: smaller"
                      >(${service.type})</span
                    >
                  </h6>
                </div>
                <div
                  class="col d-flex align-items-center justify-content-end"
                >
                  <h6 class="card-title text-right">₱ ${service.price}</h6>
                </div>
              </div>
            </div>
          </div>
        `)
      });

    },
    error: function (xhr, status, error) {
      console.log('Request failed:', error);
    }
  });
}