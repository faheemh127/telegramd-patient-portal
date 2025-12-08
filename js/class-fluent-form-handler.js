class HldFluentFormHandler {
  constructor() {
    // @todo uncomment this on production

    this.hasFired = false;
    this.hideBmiNextBtn();
    this.hldhideNext("hld_gender_wrap");
    this.hldhideNext("hld_state_wrap");
    this.hldhideNext("hld_medication_wrap");
    this.hldhideNext("hld_packages_wrap");
    this.initCustomizedData();
    this.removeOptinLabelBorder();
    // on page refresh or on nextendsocial login its importalt to call this function so it can pass price to strip handler class and show data on summary page
    // hldFormHandler.getAmount();
    // hldFormHandler.setStripeData();
  }

  initCustomizedData() {
    this.initMedications();
    let that = this;

    jQuery(document).ready(function ($) {
      var $steps = $(".fluentform-step");

      $("select").each((_, s) => {
        jQuery(s).on("change", (e) => {
          const selectName = jQuery(e.currentTarget).attr("name");
          let curVal = jQuery(e.currentTarget).val();
          let el = $(`[data-value="${curVal}"]`);
          el.addClass("active");
        });
      });

      if ($steps.length > 0) {
        let lastStepNode = $steps[$steps.length - 1];
        let fluentFrom = $($("Form")[$("form").length - 1]).attr("id");
        let formId = fluentFrom.split("_")[1];
        let cookieName = `fluentform_step_form_hash_${formId}`;

        let hasCookie = document.cookie.split(";").some(function (item) {
          return item.trim().startsWith(cookieName + "=");
        });

        if (!hasCookie) {
          jQuery(".hld_form_wrap_hidden").removeClass("hld_form_wrap_hidden");
          hldNavigation.toggleLoader(false);
        }
        //This is a bad  workaround to use setTimeout to to unhide the element. There is
        //no way to detect this is the right time to show the user the element.I am
        //using 2seconds just by trail and error and  by seeing other values used as high as
        //9 seconds. This is would only run when the user has interacted with form and
        //clicked all the way back to the frist step of the form and left and is now returning
        //again.
        else
          setTimeout(function () {
            jQuery(".hld_form_wrap_hidden").removeClass("hld_form_wrap_hidden");
          }, 8000);

        function executeLastStepCode() {
          hldFormHandler.getAmount();
          hldFormHandler.setStripeData();
          if ($("body").hasClass("logged-in") && !hldFormHandler.hasFired) {
            // todoGHL origional number of patietn
            const phone = "+923068493810";

            const subResult = fetch(MyStripeData.ajax_url, {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `action=activate_reminder&phone=${encodeURIComponent(
                phone,
              )}`,
            });
            hldFormHandler.hasFired = true;
          }
        }

        var observer = new MutationObserver(function (mutations) {
          mutations.forEach(function (mutation) {
            if (mutation.type === "attributes") {
              var $lastStep = $(lastStepNode);
              var hasData = false;

              $steps.each(function (i, step) {
                // if ($(step).hasClass("active") && i > 1)

                if ($(step).hasClass("active") && $(step).is(":visible")) {
                  $(step)
                    .find("input, select, textarea")
                    .each(function () {
                      var field = $(this);
                      var type = field.attr("type");
                      var tagName = field.prop("tagName").toLowerCase();

                      if (
                        type === "hidden" ||
                        type === "button" ||
                        type === "submit"
                      )
                        return;

                      if (type === "checkbox" || type === "radio") {
                        if (field.is(":checked")) {
                          hasData = true;
                          return false;
                        }
                      } else if (tagName === "select") {
                        var val = field.val();

                        if (Array.isArray(val) && val.length > 0) {
                          hasData = true;
                          return false;
                        } else if (val !== null && val !== "") {
                          hasData = true;
                          return false;
                        }
                      } else {
                        if (field.val() && field.val().trim() !== "") {
                          hasData = true;
                          return false;
                        }
                      }
                    });
                }

                if (hasData) {
                  const nextBtn = step.querySelector(
                    'button[data-action="next"]',
                  );

                  if (nextBtn) {
                    nextBtn.classList.remove("hld-hidden");
                    nextBtn.style.visibility = "visible";
                    nextBtn.style.display = "block";
                  }
                }

                if (
                  i == $steps.length - 2 &&
                  $("body").hasClass("logged-in") &&
                  $(step).hasClass("active")
                ) {
                  const stepElement = document.querySelector(".hld_login_wrap");
                  const nextButton = stepElement.querySelector(
                    'button[data-action="next"]',
                  );
                  nextButton.click();
                }

                if (
                  i == $steps.length - 1 &&
                  !$("body").hasClass("logged-in") &&
                  $($lastStep).hasClass("active")
                ) {
                  const activeStep = document.querySelector(
                    ".fluentform-step.active",
                  );
                  const prevButton = activeStep.querySelector(".ff-btn-prev");
                  prevButton.click(); // Trigger FluentForm's previous step
                }

                if ($(step).hasClass("active")) {
                  hldNavigation.toggleLoader(false);
                  $(".hld_form_wrap_hidden").removeClass(
                    "hld_form_wrap_hidden",
                  );
                }
              });

              if ($lastStep.hasClass("active") || $lastStep.is(":visible")) {
                if (!hldFormHandler.hasFired) {
                  executeLastStepCode.call(that);
                }
              }
            }
          });
        });

        $steps.each((i, step) =>
          observer.observe(step, {
            attributes: true,
            attributeFilter: ["class"],
          }),
        );
      }
    });
  }

  initMedications(selectedMedication = "") {
    console.log("initMedications function recieve value", selectedMedication);
    const wrap = document.getElementById("hldGlpMedicationWrap");
    if (!wrap) return;

    wrap.innerHTML = ""; // clear previous

    let html = "";

    let filteredMeds = "";

    if (selectedMedication == "") {
      filteredMeds = fluentFormData.medications;
    } else {
      filteredMeds = fluentFormData.medications.filter(
        (med) =>
          med.medication.toLowerCase() === selectedMedication.toLowerCase(),
      );
    }

    console.log("filtered medications", filteredMeds);
    filteredMeds.forEach((med) => {
      // extract name & optional label (like Most Popular)
      const nameParts = med.medication_name.split("(");
      const telegraID = med.telegra_code;
      const medName = nameParts[0].trim();
      const extraLabel = nameParts[1]
        ? nameParts[1].replace(")", "").trim()
        : "";

      // parse labels → badges
      const labels = med.labels
        ? med.labels.split(",").map((l) => l.trim())
        : [];
      const badgesHTML = labels
        .map((label, i) => {
          const colors = ["blue", "pink", "green", "purple"]; // fallback color cycle
          const color = colors[i % colors.length];
          return `<span class="hld-badge hld-badge-${color}">${label}</span>`;
        })
        .join("");

      // parse description → features
      const descList = med.description
        ? med.description.split(",").map((d) => d.trim())
        : [];
      const featuresHTML = descList.map((f) => `<li>${f}</li>`).join("");

      // find price for 1 month package
      const oneMonthPkg = med.packages.find((p) => p.monthly_duration === "1");
      const price = oneMonthPkg
        ? `$${oneMonthPkg.monthly_price}/month`
        : "$147/month"; // fallback

      // build medication block
      html += `
      <div class="hld-custom-checkbox hld-medicine" data-value="${medName}" data-telegra-id="${telegraID}">
        <div class="med-box">
          <div class="badges">${badgesHTML}</div>
          <div class="med-title">
            ${medName} ${
              extraLabel ? `<span class="star">${extraLabel}</span>` : ""
            }
          </div>
          <div class="med-price">${price}</div>
          <ul class="med-features">${featuresHTML}</ul>
        </div>
      </div>
    `;
    });

    wrap.innerHTML = html;
  }

  hldhideNext(wrapperClass) {
    const parent = document.querySelector(`.${wrapperClass}`);
    if (parent) {
      const btn = parent.querySelector("div .ff-btn-next");
      if (btn) {
        btn.classList.add("hld-hidden");
      }
    }
  }

  // setStripeData() {
  //   const dropdown2 = document.querySelector('[name="dropdown_4"]');
  //   const dropdown3 = document.querySelector('[name="dropdown_3"]');

  //   const medication = dropdown2 ? dropdown2.value : null;
  //   const value3 = dropdown3 ? dropdown3.value : null;

  //   console.log("Function setStripeData");
  //   console.log("dropdown_2 selected value:", medication);
  //   console.log("dropdown_3 selected value:", value3);

  //   // ✅ Set medication text to the div
  //   const summaryDiv = document.getElementById("hldSummaryMedication");
  //   if (summaryDiv) {
  //     summaryDiv.textContent = medication
  //       ? medication
  //       : "No medication selected";
  //   }

  //   // ✅ Set package duration and update window.stripeHandler.packageDuration
  //   const durationDiv = document.getElementById("hldSummaryPackageDuration");
  //   if (durationDiv) {
  //     if (value3 === "Monthly") {
  //       durationDiv.textContent = "1 Month";
  //       window.stripeHandler.packageDuration = 1;
  //     } else if (value3 === "3-Month") {
  //       durationDiv.textContent = "3 Months";
  //       window.stripeHandler.packageDuration = 3;
  //     } else if (value3 === "6-Month") {
  //       durationDiv.textContent = "6 Months";
  //       window.stripeHandler.packageDuration = 6;
  //     } else {
  //       durationDiv.textContent = "No plan selected";
  //       window.stripeHandler.packageDuration = 1; // fallback default
  //     }
  //   } else {
  //     console.log("Duration div not found");
  //   }
  // }
  getSelectedMedication() {
    const dropdown2 = document.querySelector('[name="dropdown_4"]');
    const medication =
      dropdown2 && dropdown2.value.trim() !== "" ? dropdown2.value : null;
    return medication;
  }

  setDOB(year, month, day) {
    const selectYear = document.querySelector(".dOb_Y");
    const selectMonth = document.querySelector(".dOb_M");
    const selectDay = document.querySelector(".dOb_D");

    if (selectYear) {
      selectYear.value = year;
      // selectYear.dispatchEvent(new Event("change"));
    }

    if (selectMonth) {
      selectMonth.value = month;
      // selectMonth.dispatchEvent(new Event("change"));
    }

    if (selectDay) {
      selectDay.value = day;
      // selectDay.dispatchEvent(new Event("change"));
    }
  }
  setStripeData() {
    const dropdown3 = document.querySelector('[name="dropdown_3"]');

    const medication = this.getSelectedMedication();

    const value3 =
      dropdown3 && dropdown3.value.trim() !== "" ? dropdown3.value : null;

    // If both dropdowns are empty, exit early
    if (!medication && !value3) {
      console.log("No valid dropdown values — exiting.");
      return;
    }

    console.log("Function setStripeData");
    console.log("dropdown_2 selected value:", medication);
    console.log("dropdown_3 selected value:", value3);

    // ✅ Only update medication div if dropdown2 has a value
    if (medication) {
      const summaryDiv = document.getElementById("hldSummaryMedication");
      if (summaryDiv) {
        summaryDiv.textContent = medication;
      }
    }

    // ✅ Only update duration div if dropdown3 has a value
    if (value3) {
      const durationDiv = document.getElementById("hldSummaryPackageDuration");
      if (durationDiv) {
        if (value3 === "Monthly") {
          durationDiv.textContent = "1 Month";
          window.stripeHandler.packageDuration = 1;
        } else if (value3 === "3-Month") {
          durationDiv.textContent = "3 Months";
          window.stripeHandler.packageDuration = 3;
        } else if (value3 === "6-Month") {
          durationDiv.textContent = "6 Months";
          window.stripeHandler.packageDuration = 6;
        } else {
          console.log("Invalid plan selected");
        }
      } else {
        console.log("Duration div not found");
      }
    }

    if (stripeHandler.isNewPatient()) {
      try {
        const discountWrap = document.getElementById(
          "hldNewPatientDiscountWrap",
        );
        const discountEl = document.getElementById("hldNewPatientDiscount");
        

        // Validate DOM elements
        if (!discountWrap) {
          console.error(
            "❌ Element #hldNewPatientDiscountWrap not found in DOM or #hldDiscountCoupon not found",
          );
          return;
        }
        if (!discountEl) {
          console.error("❌ Element #hldNewPatientDiscount not found in DOM");
          return;
        }

        discountWrap.classList.remove("hidden");

        const duration = stripeHandler.packageDuration;
        const orderTotal = this.getAmount();

        if (!medication) {
          console.error("❌ Medication is undefined when calculating discount");
          return;
        }

        if (typeof stripeHandler.calculateDiscountedPercentage !== "function") {
          console.error("❌ calculateDiscountedPercentage is not a function");
          return;
        }

        const discountPercent = stripeHandler.calculateDiscountedPercentage(
          medication,
          duration,
          orderTotal,
          false,
        );

        if (isNaN(discountPercent)) {
          console.error("❌ Discount calculation returned NaN", {
            medication,
            duration,
            orderTotal,
          });
          return;
        }

        // calclate discount amount
        const discountedAmount =
          orderTotal - orderTotal * (discountPercent / 100);

        // setting prices
        discountEl.innerHTML = "$" + discountedAmount;
        
        hldFormHandler.blurOrigionalPrice();
      } catch (err) {
        console.error("❌ Error applying discount:", err);
      }
    }
  }

  blurOrigionalPrice() {
    const elem = document.getElementById("hldSummaryTotalToday");

    if (elem) {
      elem.classList.add("hld-line-through");
    } else {
      console.error("Element with ID 'hldSummaryTotalToday' not found.");
    }
  }
  removeOptinLabelBorder() {
    // Find all elements with the class "optin_cb_container"
    const containers = document.querySelectorAll(".optin_cb_container");

    containers.forEach((container) => {
      // Apply border none with !important
      container.style.setProperty("border", "none", "important");

      // Find label inside the container
      const label = container.querySelector("label");
      if (label) {
        label.style.setProperty("border", "none", "important");
      }
    });
  }

  // getAmount() {
  //   const dropdown2 = document.querySelector('[name="dropdown_4"]');
  //   const dropdown3 = document.querySelector('[name="dropdown_3"]');

  //   const medication = dropdown2 ? dropdown2.value : null;
  //   const value3 = dropdown3 ? dropdown3.value : null;
  //   console.log("getAmount");
  //   console.log("Selected medication:", medication);
  //   console.log("Selected plan:", value3);

  //   let duration = 1; // default
  //   if (value3 === "Monthly") {
  //     duration = 1;
  //   } else if (value3 === "3-Month") {
  //     duration = 3;
  //   } else if (value3 === "6-Month") {
  //     duration = 6;
  //   }

  //   // ✅ Update window.stripeHandler.packageDuration
  //   window.stripeHandler.packageDuration = duration;

  //   let selectedPrice = 0;

  //   // ✅ Find medication in fluentFormData
  //   if (medication && fluentFormData.medications) {
  //     const med = fluentFormData.medications.find((m) =>
  //       m.medication_name.toLowerCase().includes(medication.toLowerCase())
  //     );
  //     if (med) {
  //       const pkg = med.packages.find(
  //         (p) => parseInt(p.monthly_duration, 10) === duration
  //       );

  //       if (pkg) {
  //         selectedPrice = parseInt(pkg.monthly_price, 10);

  //         // ✅ Set window.stripeHandler.priceId
  //         window.stripeHandler.stripePriceId = pkg.stripe_price_id;

  //         // ✅ Update UI
  //         const todayDiv = document.getElementById("hldSummaryTotalToday");
  //         if (todayDiv) {
  //           todayDiv.textContent = selectedPrice;
  //         }
  //       }
  //     }
  //   }

  //   console.log("Amount to charge today:", selectedPrice);
  //   return selectedPrice;
  // }

  getAmount() {
    const dropdown2 = document.querySelector('[name="dropdown_4"]');
    const dropdown3 = document.querySelector('[name="dropdown_3"]');

    const medication =
      dropdown2 && dropdown2.value.trim() !== "" ? dropdown2.value : null;
    const value3 =
      dropdown3 && dropdown3.value.trim() !== "" ? dropdown3.value : null;

    console.log("getAmount");
    console.log("Selected medication:", medication);
    console.log("Selected plan:", value3);

    // If both are empty, stop here
    if (!medication && !value3) {
      console.log("No valid dropdown values found — exiting.");
      return 0;
    }

    let duration = 1; // default
    if (value3 === "Monthly") {
      duration = 1;
    } else if (value3 === "3-Month") {
      duration = 3;
    } else if (value3 === "6-Month") {
      duration = 6;
    }

    // ✅ Update window.stripeHandler.packageDuration
    window.stripeHandler.packageDuration = duration;

    let selectedPrice = 0;

    // ✅ Only proceed if medication has a valid value
    if (medication && fluentFormData.medications) {
      const med = fluentFormData.medications.find((m) =>
        m.medication_name.toLowerCase().includes(medication.toLowerCase()),
      );

      if (med) {
        const pkg = med.packages.find(
          (p) => parseInt(p.monthly_duration, 10) === duration,
        );

        if (pkg) {
          selectedPrice = parseInt(pkg.monthly_price, 10);

          // const discountCouponEl = document.getElementById("hldDiscountCoupon");
          // discountCouponEl.innerHTML(med.coupon);

          document.getElementById("hldOneMonthSupply").textContent = Number(selectedPrice).toFixed(2);
          // ✅ Set window.stripeHandler.priceId
          window.stripeHandler.stripePriceId = pkg.stripe_price_id;

          // ✅ Update UI
          const todayDiv = document.getElementById("hldSummaryTotalToday");
          if (todayDiv) {
            todayDiv.textContent = "$"+selectedPrice;
          }
        }
      }
    }

    console.log("Amount to charge today:", selectedPrice);
    return selectedPrice;
  }

  hideBmiNextBtn() {
    const parent = document.querySelector(".hld_btn_next_bmi");
    if (parent) {
      const btn = parent.querySelector("div .ff-btn-next");
      if (btn) {
        btn.classList.add("hld-hidden");
      }
    }
  }
  showBmiNextBtn() {
    const parent = document.querySelector(".hld_btn_next_bmi");
    if (parent) {
      const btn = parent.querySelector("div .ff-btn-next");
      if (btn) {
        btn.classList.remove("hld-hidden");
      }
    }
  }
  setPackagePrice(medicine) {
    console.log("medicine", medicine);

    // find the full medicine object
    const med = fluentFormData.medications.find((m) =>
      m.medication_name.includes(medicine),
    );
    if (!med) return;

    // build packages HTML
    const packagesHTML = med.packages
      .map((p) => {
        const duration = p.monthly_duration;
        const price = p.monthly_price;
        const descItems = p.desc
          ? p.desc
              .split(",")
              .map((d) => `<li>${d.trim()}</li>`)
              .join("")
          : "";

        let headerTitle = "";
        let headerLabel = "";
        let dataValue = "";
        let priceHTML = "";
        let descriptionHTML = `<div class="package-description"><ul>${descItems}</ul></div>`;

        if (duration === "1") {
          headerTitle = "Monthly";
          dataValue = "Monthly";
          priceHTML = `
          <div class="package-price">
            <span class="hdl_primary_color">
              $<span class="hld-1-month-price">${price}</span>/month
            </span>
          </div>`;
        } else if (duration === "3") {
          headerTitle = "3-Month Commitment";
          headerLabel = `<span class="save-label">Best Value</span>`;
          dataValue = "3-Month";
          priceHTML = `
          <div class="package-price">
            <span class="hdl_primary_color">
              $<span class="hld-3-month-price">${price}</span>/month (billed monthly)
            </span>
          </div>`;
        } else if (duration === "6") {
          headerTitle = "6-Month Upfront (Max Savings)";
          // headerLabel = `<span class="save-label">Save $100 extra</span>`;
          headerLabel = ``;
          dataValue = "6-Month";
          priceHTML = `
          <div class="package-price">
            <span class="hdl_primary_color">
              $<span class="hld-6-month-price">${price}</span> one-time thereafter*
            </span>
          </div>`;
        }

        return `
        <label class="package-option hld-custom-checkbox" data-value="${dataValue}">
          <input hidden name="patient_package" type="radio" value="${dataValue}" />
          <div class="package-box">
            <div class="package-header">
              <strong>${headerTitle}</strong>
              ${headerLabel}
            </div>
            ${priceHTML}
            ${descriptionHTML}
          </div>
        </label>
      `;
      })
      .join("");

    // inject into the DOM
    const wrap = document.querySelector(".hld_patient_packages");
    if (wrap) {
      wrap.innerHTML = packagesHTML;
    }
  }

  getFullNameFromContainer() {
    // Find the container
    const container = document.querySelector(".hld_name_container");
    if (!container) return ""; // return empty if not found

    // Get first and last name inputs using the name attribute
    const firstNameInput = container.querySelector(
      'input[name="names[first_name]"]',
    );
    const lastNameInput = container.querySelector(
      'input[name="names[last_name]"]',
    );

    // Get values safely
    const firstName = firstNameInput ? firstNameInput.value.trim() : "";
    const lastName = lastNameInput ? lastNameInput.value.trim() : "";

    // Concatenate and return
    return `${firstName} ${lastName}`.trim();
  }

  init_google_places() {
    const input = document.getElementById("hld_address_line_1_test");

    if (input) {
      const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ["address"], // you can also use ['geocode']
        componentRestrictions: { country: "us" }, // optional: restrict to a country
      });

      autocomplete.addListener("place_changed", function () {
        const place = autocomplete.getPlace();
        console.log("Selected place:", place);
        // ✅ You can extract more details if you want
        // Example: fill hidden fields for city/state/zip
        // place.address_components.forEach(component => {
        //     console.log(component.types[0], component.long_name);
        // });
      });
    }
  }
  /**
   * Set value for a select element by its name attribute
   * @param {string} dropdownName - The name of the select element (e.g., "dropdown_1")
   * @param {string} value - The value to select (e.g., "Female")
   */
  setDropdownValue(dropdownName, value) {
    const selectEl = document.querySelector(`select[name="${dropdownName}"]`);
    if (selectEl) {
      selectEl.value = value;
      selectEl.dispatchEvent(new Event("input", { bubbles: true }));
      selectEl.dispatchEvent(new Event("change", { bubbles: true }));
    } else {
      console.warn(`Dropdown with name "${dropdownName}" not found.`);
    }
  }
}

var hldFormHandler = new HldFluentFormHandler();

document.addEventListener("DOMContentLoaded", () => {
  setTimeout(() => hldNavigation.toggleLoader(false), 3000);
  // setTimeout(() => {
  //   if (typeof hldFormHandler !== "undefined") {
  //     hldFormHandler.getAmount();
  //     hldFormHandler.setStripeData();
  //   }
  // }, 6000); // 5000ms = 5 seconds
});
