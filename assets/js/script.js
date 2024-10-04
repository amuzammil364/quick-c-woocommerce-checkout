document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,7}$/;
  let successMessage = document.querySelector(".QCWC_modal-content .message");
  let successMessageText = document.querySelector(
    ".QCWC_modal-content .message .text"
  );
  let errorMessage = document.querySelector(
    ".QCWC_modal-content .error-message"
  );
  let email = document.getElementById("userEmail");
  let user_addresses = document.querySelector(".user-addresses");
  let user_delivery_prefences_list = document.querySelector(
    ".user-delivery-prefences-list"
  );
  let userDetail = {};
  let user_address_loader = document.querySelector(".user-address-loader");
  let user_delivery_prefences_loader = document.querySelector(
    ".user-delivery-prefences-loader"
  );
  email.value = popupData.userEmail;

  if (typeof popupData !== "undefined" && popupData.isTokenEmpty === "1") {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".verification-content").style.display = "none";
    document.querySelector(".otp-content").style.display = "none";
  }

  const editAddressButton = document.querySelector("#edit-address-button");

  if (editAddressButton) {
    editAddressButton.addEventListener("click", function () {
      fetchUserDetails("mhasank999@gmail.com");
      document.getElementById("QCWC_addressesModal").style.display = "flex";
      document.body.style.overflow = "hidden";
    });
  }

  let isAuthenticated = false;
  let isApiKeyChecked = false;

  function closeModal() {
    const modal = document.getElementById("QCWC_loginModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }

  function closeAddressesModal() {
    const modal = document.getElementById("QCWC_addressesModal");
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
        userDetail = response.data;
        let userAddressesHtml = "";
        let userDeliveryPrefencesHtml = "";

        if (userDetail) {
          userDetail.addresses.forEach((address) => {
            userAddressesHtml += `
            <div class="address">
              <label class="custom-radio">
                <input type="radio" name="user_address" value="${
                  address.id
                }" data-street_address="${address.short_address}" data-city="${
              address.city
            }" data-postal_code="${address.postal_code}" ${
              address.is_primary ? "checked" : ""
            } />
             <span class="radio-custom"></span>
                ${address.street_name}, ${address.city}, ${address.postal_code}
              </label>
              </div>
            `;
          });
          userDetail.delivery_preferences.forEach((userDelivery) => {
            const startTime = userDelivery.start_time
              ? userDelivery.start_time
              : "N/A";
            const endTime = userDelivery.end_time
              ? userDelivery.end_time
              : "N/A";
            userDeliveryPrefencesHtml += `
            <div class="preference">
              <label class="custom-radio">
                <input type="radio" name="delivery_preference" value="${
                  userDelivery.id
                }" data-day="${
              userDelivery.day
            }" data-start_time="${startTime}" data-end_time="${endTime}" ${
              userDelivery.is_primary ? "checked" : ""
            } />
                <span class="radio-custom"></span>
                <span>${userDelivery.day} ( ${startTime} - ${endTime} )</span>
              </label>
            </div>
            `;
          });
          user_addresses.innerHTML += userAddressesHtml;
          user_delivery_prefences_list.innerHTML += userDeliveryPrefencesHtml;
          user_address_loader.style.display = "none";
          user_delivery_prefences_loader.style.display = "none";
        } else {
          user_address_loader.style.display = "block";
          user_delivery_prefences_loader.style.display = "block";
        }
      },
      error: function () {
        alert("There was an error with the request.");
      },
    });
  }

  document
    .getElementById("confirmAddressButton")
    .addEventListener("click", function () {
      const selectedAddress = document.querySelector(
        'input[name="user_address"]:checked'
      );
      const selectedPrefence = document.querySelector(
        'input[name="delivery_preference"]:checked'
      );

      const btnLoader = document.querySelector(".confirm-btn-loader");
      const btnText = document.querySelector(".confirm-btn-text");
      const streetAddress = selectedAddress.getAttribute("data-street_address");
      const city = selectedAddress.getAttribute("data-city");
      const postalCode = selectedAddress.getAttribute("data-postal_code");

      const day = selectedPrefence.getAttribute("data-day");
      const start_time = selectedPrefence.getAttribute("data-start_time");
      const end_time = selectedPrefence.getAttribute("data-end_time");

      const data = {
        action: "save_user_detail",
        first_name: userDetail?.first_name,
        last_name: userDetail?.last_name,
        phone: userDetail.profile.primary_contact,
        street_address: streetAddress,
        city: city,
        postal_code: postalCode,
        day: day,
        start_time: start_time,
        end_time: end_time,
      };

      if (selectedAddress) {
        btnLoader.style.display = "block";
        btnText.style.display = "none";
        document.querySelector(".user-detail-content").style.opacity = "0.6";
        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: data,
          success: function (response) {
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            document.querySelector(".user-detail-content").style.opacity = "1";
            closeAddressesModal();
            window.location.reload();
          },
          error: function () {
            alert("There was an error with the request.");
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            document.querySelector(".user-detail-content").style.opacity = "1";
          },
        });
      }
    });

  function savePrimaryUserDetail(email) {
    const data = {
      action: "save_user_primary_detail",
      email: email,
    };

    jQuery.ajax({
      url: ajaxurl,
      type: "POST",
      data: data,
      success: function (response) {
        window.location.reload();
        console.log(response);
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
          savePrimaryUserDetail(email.value);
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
            errorMessage.classList.remove("active");
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
          errorMessage.classList.remove("active");
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
          errorMessage.classList.remove("active");
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
            errorMessage.classList.remove("active");
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
