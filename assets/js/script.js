document.addEventListener("DOMContentLoaded", () => {
  console.log("Main Js Loaded!");
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  let successMessage = document.querySelector(".QCWC_modal-content .message");
  let successMessageText = document.querySelector(
    ".QCWC_modal-content .message .text"
  );
  let errorMessage = document.querySelector(
    ".QCWC_modal-content .error-message"
  );
  let errorMessage1 = document.querySelector(
    ".QCWC_modal-content .error-message1"
  );
  let errorMessage2 = document.querySelector(
    ".QCWC_modal-content .error-message2"
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
  let quick_c_logout_btn = document.getElementById("quick-c-logout-btn");
  let register_account_paras = document.querySelectorAll(
    ".register-account-para"
  );
  let login_account_para = document.querySelector(".login-account-para");
  let login_account_para_link = login_account_para.querySelector(".link");
  let register_tabs = document.querySelectorAll(".register-tab");
  let register_tab_contents = document.querySelectorAll(
    ".register-tab-content"
  );
  const add_new_time_tagline = document.querySelector(
    ".add-new-delivery-time-tagline"
  );
  const add_new_address_tagline = document.querySelector(
    ".add-new-address-tagline"
  );
  let addressContainer = document.querySelector(".QCWC_addresses");
  const add_new_time_form = document.querySelector(
    ".add-new-delivery-time-form"
  );
  const add_delivery_time_button = document.getElementById("add_delivery_time");
  const cancel_delivery_time_button = document.getElementById(
    "cancel_delivery_time"
  );
  const delivery_times_container = document.querySelector(".delivery-times");
  const firstName = document.getElementById("register_first_name");
  const lastName = document.getElementById("register_last_name");
  const register_primary_phone_number_code = document.getElementById(
    "register_primary_phone_number_code"
  );
  const register_primary_phone_number = document.getElementById(
    "register_primary_phone_number"
  );
  const register_secondary_phone_number = document.getElementById(
    "register_secondary_phone_number"
  );
  const register_secondary_phone_number_code = document.getElementById(
    "register_secondary_phone_number_code"
  );
  const register_email = document.getElementById("register_email");
  let qcwc_cart_button = document.getElementById("qcwc-cart-button");
  let deliveryPreferences = [
    {
      day: "MORNING",
      start_time: "09:00:00",
      end_time: "10:00:00",
      is_primary: true,
    },
  ];
  let addresses = [];
  let addressCounter = 1;
  email.value = popupData.userEmail;

  if (
    typeof popupData !== "undefined" &&
    popupData.isTokenEmpty === "1" &&
    popupData.quick_c_checkout &&
    popupData.is_open
  ) {
    document.getElementById("QCWC_loginModal").style.display = "flex";
    document.body.style.overflow = "hidden";
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".verification-content").style.display = "none";
    document.querySelector(".otp-content").style.display = "none";
    document.querySelector(".register-content").style.display = "none";
  }

  if (qcwc_cart_button) {
    qcwc_cart_button.addEventListener("click", () => {
      document.getElementById("QCWC_loginModal").style.display = "flex";
      document.body.style.overflow = "hidden";
      document.querySelector(".login-content").style.display = "block";
      document.querySelector(".verification-content").style.display = "none";
      document.querySelector(".otp-content").style.display = "none";
      document.querySelector(".register-content").style.display = "none";
    });
  }
  register_account_paras.forEach((account_para) => {
    let link = account_para.querySelector(".link");
    link.addEventListener("click", () => {
      document.querySelector(".login-content").style.display = "none";
      document.querySelector(".verification-content").style.display = "none";
      document.querySelector(".otp-content").style.display = "none";
      document.querySelector(".register-content").style.display = "block";
      login_account_para.style.display = "block";
      register_account_paras.forEach(
        (account_para) => (account_para.style.display = "none")
      );
    });
  });

  login_account_para_link.addEventListener("click", () => {
    document.querySelector(".login-content").style.display = "block";
    document.querySelector(".verification-content").style.display = "none";
    document.querySelector(".otp-content").style.display = "none";
    document.querySelector(".register-content").style.display = "none";
    login_account_para.style.display = "none";
    register_account_paras.forEach(
      (account_para) => (account_para.style.display = "block")
    );
  });

  register_tabs.forEach((register_tab, index) => {
    const register_tab_attr = register_tab.getAttribute("data-target");
    register_tab.addEventListener("click", () => {
      if (index === 1) {
        if (
          firstName.value == "" ||
          lastName.value == "" ||
          register_primary_phone_number_code.value == "" ||
          register_primary_phone_number.value == "" ||
          register_secondary_phone_number.value == "" ||
          register_secondary_phone_number_code.value == "" ||
          register_email.value == "" ||
          !emailPattern.test(register_email.value)
        ) {
          return;
        }
      }

      if (index === 2) {
        let validationError = false;
        addresses.forEach((address) => {
          const short_address = document.getElementById(
            `register_short_address_${address.id}`
          );
          const primary_address = document.getElementById(
            `register_primary_address_${address.id}`
          );
          const building_number = document.getElementById(
            `register_building_number_${address.id}`
          );
          const street_name = document.getElementById(
            `register_street_name_${address.id}`
          );
          const secondary = document.getElementById(
            `register_secondary_${address.id}`
          );
          const district = document.getElementById(
            `register_district_${address.id}`
          );
          const postal_code = document.getElementById(
            `register_postal_code_${address.id}`
          );
          const city = document.getElementById(`register_city_${address.id}`);

          if (
            short_address.value == "" ||
            primary_address.value == "" ||
            building_number.value == "" ||
            street_name.value == "" ||
            secondary.value == "" ||
            district.value == "" ||
            postal_code.value == "" ||
            city.value == ""
          ) {
            validationError = true;
          }
        });

        if (validationError) {
          return;
        }
      }
      register_tabs.forEach((tab) => tab.classList.remove("active"));

      register_tab.classList.add("active");

      register_tab_contents.forEach((content) => {
        content.style.display = "none";
      });
      const targetContent = document.getElementById(register_tab_attr);
      if (targetContent) {
        targetContent.style.display = "block";
      }
    });
  });

  register_tabs[0].click();

  add_new_time_tagline.addEventListener("click", () => {
    add_new_time_tagline.style.display = "none";
    add_new_time_form.style.display = "block";
  });

  cancel_delivery_time_button.addEventListener("click", () => {
    add_new_time_tagline.style.display = "block";
    add_new_time_form.style.display = "none";
  });

  add_delivery_time_button.addEventListener("click", () => {
    const day = document.getElementById("delivery_day");
    const startTime = document.getElementById("delivery_start_time");
    const endTime = document.getElementById("delivery_end_time");

    const formatTime = (time) => {
      const [hours, minutes] = time.split(":");
      return `${hours.padStart(2, "0")}:${minutes.padStart(2, "0")}:00`;
    };

    if (!day.value) {
      day.classList.add("valid");
    } else {
      day.classList.remove("valid");
    }
    if (!startTime.value) {
      startTime.classList.add("valid");
    } else {
      startTime.classList.remove("valid");
    }
    if (!endTime.value) {
      endTime.classList.add("valid");
    } else {
      endTime.classList.remove("valid");
    }

    if (day.value && startTime.value && endTime.value) {
      const deliveryPreference = {
        day: day.value.toUpperCase(),
        start_time: formatTime(startTime.value),
        end_time: formatTime(endTime.value),
        is_primary: false,
      };

      deliveryPreferences.push(deliveryPreference);

      const newDeliveryTime = document.createElement("div");
      newDeliveryTime.classList.add("delivery-time");
      const deliveryLabel = `${day.value.toUpperCase()} ( ${
        deliveryPreference.start_time
      } - ${deliveryPreference.end_time} )`;
      newDeliveryTime.innerHTML = `
        <label class="custom-radio">
            <input type="radio" name="delivery_time" data-day="${day.value.toUpperCase()}" data-start_time="${
        deliveryPreference.start_time
      }" data-end_time="${deliveryPreference.end_time}"  />
            <span class="radio-custom"></span>
            <span>${deliveryLabel}</span>
        </label>
    `;
      delivery_times_container.appendChild(newDeliveryTime);

      document.getElementById("delivery_day").value = "";
      document.getElementById("delivery_start_time").value = "";
      document.getElementById("delivery_end_time").value = "";
      add_new_time_form.style.display = "none";
      add_new_time_tagline.style.display = "block";

      attachRadioButtonListeners();
    }
  });

  function attachRadioButtonListeners() {
    const delivery_times_radio_buttons = document.querySelectorAll(
      'input[name="delivery_time"]'
    );
    delivery_times_radio_buttons.forEach((radio) => {
      radio.addEventListener("change", () => {
        deliveryPreferences.forEach((pref) => {
          pref.is_primary = false;
        });

        const selectedDay = radio.getAttribute("data-day").toUpperCase();
        const selectedStartTime = radio.getAttribute("data-start_time");
        const selectedEndTime = radio.getAttribute("data-end_time");

        const matchingIndex = Array.from(
          delivery_times_radio_buttons
        ).findIndex(
          (rb) =>
            rb.dataset.day === selectedDay &&
            rb.dataset.start_time === selectedStartTime &&
            rb.dataset.end_time === selectedEndTime
        );

        if (matchingIndex.length !== -1) {
          deliveryPreferences[matchingIndex].is_primary = true;
        }
      });
    });
  }

  attachRadioButtonListeners();

  function debounce(func, delay) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), delay);
    };
  }

  const createAddressForm = (unique_id, is_default = false) => {
    const removeButtonHtml = !is_default
      ? `<div class="QCWC_form-group">
         <button class="delete-address-button" data-id="${unique_id}">
           Remove Address
         </button>
       </div>`
      : "";

    const addressHtml = `
        <div class="QCWC_address" id="${unique_id}">
          <div class="QCWC_form-groups">
            <div class="QCWC_form-group">
                <label for="#register_short_address">Short Address</label>
                <input type="text" id="register_short_address_${unique_id}" placeholder="e.g123123">
                <span class="error" id="shortAddressError_${unique_id}"></span>
                <span class="input-loader" id="input-loader_${unique_id}"></span>
                <div class="dropdown-suggestions" id="dropdown_${unique_id}" style="display: none;">
                </div>
            </div>
            <div class="QCWC_form-group">
                <label for="#register_primary_address">Primary Address</label>
                <input type="text" id="register_primary_address_${unique_id}" placeholder="Enter your primary address">
                <span class="error" id="primaryAddressError_${unique_id}"></span>
            </div>
            <div class="QCWC_form-group">
                <div class="QCWC_form-group-inputs">
                    <div class="QCWC_child-form-group">
                        <label for="#register_building_number">Building No</label>
                        <input type="text" id="register_building_number_${unique_id}" placeholder="e.g123123">
                    </div>
                    <div class="QCWC_child-form-group">
                        <label for="#register_street_name">Street Name</label>
                        <input type="text" id="register_street_name_${unique_id}" placeholder="e.g. Hello Street">
                        <span class="error" id="streetNameError_${unique_id}"></span>
                    </div>
                </div>
            </div>
            <div class="QCWC_form-group">
                <div class="QCWC_form-group-inputs">
                    <div class="QCWC_child-form-group">
                        <label for="#register_secondary">Secondary</label>
                        <input type="text" id="register_secondary_${unique_id}" placeholder="e.g123123">
                    </div>
                    <div class="QCWC_child-form-group">
                        <label for="#register_district">District</label>
                        <input type="text" id="register_district_${unique_id}" placeholder="e.g. Hello District">
                        <span class="error" id="districtError_${unique_id}"></span>
                    </div>
                </div>
            </div>
            <div class="QCWC_form-group">
                <div class="QCWC_form-group-inputs">
                    <div class="QCWC_child-form-group">
                        <label for="#register_postal_code">Postal Code</label>
                        <input type="number" id="register_postal_code_${unique_id}" placeholder="">
                    </div>
                    <div class="QCWC_child-form-group">
                        <label for="#register_city">City</label>
                        <input type="text" id="register_city_${unique_id}" placeholder="City">
                        <span class="error" id="cityError_${unique_id}"></span>
                    </div>
                </div>
            </div>
            ${removeButtonHtml}
            <div class="QCWC_form-group">
                <label class="custom-radio">
                    <input type="radio" name="defaultAddress" class="defaultAddress" data-id="${unique_id}" ${
      is_default ? "checked" : ""
    } />
                    <span class="radio-custom"></span>
                    <span>Set as Default National Address</span>
                </label>
            </div>
          </div>
        </div>
    `;

    addressContainer.insertAdjacentHTML("beforeend", addressHtml);

    addresses.push({
      id: unique_id,
      short_address: "",
      primary_address: "",
      building_number: "",
      street_name: "",
      secondary: "",
      district: "",
      postal_code: "",
      city: "",
      is_primary: is_default,
    });

    const addressObject = addresses.find((address) => address.id === unique_id);

    const updateAddressField = (field, value) => {
      if (addressObject) {
        addressObject[field] = value;
      }
    };

    document
      .getElementById(`register_short_address_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("short_address", e.target.value);
        if (e.target.value.length > 7) {
          debouncedSearchShortAddress(e.target.value);
        }
      });
    document
      .getElementById(`register_primary_address_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("primary_address", e.target.value);
      });
    document
      .getElementById(`register_building_number_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("building_number", e.target.value);
      });
    document
      .getElementById(`register_street_name_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("street_name", e.target.value);
      });
    document
      .getElementById(`register_secondary_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("secondary", e.target.value);
      });
    document
      .getElementById(`register_district_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("district", e.target.value);
      });
    document
      .getElementById(`register_postal_code_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("postal_code", e.target.value);
      });
    document
      .getElementById(`register_city_${unique_id}`)
      .addEventListener("input", (e) => {
        updateAddressField("city", e.target.value);
      });

    const defaultAddressRadios = document.querySelectorAll(".defaultAddress");

    defaultAddressRadios.forEach((radio) => {
      radio.addEventListener("change", () => {
        addresses.forEach((pref) => (pref.is_primary = false));

        const selectedId = radio.getAttribute("data-id");
        const selectedAddress = addresses.find(
          (pref) => pref.id === selectedId
        );
        if (selectedAddress) {
          selectedAddress.is_primary = true;
        }
      });
    });

    if (!is_default) {
      document
        .querySelector(`#${unique_id} .delete-address-button`)
        .addEventListener("click", () => {
          document.getElementById(`${unique_id}`).remove();

          addresses = addresses.filter((address) => address.id !== unique_id);
        });
    }

    const debouncedSearchShortAddress = debounce((value) => {
      searchShortAddress(value);
    }, 300);

    const inputField = document.getElementById(
      `register_short_address_${unique_id}`
    );
    const dropdown = document.getElementById(`dropdown_${unique_id}`);

    function searchShortAddress(value) {
      const input_loader = document.getElementById(`input-loader_${unique_id}`);
      const data = {
        action: "search_short_address",
        value: value,
      };

      input_loader.style.display = "block";

      jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        data: data,
        success: function (response) {
          const dropdown = document.getElementById(`dropdown_${unique_id}`);
          dropdown.innerHTML = "";
          let short_addresses = [];
          if (
            response.data?.Addresses &&
            response.data?.Addresses?.length > 0
          ) {
            dropdown.style.display = "block";

            short_addresses = response.data.Addresses;
          } else {
            dropdown.style.display = "block";

            short_addresses = [];

            const suggestionItem = document.createElement("div");
            suggestionItem.classList.add("suggestion-item");
            suggestionItem.classList.add("not-found");
            suggestionItem.textContent = "No address found!";

            dropdown.appendChild(suggestionItem);
            input_loader.style.display = "none";
          }

          short_addresses.forEach((item) => {
            const suggestionItem = document.createElement("div");
            suggestionItem.classList.add("suggestion-item");
            suggestionItem.textContent = `${item.BuildingNumber}, ${item.Street}, ${item.City}, ${item.PostCode}`;
            suggestionItem.addEventListener("click", () => {
              document.getElementById(
                `register_short_address_${unique_id}`
              ).value = item.ShortAddress
                ? item.ShortAddress
                : document.getElementById(`register_short_address_${unique_id}`)
                    .value;
              document.getElementById(
                `register_building_number_${unique_id}`
              ).value = item.BuildingNumber ? item.BuildingNumber : "";
              document.getElementById(
                `register_street_name_${unique_id}`
              ).value = item.Street ? item.Street : "";
              document.getElementById(`register_district_${unique_id}`).value =
                item.District ? item.District : "";
              document.getElementById(`register_city_${unique_id}`).value =
                item.City ? item.City : "";
              document.getElementById(
                `register_postal_code_${unique_id}`
              ).value = item.PostCode ? item.PostCode : "";
              dropdown.style.display = "none";
            });
            dropdown.appendChild(suggestionItem);
            input_loader.style.display = "none";
          });
        },
        error: function () {
          alert("There was an error with the request.");
          input_loader.style.display = "none";
        },
      });
    }

    document.addEventListener("click", (event) => {
      if (!dropdown.contains(event.target) && event.target !== inputField) {
        dropdown.style.display = "none";
      }
    });
  };

  createAddressForm(`address-default-${Date.now()}`, true);

  add_new_address_tagline.addEventListener("click", () => {
    const uniqueId = `address-${Date.now()}-${addressCounter++}`;
    createAddressForm(uniqueId);
  });

  document.getElementById("registerButton").addEventListener("click", () => {
    const firstNameError = document.getElementById("firstNameError");
    const lastNameError = document.getElementById("lastNameError");
    const primaryPhoneNumberError = document.getElementById(
      "primaryPhoneNumberError"
    );

    const secondaryPhoneNumberError = document.getElementById(
      "secondaryPhoneNumberError"
    );
    const emailError = document.getElementById("emailError");
    firstNameError.textContent = "";
    lastNameError.textContent = "";
    primaryPhoneNumberError.textContent = "";
    secondaryPhoneNumberError.textContent = "";
    emailError.textContent = "";

    let isValid = true;

    if (document.getElementById("main-detail").style.display === "block") {
      if (firstName.value == "") {
        isValid = false;
        document.getElementById("register_first_name").style.borderColor =
          "red";
        firstNameError.textContent = "First name is required.";
      } else {
        document.getElementById("register_first_name").style.borderColor =
          "rgb(223, 226, 232)";
        firstNameError.textContent = "";
      }

      if (lastName.value == "") {
        isValid = false;
        document.getElementById("register_last_name").style.borderColor = "red";
        lastNameError.textContent = "Last name is required.";
      } else {
        document.getElementById("register_last_name").style.borderColor =
          "rgb(223, 226, 232)";
        lastNameError.textContent = "";
      }

      if (register_primary_phone_number_code.value == "") {
        isValid = false;
        document.getElementById(
          "register_primary_phone_number_code"
        ).style.borderColor = "red";
      } else {
        document.getElementById(
          "register_primary_phone_number_code"
        ).style.borderColor = "rgb(223, 226, 232)";
      }

      if (register_primary_phone_number.value == "") {
        isValid = false;
        document.getElementById(
          "register_primary_phone_number"
        ).style.borderColor = "red";
        primaryPhoneNumberError.textContent = "Phone number is required.";
      } else {
        document.getElementById(
          "register_primary_phone_number"
        ).style.borderColor = "rgb(223, 226, 232)";
        primaryPhoneNumberError.textContent = "";
      }

      if (register_secondary_phone_number.value == "") {
        isValid = false;
        document.getElementById(
          "register_secondary_phone_number"
        ).style.borderColor = "red";
        secondaryPhoneNumberError.textContent = "Phone number is required.";
      } else {
        document.getElementById(
          "register_secondary_phone_number"
        ).style.borderColor = "rgb(223, 226, 232)";
        secondaryPhoneNumberError.textContent = "";
      }

      if (register_secondary_phone_number_code.value == "") {
        isValid = false;
        document.getElementById(
          "register_secondary_phone_number_code"
        ).style.borderColor = "red";
      } else {
        document.getElementById(
          "register_secondary_phone_number_code"
        ).style.borderColor = "rgb(223, 226, 232)";
      }

      if (register_email.value == "") {
        isValid = false;
        document.getElementById("register_email").style.borderColor = "red";
        emailError.textContent = "Email address is required.";
      } else if (!emailPattern.test(register_email.value)) {
        isValid = false;
        document.getElementById("register_email").style.borderColor = "red";
        emailError.textContent = "Valid email address is required.";
      } else {
        document.getElementById("register_email").style.borderColor =
          "rgb(223, 226, 232)";
        emailError.textContent = "";
        isValid = false;
      }

      register_tabs[1].click();
    } else if (
      document.getElementById("address-detail").style.display === "block"
    ) {
      addresses.forEach((address) => {
        const short_address = document.getElementById(
          `register_short_address_${address.id}`
        );
        const primary_address = document.getElementById(
          `register_primary_address_${address.id}`
        );
        const building_number = document.getElementById(
          `register_building_number_${address.id}`
        );
        const street_name = document.getElementById(
          `register_street_name_${address.id}`
        );
        const secondary = document.getElementById(
          `register_secondary_${address.id}`
        );
        const district = document.getElementById(
          `register_district_${address.id}`
        );
        const postal_code = document.getElementById(
          `register_postal_code_${address.id}`
        );
        const city = document.getElementById(`register_city_${address.id}`);

        const short_address_error = document.getElementById(
          `shortAddressError_${address.id}`
        );
        const primary_address_error = document.getElementById(
          `primaryAddressError_${address.id}`
        );
        const street_name_error = document.getElementById(
          `streetNameError_${address.id}`
        );
        const district_error = document.getElementById(
          `districtError_${address.id}`
        );
        const city_error = document.getElementById(`cityError_${address.id}`);

        short_address_error.textContent = "";
        primary_address_error.textContent = "";

        if (short_address.value == "") {
          isValid = false;
          short_address.style.borderColor = "red";
          short_address_error.textContent = "Short address is required.";
        } else {
          short_address.style.borderColor = "rgb(223, 226, 232)";
          short_address_error.textContent = "";
        }

        if (primary_address.value == "") {
          isValid = false;
          primary_address.style.borderColor = "red";
          primary_address_error.textContent = "Primary address is required.";
        } else {
          primary_address.style.borderColor = "rgb(223, 226, 232)";
          primary_address_error.textContent = "";
        }

        if (building_number.value == "") {
          isValid = false;
          building_number.style.borderColor = "red";
        } else {
          building_number.style.borderColor = "rgb(223, 226, 232)";
        }

        if (street_name.value == "") {
          isValid = false;
          street_name.style.borderColor = "red";
          street_name_error.textContent = "Street name is required.";
        } else {
          street_name.style.borderColor = "rgb(223, 226, 232)";
          street_name_error.textContent = "";
        }

        if (secondary.value == "") {
          isValid = false;
          secondary.style.borderColor = "red";
        } else {
          secondary.style.borderColor = "rgb(223, 226, 232)";
        }

        if (district.value == "") {
          isValid = false;
          district.style.borderColor = "red";
          district_error.textContent = "District is required.";
        } else {
          district.style.borderColor = "rgb(223, 226, 232)";
          district_error.textContent = "";
        }

        if (postal_code.value == "") {
          isValid = false;
          postal_code.style.borderColor = "red";
        } else {
          postal_code.style.borderColor = "rgb(223, 226, 232)";
        }

        if (city.value == "") {
          isValid = false;
          city.style.borderColor = "red";
          city_error.textContent = "City is required.";
        } else {
          city.style.borderColor = "rgb(223, 226, 232)";
          city_error.textContent = "";
        }
      });
      register_tabs[2].click();
    } else if (
      document.getElementById("delivery-time-detail").style.display === "block"
    ) {
      const btnLoader = document.querySelector(".register-btn-loader");
      const btnText = document.querySelector(".register-btn-text");

      btnLoader.style.display = "block";
      btnText.style.display = "none";
      let data = {
        action: "register_user",
        first_name: firstName.value,
        last_name: lastName.value,
        email: register_email.value,
        profile: {
          primary_contact:
            register_primary_phone_number_code.value +
            "" +
            register_primary_phone_number.value,
          secondary_contact:
            register_secondary_phone_number_code.value +
            "" +
            register_secondary_phone_number.value,
        },
        addresses: addresses,
        delivery_preferences: deliveryPreferences,
      };

      jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            successMessage.classList.add("active");
            successMessageText.innerHTML =
              "User registered successfully please wait.";
            errorMessage.classList.remove("active");
            errorMessage.innerHTML = "";
            btnLoader.style.display = "none";
            btnText.style.display = "block";
            if (popupData && !popupData.is_open) {
              window.location.href = "/checkout?quick-c-checkout=true";
            } else {
              window.location.reload(true);
            }
          } else {
            successMessage.classList.remove("active");
            successMessageText.innerHTML = "";
            errorMessage.classList.add("active");
            if (response.errors.profile) {
              errorMessage.innerHTML =
                response.errors.profile.primary_contact[0];
            }

            if (response.errors.email) {
              errorMessage.innerHTML = response.errors.email[0];
            }
            btnLoader.style.display = "none";
            btnText.style.display = "block";
          }
          btnLoader.style.display = "none";
          btnText.style.display = "block";
        },
        error: function () {
          btnLoader.style.display = "none";
          btnText.style.display = "block";
        },
      });
    }
  });

  const editAddressButton = document.querySelector("#edit-address-button");

  if (editAddressButton && popupData.quick_c_checkout) {
    editAddressButton.addEventListener("click", function () {
      fetchUserDetails(email.value);
      document.getElementById("QCWC_addressesModal").style.display = "flex";
      document.body.style.overflow = "hidden";
    });
  }

  const QCWC_loginModal_close_btn = document.querySelector(
    ".QCWC_loginModal_close_btn"
  );

  if (QCWC_loginModal_close_btn) {
    QCWC_loginModal_close_btn.addEventListener("click", function () {
      document.getElementById("QCWC_loginModal").style.display = "none";
      document.body.style.overflow = "auto";
    });
  }

  const QCWC_addressesModal_close_btn = document.querySelector(
    ".QCWC_addressesModal_close_btn"
  );

  if (QCWC_addressesModal_close_btn) {
    QCWC_addressesModal_close_btn.addEventListener("click", function () {
      document.getElementById("QCWC_addressesModal").style.display = "none";
      document.body.style.overflow = "auto";
    });
  }

  const QCWC_prefencesModal_close_btn = document.querySelector(
    ".QCWC_prefencesModal_close_btn"
  );

  if (QCWC_prefencesModal_close_btn) {
    QCWC_prefencesModal_close_btn.addEventListener("click", function () {
      document.getElementById("QCWC_prefencesModal").style.display = "none";
      document.body.style.overflow = "auto";
    });
  }

  const editPrefencesButton = document.querySelector("#edit-prefences-button");

  if (editPrefencesButton) {
    editPrefencesButton.addEventListener("click", function () {
      fetchUserDetails(email.value);
      document.getElementById("QCWC_prefencesModal").style.display = "flex";
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

  function closePrefencesModal() {
    const modal = document.getElementById("QCWC_prefencesModal");
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
          user_addresses.innerHTML = "";
          user_delivery_prefences_list.innerHTML = "";

          if (userDetail.addresses.length !== 0) {
            userDetail.addresses.forEach((address) => {
              userAddressesHtml += `
            <div class="address">
              <label class="custom-radio">
                <input type="radio" name="user_address" value="${address.id}"
                data-latitude="${
                  address.latitude && address.latitude !== null
                    ? address.latitude
                    : ""
                }"
                data-longitude="${
                  address.longitude && address.longitude !== null
                    ? address.longitude
                    : ""
                }"
                data-unit_number="${
                  address.unit_number && address.unit_number !== null
                    ? address.unit_number
                    : ""
                }"
                data-district="${
                  address.district && address.district !== null
                    ? address.district
                    : ""
                }"
                data-street_name="${
                  address.street_name && address.street_name !== null
                    ? address.street_name
                    : ""
                }"
                data-building_number="${
                  address.building_number && address.building_number !== null
                    ? address.building_number
                    : ""
                }"
                data-region="${
                  address.region && address.region !== null
                    ? address.region
                    : ""
                }" data-short_address="${
                address.short_address && address.short_address !== null
                  ? address.short_address
                  : ""
              }" data-primary_address="${
                address.primary_address && address.primary_address !== null
                  ? address.primary_address
                  : ""
              }" data-secondary_address="${
                address.secondary && address.secondary !== null
                  ? address.secondary
                  : ""
              }" data-city="${address.city}" data-postal_code="${
                address.postal_code
              }" ${address.is_primary ? "checked" : ""} />
             <span class="radio-custom"></span>
                ${
                  address.primary_address && address.primary_address !== null
                    ? address.primary_address
                    : ""
                }
                ${
                  address.secondary && address.secondary !== null
                    ? address.secondary
                    : ""
                }
                , ${address.city}, ${address.postal_code}
              </label>
              </div>
            `;
            });
          } else {
            userAddressesHtml += "<p>No Addresses Found!</p>";
          }

          if (userDetail.delivery_preferences.length !== 0) {
            userDetail.delivery_preferences.forEach((userDelivery) => {
              const startTime =
                userDelivery.start_time && userDelivery.start_time !== null
                  ? userDelivery.start_time
                  : "";
              const endTime =
                userDelivery.end_time && userDelivery.end_time !== null
                  ? userDelivery.end_time
                  : "";
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
                <span>${
                  userDelivery.day && userDelivery.day !== null
                    ? userDelivery.day
                    : ""
                } ( ${
                startTime ? startTime + " -" : startTime
              } ${endTime} )</span>
              </label>
            </div>
            `;
            });
          } else {
            userDeliveryPrefencesHtml += "<p>No Delivery Prefences Found!</p>";
          }

          user_addresses.innerHTML += userAddressesHtml;
          user_delivery_prefences_list.innerHTML += userDeliveryPrefencesHtml;
          user_address_loader.style.display = "none";
          user_delivery_prefences_loader.style.display = "none";
          errorMessage1.classList.remove("active");
          errorMessage1.innerHTML = "";
          errorMessage2.classList.remove("active");
          errorMessage2.innerHTML = "";
        } else {
          user_address_loader.style.display = "none";
          user_delivery_prefences_loader.style.display = "none";
          errorMessage1.classList.add("active");
          errorMessage1.innerHTML =
            response.errors.detail + " Waiting Logout...";
          errorMessage2.classList.add("active");
          errorMessage2.innerHTML =
            response.errors.detail + " Waiting Logout...";
          quick_c_logout_btn.click();
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
      // const selectedPrefence = document.querySelector(
      //   'input[name="delivery_preference"]:checked'
      // );

      const btnLoader = document.querySelector(".confirm-btn-loader");
      const btnText = document.querySelector(".confirm-btn-text");
      const primaryAddress = selectedAddress.getAttribute(
        "data-primary_address"
      );
      const secondaryAddress = selectedAddress.getAttribute(
        "data-secondary_address"
      );
      const billingShortAddress =
        selectedAddress.getAttribute("data-short_address");
      const buildingNumber = selectedAddress.getAttribute(
        "data-building_number"
      );
      const streetName = selectedAddress.getAttribute("data-street_name");
      const district = selectedAddress.getAttribute("data-district");
      const unit_number = selectedAddress.getAttribute("data-unit_number");
      const region = selectedAddress.getAttribute("data-region");
      const latitude = selectedAddress.getAttribute("data-latitude");
      const longitude = selectedAddress.getAttribute("data-longitude");
      const city = selectedAddress.getAttribute("data-city");
      const postalCode = selectedAddress.getAttribute("data-postal_code");

      // const day = selectedPrefence.getAttribute("data-day");
      // const start_time = selectedPrefence.getAttribute("data-start_time");
      // const end_time = selectedPrefence.getAttribute("data-end_time");

      const data = {
        action: "save_user_detail",
        first_name: userDetail?.first_name,
        last_name: userDetail?.last_name,
        phone: userDetail.profile.primary_contact,
        primary_address: primaryAddress,
        secondary_address: secondaryAddress,
        billing_short_address: billingShortAddress,
        building_number: buildingNumber,
        street_name: streetName,
        district: district,
        unit_number: unit_number,
        region: region,
        latitude: latitude,
        longitude: longitude,
        city: city,
        postal_code: postalCode,
        // day: day,
        // start_time: start_time,
        // end_time: end_time,
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

  document
    .getElementById("confirmPrefenceButton")
    .addEventListener("click", function () {
      const selectedPrefence = document.querySelector(
        'input[name="delivery_preference"]:checked'
      );

      const btnLoader1 = document.querySelector(".confirm-btn-loader1");
      const btnText1 = document.querySelector(".confirm-btn-text1");

      const day = selectedPrefence.getAttribute("data-day");
      const start_time = selectedPrefence.getAttribute("data-start_time");
      const end_time = selectedPrefence.getAttribute("data-end_time");

      const data = {
        action: "save_user_delivery_prefence_detail",
        day: day,
        start_time: start_time,
        end_time: end_time,
      };

      if (selectedPrefence) {
        btnLoader1.style.display = "block";
        btnText1.style.display = "none";
        document.querySelector(".prefence-detail-content").style.opacity =
          "0.6";
        jQuery.ajax({
          url: ajaxurl,
          type: "POST",
          data: data,
          success: function (response) {
            btnLoader1.style.display = "none";
            btnText1.style.display = "block";
            document.querySelector(".prefence-detail-content").style.opacity =
              "1";
            closePrefencesModal();
            window.location.reload();
          },
          error: function () {
            alert("There was an error with the request.");
            btnLoader1.style.display = "none";
            btnText1.style.display = "block";
            document.querySelector(".prefence-detail-content").style.opacity =
              "1";
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
        if (popupData && !popupData.is_open) {
          window.location.href = "/checkout?quick-c-checkout=true";
        } else {
          window.location.reload(true);
        }
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
          document.querySelector(".login-content").style.pointerEvents = "all";
          document.querySelector(".login-content").style.opacity = "1";
          fetchUserDetails(email.value);
          savePrimaryUserDetail(email.value);
        } else if (
          response.status_code === 401 ||
          response.status_code === 400
        ) {
          stopApiKeyCheck();
          document.querySelector(".login-content").style.display = "none";
          document.querySelector(".register-content").style.display = "none";
          document.querySelector(".verification-content").style.display =
            "block";
          document.querySelector(".otp-content").style.display = "none";
          successMessage.classList.remove("active");
          document.querySelector(".login-content").style.pointerEvents = "all";
          document.querySelector(".login-content").style.opacity = "1";
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
    } else {
      email.style.borderColor = "#dfe2e8";
      errorText1.classList.remove("active");
      errorText1.innerHTML = "";
    }

    const data = {
      action: "authenticate_user",
      email: email.value,
    };

    if (email.value && email.value !== "") {
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
            savePrimaryUserDetail(emailValue);
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

  quick_c_logout_btn.addEventListener("click", () => {
    const btnLoader = quick_c_logout_btn.querySelector(".btn-loader");
    const btnText = quick_c_logout_btn.querySelector(".btn-text");

    btnLoader.style.display = "block";
    btnText.style.display = "none";

    const data = {
      action: "logout_quick_c",
    };

    jQuery.ajax({
      url: ajaxurl,
      data: data,
      type: "POST",
      success: function (response) {
        window.location.reload(true);
        btnLoader.style.display = "none";
        btnText.style.display = "block";
        closeAddressesModal();
      },
      error: function () {
        alert("There was an error with the request.");
        btnLoader.style.display = "none";
        btnText.style.display = "block";
      },
    });
  });
});
