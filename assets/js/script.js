document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  if (typeof popupData !== "undefined" && popupData.isTokenEmpty) {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".otp-content").style.display = "none";
  }

  document.getElementById("authenticateButton").onclick = async function () {
    const emailValue = document.getElementById("userEmail").value;
    const btnLoader = document.querySelector(".btn-loader");
    const btnText = document.querySelector(".btn-text");

    if (!emailValue) {
      alert("Please enter your email!");
    }

    const data = {
      action: "authenticate_user",
      email: emailValue,
    };

    if (emailValue && emailValue !== "") {
      btnLoader.style.display = "block";
      btnText.style.display = "none";
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            document.querySelector(".login-content").style.display = "none";
            document.querySelector(".otp-content").style.display = "block";
            btnLoader.style.display = "none";
            btnText.style.display = "block";
          } else {
            alert("Authentication failed: " + response.data);
            btnLoader.style.display = "none";
            btnText.style.display = "block";
          }
        },
        error: function () {
          alert("There was an error with the request.");
        },
      });
    }
  };

  function closeModal() {
    const modal = document.getElementById("QCWC_loginModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }
});
