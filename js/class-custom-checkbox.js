/**
 * This class is from now is a single source of truth for nextbtn hanlding of fluent form
 */
class HLD_FFNextBtn {
  constuctor() {
    this.hldhideNext("hld_gender_wrap");
    this.hldhideNext("hld_state_wrap");
    this.hldhideNext("hld_medication_wrap");
    this.hldhideNext("hld_packages_wrap");
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
}

/**
 * Class Custom Checkbox Handling Starts
 */
class hldCustomCheckbox {
  constructor(containerSelector, callback) {
    this.container = document.querySelector(containerSelector);
    this.callback = callback;
    if (!this.container) return;

    // Use event delegation
    this.container.addEventListener("click", (e) => {
      const box = e.target.closest(".hld-custom-checkbox");
      if (!box || !this.container.contains(box)) return;

      this.handleClick(box);
    });
  }

  handleClick(selectedBox) {
    // remove active from all
    console.log("selectedBox by user", selectedBox);

    // If selected checkbox contain the telegra Product ID then pass this id to stripe handler

    if (selectedBox && selectedBox.hasAttribute("data-telegra-id")) {
      const telegraId = selectedBox.getAttribute("data-telegra-id");
      if (window.stripeHandler) {
        window.stripeHandler.telegraProdID = telegraId;
        console.log("TeleGra ID set to stripeHandler:", telegraId);
      } else {
        console.warn("stripeHandler not found on window");
      }
    } else {
      console.warn("Selected box does not have a data-telegra-id attribute");
    }

    // snipet ends

    const boxes = this.container.querySelectorAll(".hld-custom-checkbox");
    boxes.forEach((box) => box.classList.remove("active"));

    // add active to clicked one
    selectedBox.classList.add("active");

    // get value
    const value = selectedBox.getAttribute("data-value");

    // call callback function with value
    if (this.callback) {
      this.callback(value);
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.genderCheckbox = new hldCustomCheckbox(
    ".hld-custom-checkbox-group",
    (value) => {
      console.log("Selected:", value);
      hldFormHandler.setDropdownValue("dropdown_1", value);
    }
  );

  window.hldMedicineOptions = new hldCustomCheckbox(
    ".hld-custom-checkbox-group[data-field='pre_medicine']",
    (value) => {
      console.log("Selected medicine:", value);
      hldFormHandler.setDropdownValue("dropdown_2", value);
      hldFormHandler.setPackagePrice(value);
      hldFormHandler.initMedications(value);
    }
  );

  window.hldMedicineOptions = new hldCustomCheckbox(
    ".hld-custom-checkbox-group[data-field='medicine']",
    (value) => {
      console.log("Selected medicine:", value);
      hldFormHandler.setDropdownValue("dropdown_4", value);
      hldFormHandler.setPackagePrice(value);
    }
  );

  window.hldPatientPackages = new hldCustomCheckbox(
    ".hld_patient_packages",
    (value) => {
      console.log("Selected package:", value);
      hldFormHandler.setDropdownValue("dropdown_3", value);
      hldFormHandler.setStripeData();
      hldFormHandler.getAmount();
    }
  );
});
