const url = "https://pine-studio.logiclynxz.com/";
const currentUrl = window.location.href;

const id = Cookies.get("id");
const usertype = Cookies.get("usertype");



$(document).ready(function () {
  $(document).on("click", "#logoutBtn", function (e) {
    e.preventDefault();
    Cookies.remove("usertype");
    Cookies.remove("id");
    window.location.href = '/';
  });

  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  const day = String(now.getDate()).padStart(2, "0");
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");

  const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
  $("#start, #end").attr("min", currentDateTime);
});
