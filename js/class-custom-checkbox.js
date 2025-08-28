class hldCustomCheckbox {
  constructor(containerSelector, callback) {
    this.container = document.querySelector(containerSelector);
    this.callback = callback;
    if (!this.container) return;

    this.checkboxes = this.container.querySelectorAll(".hld-custom-checkbox");

    this.checkboxes.forEach((box) => {
      box.addEventListener("click", () => this.handleClick(box));
    });
  }

  handleClick(selectedBox) {
    // remove active from all
    this.checkboxes.forEach((box) => box.classList.remove("active"));

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
    ".hld-custom-checkbox-group[data-field='medicine']",
    (value) => {
      console.log("Selected medicine:", value);
      hldFormHandler.setDropdownValue("dropdown_2", value);
    }
  );

  window.hldPatientPackages = new hldCustomCheckbox(
    ".hld_patient_packages",
    (value) => {
      console.log("Selected package:", value);
      hldFormHandler.setDropdownValue("dropdown_3", value);
    }
  );
});
