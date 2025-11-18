class HLDTelegra {
  constructor() {
    this.initRefundListener();
  }

  // Attach click listener to all refund buttons
  initRefundListener() {
    jQuery(document).on("click", ".hldRequestRefund", (e) => {
      e.preventDefault();
      this.handleRefundRequest(e.currentTarget);
    });
  }

  // Main function that handles refund request
  handleRefundRequest(buttonElement) {
    const $btn = jQuery(buttonElement);

    // Find telegra_order_id anywhere in DOM
    const orderId = jQuery('input[name="telegra_order_id"]').val();

    if (!orderId) {
      alert("❌ No telegra_order_id found in the DOM.");
      return;
    }

    $btn.text("Requesting...").prop("disabled", true);

    jQuery.ajax({
      url: hld_ajax_obj.ajaxurl,
      type: "POST",
      dataType: "json",
      data: {
        action: "hld_request_refund",
        nonce: hld_ajax_obj.nonce,
        telegra_order_id: orderId,
      },
      success: (response) => {
        if (response.success) {
          alert("✅ Refund request submitted successfully.");
        } else {
          alert("⚠️ " + (response.data?.message || "Request failed."));
        }
      },
      error: () => {
        alert("❌ AJAX Error occurred.");
      },
      complete: () => {
        $btn.text("Request Refund").prop("disabled", false);
      },
    });
  }
}

// Initialize class
jQuery(document).ready(() => {
  new HLDTelegra();
});

// class code ends

jQuery(document).ready(function ($) {
  $(document).on("click", "#hldViewOrderDetail", function (e) {
    e.preventDefault();

    const $this = $(this);
    const orderId = $this.data("order-id");

    if (!orderId) {
      console.error("No order ID found on element.");
      return;
    }

    // Show professional loading state
    $this.text("Fetching Details...").prop("disabled", true);

    $.ajax({
      url: hld_ajax_obj.ajaxurl,
      type: "POST",
      dataType: "json",
      data: {
        action: "hld_view_order_detail",
        nonce: hld_ajax_obj.nonce,
        order_id: orderId,
      },
      success: function (response) {
        if (response.success) {
          console.log("Order Detail:", response.data);
          // alert('✅ Order fetched successfully! Check console for full details.');
          renderOrderDetail(response.data);
          $("#hldOrdersWrap").addClass("hidden");
        } else {
          alert(
            "⚠️ " + (response.data?.message || "Failed to fetch order details.")
          );
          $("#hldOrdersWrap").removeClass("hidden");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        alert("❌ Error while fetching order details. Please try again.");
        $("#hldOrdersWrap").removeClass("hidden");
      },
      complete: function () {
        // Restore button text after request
        $this.text("View Details").prop("disabled", false);
      },
    });

    function renderOrderDetail(order) {
      if (!order || !order.id) {
        $("#hldOrderDetailBox").html(
          '<div class="alert alert-warning">No order details available.</div>'
        );
        return;
      }

      const patient = order.patient || {};
      const address = order.address?.shipping || {};
      const product = order.productVariations?.[0]?.productVariation || {};
      const productName = product.product?.title || "N/A";
      const affiliate = order.affiliate?.name || "N/A";

      // Format date nicely
      const createdAt = new Date(order.createdAt).toLocaleString();

      // Construct HTML
      const html = `
  <div class="card shadow-sm hld-order-card">
    <div class="card-header">
      <h5 class="mb-0">Order #${order.orderNumber}</h5>
    </div>
    <div class="card-body">

      <!-- Patient Info -->
      <div class="row mb-4">
        <div class="col-md-6">
          <h6 class="text-uppercase text-muted mb-2">Patient Information</h6>
          <p class="mb-1"><strong>Name:</strong> ${patient.name || "N/A"}</p>
          <p class="mb-1"><strong>Email:</strong> ${patient.email || "N/A"}</p>
          <p class="mb-1"><strong>Phone:</strong> ${patient.phone || "N/A"}</p>
          <p class="mb-0"><strong>Date of Birth:</strong> ${
            patient.dateOfBirth
              ? new Date(patient.dateOfBirth).toLocaleDateString()
              : "N/A"
          }</p>
        </div>
      </div>

      <!-- Order Info -->
      <h6 class="text-uppercase text-muted mb-2">Order Details</h6>
      <div class="row mb-4">
        <div class="col-md-6">
          <p class="mb-1"><strong>Status:</strong> <span class="badge bg-info text-white text-capitalize">${
            order.status
          }</span></p>
          <p class="mb-1"><strong>Created:</strong> ${createdAt}</p>
          
          <p class="mb-1"><strong>Product:</strong> ${productName}</p>
          <p class="mb-0"><strong>Form:</strong> ${product.form || "N/A"}</p>
        </div>
        <div class="col-md-6">
          <p class="mb-1"><strong>Strength:</strong> ${
            product.strength || "N/A"
          }</p>
          <p class="mb-1"><strong>Duration:</strong> ${
            product.typicalDuration || "N/A"
          } days</p>
        
          
        </div>
      </div>

      <!-- Address Info -->
      <h6 class="text-uppercase text-muted mb-2">Shipping Address</h6>
      <p class="mb-1">${address.address1 || ""}</p>
      <p class="mb-1">${address.city || ""}, ${address.state?.name || ""}</p>
      <p class="mb-1">${address.zipcode || ""}</p>
      <p class="mb-0"><strong>State:</strong> ${
        address.state?.abbreviation || ""
      }</p>

      <!-- Symptoms -->
      ${
        order.symptoms && order.symptoms.length
          ? `
        <h6 class="text-uppercase text-muted mt-4 mb-2">Symptoms Reported</h6>
        <div class="hld-symptom-list list-unstyled">
          ${order.symptoms
            .map(
              (sym) =>
                `<span class="badge bg-secondary me-2 mb-2">${sym.name}</span>`
            )
            .join("")}
        </div>`
          : ""
      }

      <!-- Back Button -->
      <div class="text-center mt-4">
        <button id="hldBackToOrders" class="btn btn-outline-primary hld-back-to-orders">
          <i class="bi bi-arrow-left"></i> Back to Orders
        </button>
      </div>
    </div>
  </div>
  `;

      $("#hldOrderDetailBox").html(html).removeClass("hidden");
    }

    $(document).on("click", "#hldBackToOrders", function () {
      $("#hldOrderDetailBox").addClass("hidden").empty();
      $("#hldOrdersWrap").removeClass("hidden");
    });
  });
});
