class HldFluentFormHandler {
  constructor() {
    console.log("HldFluentFormHandler initialized");
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
