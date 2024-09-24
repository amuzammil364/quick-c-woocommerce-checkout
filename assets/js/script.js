document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  console.log(popupData);
  if (typeof popupData !== "undefined" && popupData.isTokenEmpty === "1") {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".otp-content").style.display = "none";
  }

  let isAuthenticated = false;
  let isApiKeyChecked = false;

  function closeModal() {
    const modal = document.getElementById("QCWC_loginModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  function checkApiKey() {
    const data = {
      action: "check_api_key",
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "GET",
      data: data,
      success: function (response) {
        if (response.status_code === 200) {
          closeModal();
          clearInterval(intervalId);
          console.log("API key check successful, interval stopped.");
          isApiKeyChecked = true;
        } else {
          isApiKeyChecked = true;
        }
      },
      error: function () {
        alert("There was an error with the request.");
        isApiKeyChecked = true;
      },
    });
  }

  let intervalId;

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
            isAuthenticated = true;
            // document.querySelector(".login-content").style.display = "none";
            // document.querySelector(".otp-content").style.display = "block";
            intervalId = setInterval(checkApiKey, 5000);
            if (isApiKeyChecked && isAuthenticated) {
              btnLoader.style.display = "none";
              btnText.style.display = "block";
            }
          } else {
            alert("Authentication failed: " + response.data);
            if (isApiKeyChecked && isAuthenticated) {
              btnLoader.style.display = "none";
              btnText.style.display = "block";
            }
            isAuthenticated = true;
          }
        },
        error: function () {
          alert("There was an error with the request.");
          isAuthenticated = true;
        },
      });
    }
  };
});
