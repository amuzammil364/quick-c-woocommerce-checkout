document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  console.log(popupData);
  if (typeof popupData !== "undefined" && popupData.isTokenEmpty === "1") {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".verification-content").style.display = "none";
    document.querySelector(".otp-content").style.display = "none";
  }

  let isAuthenticated = false;
  let isApiKeyChecked = false;

  function closeModal() {
    const modal = document.getElementById("QCWC_loginModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  let userEmail = "mhasank999@gmail.com";

  function fetchUserDetails(email) {
    const data = {
      action: "fetch_user_details",
      email: email,
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "GET",
      data: data,
      success: function (response) {
        console.log(response);
      },
      error: function () {
        alert("There was an error with the request.");
      },
    });
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
          fetchUserDetails(userEmail);
        } else {
          document.querySelector(".login-content").style.display = "none";
          document.querySelector(".verification-content").style.display =
            "block";
          document.querySelector(".otp-content").style.display = "none";
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

            checkApiKey();

            // intervalId = setInterval(checkApiKey, 5000);
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

  const verifyViaDeviceBtn = document.querySelector("#verifyViaDeviceBtn");

  verifyViaDeviceBtn.addEventListener("click", () => {
    const btnLoader = verifyViaDeviceBtn.querySelector(".btn-loader");
    const btnText = verifyViaDeviceBtn.querySelector(".btn-text");
    const emailValue = document.getElementById("userEmail").value;

    btnLoader.style.display = "block";
    btnText.style.display = "none";

    const data = {
      action: "authenticate_user",
      email: emailValue,
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      success: function (response) {
        if (response.success) {
          isAuthenticated = true;
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
      },
    });
  });

  const verifyViaOtp = document.querySelector("#verifyViaOtp");

  verifyViaOtp.addEventListener("click", () => {
    const btnLoader = verifyViaOtp.querySelector(".btn-loader");
    const btnText = verifyViaOtp.querySelector(".btn-text");
    const emailValue = document.getElementById("userEmail").value;

    btnLoader.style.display = "block";
    btnText.style.display = "none";

    const data = {
      action: "authenticate_user",
      email: "abdulsaqib2111d@aptechsite.net",
      verifyMethod: "OTP",
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      success: function (response) {
        if (response.success) {
          btnLoader.style.display = "none";
          btnText.style.display = "block";
          document.querySelector(".login-content").style.display = "none";
          document.querySelector(".verification-content").style.display =
            "none";
          document.querySelector(".otp-content").style.display = "block";
        } else {
          alert("Verification failed: " + response.data);
          btnLoader.style.display = "none";
          btnText.style.display = "block";
        }
      },
      error: function () {
        alert("There was an error with the request.");
      },
    });
  });

  const verifyOtpButton = document.querySelector("#verifyOtpButton");
  verifyOtpButton.addEventListener("click", () => {
    const emailValue = document.getElementById("userEmail").value;
    const userOtp = document.getElementById("userOtp").value;
    const btnLoader = verifyOtpButton.querySelector(".btn-loader");
    const btnText = verifyOtpButton.querySelector(".btn-text");

    if (!userOtp) {
      alert("Please enter your otp!");
    }

    const data = {
      action: "authenticate_verify_user",
      user: "abdulsaqib2111d@aptechsite.net",
      verifyMethod: "OTP",
      value: `${userOtp}`,
    };

    if (userOtp && userOtp !== "") {
      btnLoader.style.display = "block";
      btnText.style.display = "none";
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (response) {
          console.log(response);
          if (response.success) {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            closeModal();
            fetchUserDetails(userEmail);
          } else {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
          }
        },
        error: function () {
          alert("There was an error with the request.");
          btnLoader.style.display = "none";
          btnText.style.display = "block";
        },
      });
    }
  });
});
