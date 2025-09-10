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

  initMedications() {
    const wrap = document.getElementById("hldGlpMedicationWrap");
    if (!wrap) return;

    // clear previous dummy data
    wrap.innerHTML = "";

    let html = "";

    fluentFormData.medications.forEach((med) => {
      // extract name & "Most Popular" star if available
      const nameParts = med.medication_name.split("(");
      const medName = nameParts[0].trim();
      const extraLabel = nameParts[1]
        ? nameParts[1].replace(")", "").trim()
        : "";

      // take first package price & desc (fallback if needed)
      const firstPackage = med.packages[0] || {};
      const price = firstPackage.price || "$147 / Month";

      // parse description (split into features if possible)
      const descList = firstPackage.desc
        ? firstPackage.desc.split("•").map((d) => d.trim())
        : ["Weekly injection", "Compounded", "Feature not available"];

      // build features list
      const featuresHTML = descList.map((f) => `<li>${f}</li>`).join("");

      // build medication block
      html += `
      <div class="hld-custom-checkbox hld-medicine" data-value="${medName}">
        <div class="med-box">
          <div class="badges">
            <span class="hld-badge hld-badge-blue">Free Evaluation</span>
            <span class="hld-badge hld-badge-pink">Weekly Injection</span>
          </div>
          <div class="med-compounded">Compounded</div>
          <div class="med-title">
            ${medName} ${
        extraLabel ? `<span class="star">${extraLabel}</span>` : ""
      }
          </div>
          <div class="med-price">${price}</div>
          <ul class="med-features">
            ${featuresHTML}
          </ul>
        </div>
      </div>
    `;
    });

    // insert built HTML inside wrapper
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

// Create an object of this class
const hldFormHandler = new HldFluentFormHandler();
// Example usage:
