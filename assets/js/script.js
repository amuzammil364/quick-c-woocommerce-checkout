document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  console.log(popupData);
  const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,7}$/;
  let successMessage = document.querySelector(".QCWC_modal-content .message");
  let successMessageText = document.querySelector(
    ".QCWC_modal-content .message .text"
  );
  let errorMessage = document.querySelector(
    ".QCWC_modal-content .error-message"
  );
  let email = document.getElementById("userEmail");
  if (typeof popupData !== "undefined" && popupData.isTokenEmpty === "1") {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".verification-content").style.display = "none";
    document.querySelector(".otp-content").style.display = "none";

    email.value = popupData.userEmail;
  }

  let isAuthenticated = false;
  let isApiKeyChecked = false;

  function closeModal() {
    const modal = document.getElementById("QCWC_loginModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

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
        window.location.reload();
      },
      error: function () {
        alert("There was an error with the request.");
      },
    });
  }

  let intervalId;

  function checkApiKey() {
    const data = {
      action: "check_api_key",
      email: email.value,
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "GET",
      data: data,
      success: function (response) {
        if (response.status_code === 200) {
          closeModal();
          stopApiKeyCheck();
          successMessage.classList.remove("active");
          successMessageText.innerHTML = "";
          isApiKeyChecked = true;
          fetchUserDetails(email.value);
        } else if (
          response.status_code === 401 ||
          response.status_code === 400
        ) {
          stopApiKeyCheck();
          document.querySelector(".login-content").style.display = "none";
          document.querySelector(".verification-content").style.display =
            "block";
          document.querySelector(".otp-content").style.display = "none";
          successMessage.classList.remove("active");
          successMessageText.innerHTML = "";
        } else {
          startApiKeyCheck();
          successMessage.classList.add("active");
          successMessageText.innerHTML = "Authenticating...";
          errorMessage.classList.remove("active");
          errorMessage.innerHTML = "";
        }
      },
      error: function () {
        alert("There was an error with the request.");
        isApiKeyChecked = true;
      },
    });
  }

  function startApiKeyCheck() {
    clearInterval(intervalId);
    intervalId = setInterval(checkApiKey, 5000);
  }

  function stopApiKeyCheck() {
    clearInterval(intervalId);
  }

  document.getElementById("authenticateButton").onclick = async function () {
    const email = document.getElementById("userEmail");
    const btnLoader = document.querySelector(".btn-loader");
    const btnText = document.querySelector(".btn-text");
    const errorText1 = document.querySelector(".errorText1");

    if (!email.value) {
      email.style.borderColor = "red";
      errorText1.classList.add("active");
      errorText1.innerHTML = "Please input your email!";
    } else if (!emailPattern.test(email.value)) {
      email.style.borderColor = "red";
      errorText1.classList.add("active");
      errorText1.innerHTML = "Please input your valid email!";
    } else {
      email.style.borderColor = "#dfe2e8";
      errorText1.classList.remove("active");
      errorText1.innerHTML = "";
    }

    const data = {
      action: "authenticate_user",
      email: email.value,
    };

    if (email.value && email.value !== "" && emailPattern.test(email.value)) {
      document.querySelector(".login-content").style.pointerEvents = "none";
      document.querySelector(".login-content").style.opacity = "0.6";
      btnLoader.style.display = "block";
      btnText.style.display = "none";
      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            isAuthenticated = true;
            checkApiKey();
            if (isAuthenticated) {
              btnLoader.style.display = "none";
              btnText.style.display = "block";
              document.querySelector(".login-content").style.pointerEvents =
                "none";
              document.querySelector(".login-content").style.opacity = "0.6";
            }
            errorMessage.classList.add("remove");
            errorMessage.innerHTML = "";
          } else {
            isAuthenticated = true;
            if (isAuthenticated) {
              btnLoader.style.display = "none";
              btnText.style.display = "block";
            }
            errorMessage.classList.add("active");
            errorMessage.innerHTML = response.message;
            document.querySelector(".login-content").style.pointerEvents =
              "all";
            document.querySelector(".login-content").style.opacity = "1";
          }
        },
        error: function () {
          isAuthenticated = true;
          alert("There was an error with the request.");
          document.querySelector(".login-content").style.pointerEvents = "all";
          document.querySelector(".login-content").style.opacity = "1";
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

    document.querySelector(".verification-content").style.pointerEvents =
      "none";
    document.querySelector(".verification-content").style.opacity = "0.6";

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      success: function (response) {
        if (response.success) {
          isAuthenticated = true;
          checkApiKey();
          if (isAuthenticated) {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            document.querySelector(
              ".verification-content"
            ).style.pointerEvents = "none";
            document.querySelector(".verification-content").style.opacity =
              "0.6";
          }
          errorMessage.classList.add("remove");
          errorMessage.innerHTML = "";
        } else {
          isAuthenticated = true;
          if (isAuthenticated) {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
          }
          errorMessage.classList.add("active");
          errorMessage.innerHTML = response.message;
          document.querySelector(".verification-content").style.pointerEvents =
            "all";
          document.querySelector(".verification-content").style.opacity = "1";
        }
      },
      error: function () {
        alert("There was an error with the request.");
        document.querySelector(".verification-content").style.pointerEvents =
          "all";
        document.querySelector(".verification-content").style.opacity = "1";
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
      email: emailValue,
      verifyMethod: "OTP",
    };

    document.querySelector(".verification-content").style.pointerEvents =
      "none";
    document.querySelector(".verification-content").style.opacity = "0.6";

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
          errorMessage.classList.add("remove");
          errorMessage.innerHTML = "";
        } else {
          errorMessage.classList.add("active");
          errorMessage.innerHTML = response.message;
          btnLoader.style.display = "none";
          btnText.style.display = "block";
          document.querySelector(".verification-content").style.pointerEvents =
            "all";
          document.querySelector(".verification-content").style.opacity = "1";
        }
      },
      error: function () {
        alert("There was an error with the request.");
        document.querySelector(".verification-content").style.pointerEvents =
          "all";
        document.querySelector(".verification-content").style.opacity = "1";
      },
    });
  });

  const verifyOtpButton = document.querySelector("#verifyOtpButton");
  verifyOtpButton.addEventListener("click", () => {
    const emailValue = document.getElementById("userEmail").value;
    const userOtp = document.getElementById("userOtp");
    const btnLoader = verifyOtpButton.querySelector(".btn-loader");
    const btnText = verifyOtpButton.querySelector(".btn-text");
    const errorText2 = document.querySelector(".errorText2");

    if (!userOtp.value) {
      userOtp.style.borderColor = "red";
      errorText2.classList.add("active");
      errorText2.innerHTML = "Please input your otp!";
    } else if (userOtp.value.length !== 6) {
      userOtp.style.borderColor = "red";
      errorText2.classList.add("active");
      errorText2.innerHTML = "Please input your valid otp!";
    } else {
      userOtp.style.borderColor = "#dfe2e8";
      errorText2.classList.remove("active");
      errorText2.innerHTML = "";
    }

    const data = {
      action: "authenticate_verify_user",
      user: emailValue,
      verifyMethod: "OTP",
      value: `${userOtp.value}`,
    };

    if (userOtp.value && userOtp.value !== "" && userOtp.value.length === 6) {
      document.querySelector(".otp-content").style.pointerEvents = "none";
      document.querySelector(".otp-content").style.opacity = "0.6";
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
            errorMessage.classList.add("remove");
            errorMessage.innerHTML = "";
            closeModal();
            fetchUserDetails(emailValue);
          } else {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            errorMessage.classList.add("active");
            errorMessage.innerHTML = response.errors.non_field_errors[0];
            document.querySelector(".otp-content").style.pointerEvents = "all";
            document.querySelector(".otp-content").style.opacity = "1";
          }
        },
        error: function () {
          alert("There was an error with the request.");
          btnLoader.style.display = "none";
          btnText.style.display = "block";
          document.querySelector(".otp-content").style.pointerEvents = "all";
          document.querySelector(".otp-content").style.opacity = "1";
        },
      });
    }
  });
});
