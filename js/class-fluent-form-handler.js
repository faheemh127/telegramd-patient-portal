class HldFluentFormHandler {
  constructor() {
    // @todo uncomment this on production
    this.hideBmiNextBtn();
    this.hldhideNext("hld_gender_wrap");
    this.hldhideNext("hld_state_wrap");
    this.hldhideNext("hld_medication_wrap");
    this.hldhideNext("hld_packages_wrap");
    // no need this function because its already built in available 
    // this.init_google_places();24
    console.log("fluentFormData", fluentFormData);
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
