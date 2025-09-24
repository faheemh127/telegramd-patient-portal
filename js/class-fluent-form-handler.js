class HldFluentFormHandler {
  constructor() {
    // @todo uncomment this on production
    this.hideBmiNextBtn();
    this.hldhideNext("hld_gender_wrap");
    this.hldhideNext("hld_state_wrap");
    this.hldhideNext("hld_medication_wrap");
    this.hldhideNext("hld_packages_wrap");
    this.initCustomizedData();
  }

  initCustomizedData() {
    this.initMedications();
  }

  // initMedications() {
  //   const wrap = document.getElementById("hldGlpMedicationWrap");
  //   if (!wrap) return;

  //   // clear previous dummy data
  //   wrap.innerHTML = "";

  //   let html = "";

  //   fluentFormData.medications.forEach((med) => {
  //     // extract name & "Most Popular" star if available
  //     const nameParts = med.medication_name.split("(");
  //     const medName = nameParts[0].trim();
  //     const extraLabel = nameParts[1]
  //       ? nameParts[1].replace(")", "").trim()
  //       : "";

  //     // take first package price & desc (fallback if needed)
  //     const firstPackage = med.packages[0] || {};
  //     const price = firstPackage.price || "$147 / Month";

  //     // parse description (split into features if possible)
  //     const descList = firstPackage.desc
  //       ? firstPackage.desc.split("•").map((d) => d.trim())
  //       : ["Weekly injection", "Compounded", "Feature not available"];

  //     // build features list
  //     const featuresHTML = descList.map((f) => `<li>${f}</li>`).join("");

  //     // build medication block
  //     html += `
  //     <div class="hld-custom-checkbox hld-medicine" data-value="${medName}">
  //       <div class="med-box">
  //         <div class="badges">
  //           <span class="hld-badge hld-badge-blue">Free Evaluation</span>
  //           <span class="hld-badge hld-badge-pink">Weekly Injection</span>
  //         </div>
  //         <div class="med-compounded">Compounded</div>
  //         <div class="med-title">
  //           ${medName} ${
  //       extraLabel ? `<span class="star">${extraLabel}</span>` : ""
  //     }
  //         </div>
  //         <div class="med-price">$${price}/month</div>
  //         <ul class="med-features">
  //           ${featuresHTML}
  //         </ul>
  //       </div>
  //     </div>
  //   `;
  //   });

  //   // insert built HTML inside wrapper
  //   wrap.innerHTML = html;
  // }
  initMedications() {
    const wrap = document.getElementById("hldGlpMedicationWrap");
    if (!wrap) return;

    wrap.innerHTML = ""; // clear previous

    let html = "";

    fluentFormData.medications.forEach((med) => {
      // extract name & optional label (like Most Popular)
      const nameParts = med.medication_name.split("(");
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
      <div class="hld-custom-checkbox hld-medicine" data-value="${medName}">
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

  getAmount() {
    return 130;
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
      m.medication_name.includes(medicine)
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
          headerLabel = `<span class="save-label">Save $100 extra</span>`;
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
      'input[name="names[first_name]"]'
    );
    const lastNameInput = container.querySelector(
      'input[name="names[last_name]"]'
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
    console.log("selectEl", selectEl);
    if (selectEl) {
      selectEl.value = value;
      console.log("selectEl12");
      selectEl.dispatchEvent(new Event("input", { bubbles: true }));
      selectEl.dispatchEvent(new Event("change", { bubbles: true }));
      console.log(`Set ${dropdownName} to ${value}`);
    } else {
      console.warn(`Dropdown with name "${dropdownName}" not found.`);
    }
  }
}

const hldFormHandler = new HldFluentFormHandler();
