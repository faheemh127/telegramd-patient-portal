<?php
global $hld_telegra;
global $hld_fluent_handler;
$icon_capsule_tablet = '<svg width="35px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path d="M112 96c-26.5 0-48 21.5-48 48l0 112 96 0 0-112c0-26.5-21.5-48-48-48zM0 144C0 82.1 50.1 32 112 32s112 50.1 112 112l0 224c0 61.9-50.1 112-112 112S0 429.9 0 368L0 144zM554.9 399.4c-7.1 12.3-23.7 13.1-33.8 3.1L333.5 214.9c-10-10-9.3-26.7 3.1-33.8C360 167.7 387.1 160 416 160c88.4 0 160 71.6 160 160c0 28.9-7.7 56-21.1 79.4zm-59.5 59.5C472 472.3 444.9 480 416 480c-88.4 0-160-71.6-160-160c0-28.9 7.7-56 21.1-79.4c7.1-12.3 23.7-13.1 33.8-3.1L498.5 425.1c10 10 9.3 26.7-3.1 33.8z" />
                    </svg>';
$icon_tablets = '<svg width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M128 176C128 149.5 149.5 128 176 128C202.5 128 224 149.5 224 176L224 288L128 288L128 176zM240 432C240 383.3 258.1 338.8 288 305L288 176C288 114.1 237.9 64 176 64C114.1 64 64 114.1 64 176L64 464C64 525.9 114.1 576 176 576C213.3 576 246.3 557.8 266.7 529.7C249.7 501.1 240 467.7 240 432zM304.7 499.4C309.3 508.1 321 509.1 328 502.1L502.1 328C509.1 321 508.1 309.3 499.4 304.7C479.3 294 456.4 288 432 288C352.5 288 288 352.5 288 432C288 456.3 294 479.3 304.7 499.4zM361.9 536C354.9 543 355.9 554.7 364.6 559.3C384.7 570 407.6 576 432 576C511.5 576 576 511.5 576 432C576 407.7 570 384.7 559.3 364.6C554.7 355.9 543 354.9 536 361.9L361.9 536z"/></svg>';
$icon_calendar = '<svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L64 64C28.7 64 0 92.7 0 128l0 16 0 48L0 448c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-256 0-48 0-16c0-35.3-28.7-64-64-64l-40 0 0-40c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L152 64l0-40zM48 192l352 0 0 256c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256z" />
                                </svg>';
$icon_file = '<svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M320 464c8.8 0 16-7.2 16-16l0-288-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16l256 0zM0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 448c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64z" />
                                </svg>';
?>
<div class="container pb-5 hld-orders" id="hldOrdersWrap">
    <?php
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $orders = HLD_UserSubscriptions::get_orders($user_id);
        // echo "<pre>";
        // print_r($orders);
        // echo "</pre>";
        // array_push($orders, "order::433fd97b-c6a7-4564-9b2b-aaf7a39d7d78");
        // array_push($orders, "order::a55a22f5-8bdb-4299-87f7-b18eb2a3a405");
        if (!empty($orders) && is_array($orders)) {
            foreach ($orders as $order_id) {
                $order = $hld_telegra->get_order($order_id, "");
                if (!is_wp_error($order) && !$order == null) {
                    $product = $order['productVariations'][0]['productVariation'] ?? [];
                    $keywords = $product['description'] ?? 'Medicine Not found';
                    $orderNumber = $order['orderNumber'] ?? 'N/A';
                    $createdAt = isset($order['createdAt']) ? date('d M Y', strtotime($order['createdAt'])) : 'Unknown';
                    $testName = '';
                    $encoded_order_id = urlencode($order['id']);
                    $order_status = $order["status"];
                    if (!empty($order['prescriptionFulfillments']) && is_array($order['prescriptionFulfillments'])) {
                        $prescriptionFulfillments = $order['prescriptionFulfillments'][0];
                        // ✅ process prescriptionFulfillments here safely
                        $fulfillmentStatus   = $prescriptionFulfillments['status'] ?? '';
                        $pharmacyStatus      = $prescriptionFulfillments['pharmacyFulfillment']['pharmacyStatus'] ?? '';
                        $lastStatusReceived  = $prescriptionFulfillments['pharmacyFulfillment']['lastStatusReceived'] ?? '';
                        $approvalDate        = $prescriptionFulfillments['prescription']['approvalDate'] ?? '';
                        $pharmacyName        = $prescriptionFulfillments['prescription']['pharmacy']['name'] ?? '';
                        $pharmacyFax         = $prescriptionFulfillments['prescription']['pharmacy']['faxNumber'] ?? '';
                        $providerName        = $prescriptionFulfillments['prescription']['provider']['fullName'] ?? '';
                        $providerPicture     = $prescriptionFulfillments['prescription']['provider']['picture'] ?? '';
                        $prescriptionNumber  = $prescriptionFulfillments['prescription']['prescriptionNumber'] ?? '';
                        $productDescription  = $prescriptionFulfillments['prescription']['productVariations'][0]['productVariation']['description'] ?? '';
                        $productStrength     = $prescriptionFulfillments['prescription']['productVariations'][0]['productVariation']['strength'] ?? '';
                        $productForm         = $prescriptionFulfillments['prescription']['productVariations'][0]['productVariation']['form'] ?? '';
                        $productQuantity     = $prescriptionFulfillments['prescription']['productVariations'][0]['quantity'] ?? '';
                        $productInstructions = $prescriptionFulfillments['prescription']['productVariations'][0]['customInstructions'] ?? '';
                        $productName         = $prescriptionFulfillments['prescription']['productVariations'][0]['productVariation']['product']['title'] ?? '';
                        $visitPractitioner   = $prescriptionFulfillments['prescription']['visit']['practitioner']['firstName']
                            . ' ' . $prescriptionFulfillments['prescription']['visit']['practitioner']['lastName'] ?? '';
                        $pdfUrl              = $prescriptionFulfillments['pdfData']['key'] ?? '';
                        $pdfName             = $prescriptionFulfillments['pdfData']['name'] ?? '';
                        $latestEventTitle = '';
                        if (!empty($prescriptionFulfillments['history'])) {
                            $lastEvent = end($prescriptionFulfillments['history']);
                            $latestEventTitle = $lastEvent['eventTitle'] ?? '';
                        }
                        // then render your HTML block...
                    }
    ?>
                    <div class="card mb-4 shadow-sm p-4 hld-order-item">
                        <!-- Top Summary Row -->
                        <div class="row" style="align-items: flex-start;">
                            <div class="col-md-8 d-flex align-items-center gap-3">
                                <div class="icon-wrap bg-light rounded-circle p-3 d-flex align-items-center justify-content-center">
                                    <?php echo $icon_capsule_tablet; ?>
                                </div>
                                <div class="desc-wrap">
                                    <h5 class="fw-bold text-primary mb-1"><?php echo esc_html($keywords); ?></h5>
                                    <div class="d-flex flex-wrap gap-3 small text-muted">
                                        <div class="d-flex align-items-center gap-1 hld-sub-desc">
                                            <?php // echo $icon_tablets 
                                            ?>
                                            <?php echo esc_html($product['form'] ?? 'Oral'); ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 hld-sub-desc">
                                            <?php // echo $icon_calendar 
                                            ?>
                                            <?php echo esc_html($createdAt); ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 hld-sub-desc">
                                            <?php // echo $icon_calendar 
                                            ?>
                                            <strong>Order Status:</strong> <?php echo esc_html(ucfirst($order_status)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex flex-md-row gap-2 justify-content-end">
                                <!-- <a href="<?php echo esc_url(site_url('/telegra-order-detail?order_id=' . $encoded_order_id)); ?>" target="_blank">
                                    <button class="btn btn-outline-primary">View Detail</button>
                                </a> -->
                                <?php if (!empty($order_status)) : ?>
                                    <?php
                                    // Define background colors for each order status
                                    $status_colors = [
                                        'started'   => '#007bff', // Blue
                                        'completed' => '#28a745', // Green
                                        'pending'   => '#ffc107', // Amber/Yellow
                                        'failed'    => '#dc3545', // Red
                                    ];
                                    // Default color if status is unknown
                                    $background_color = isset($status_colors[strtolower($order_status)])
                                        ? $status_colors[strtolower($order_status)]
                                        : '#6c757d'; // Gray as fallback
                                    ?>
                                    <span
                                        id="hldViewOrderDetail"
                                        class="hld_order_status"
                                        data-order-id="<?php echo esc_attr($order_id); ?>"
                                        style="background: <?php echo esc_attr($background_color); ?>;">
                                        View Details
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Order Detail Section -->
                        <?php
                        if (isset($_GET["disable-this-if-temporarily"]) && !empty($order['prescriptionFulfillments']) && is_array($order['prescriptionFulfillments'])) {
                        ?>
                            <hr>
                            <div class="hld_prescription_wrap">
                                <div class="row">
                                    <!-- Prescription Info -->
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold">Prescription</h6>
                                        <p class="mb-1"><strong>Number:</strong> <?php echo esc_html($prescriptionNumber); ?></p>
                                        <p class="mb-1"><strong>Product:</strong> <?php echo esc_html($productDescription); ?> (<?php echo esc_html($productStrength); ?>, <?php echo esc_html($productForm); ?>)</p>
                                        <p class="mb-1"><strong>Quantity:</strong> <?php echo esc_html($productQuantity); ?></p>
                                        <p class="mb-1"><strong>Instructions:</strong> <?php echo esc_html($productInstructions); ?></p>
                                    </div>
                                    <!-- Provider & Pharmacy Info -->
                                    <div class="col-md-6 mb-3">
                                        <h6 class="fw-bold">Provider & Pharmacy</h6>
                                        <p class="mb-1"><strong>Provider:</strong> <?php echo esc_html($providerName); ?></p>
                                        <p class="mb-1"><strong>Pharmacy:</strong> <?php echo esc_html($pharmacyName); ?></p>
                                        <p class="mb-1"><strong>Fax:</strong> <?php echo esc_html($pharmacyFax); ?></p>
                                        <p class="mb-1"><strong>Pharmacy Status:</strong> <?php echo esc_html($pharmacyStatus); ?></p>
                                    </div>
                                </div>
                                <!-- Status & Dates -->
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Status</h6>
                                        <p class="mb-1"><strong>Fulfillment:</strong> <?php echo esc_html($fulfillmentStatus); ?></p>
                                        <p class="mb-1"><strong>Last Update:</strong> <?php echo esc_html($lastStatusReceived); ?></p>
                                        <p class="mb-1"><strong>Latest Event:</strong> <?php echo esc_html($latestEventTitle); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Dates</h6>
                                        <p class="mb-1"><strong>Approved:</strong> <?php echo esc_html($approvalDate); ?></p>
                                        <p class="mb-1"><strong>Ordered:</strong> <?php echo esc_html($createdAt); ?></p>
                                    </div>
                                </div>
                                <!-- PDF Download -->
                                <?php if (!empty($pdfUrl)) : ?>
                                    <div class="mt-3">
                                        <a href="<?php echo esc_url($pdfUrl); ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100 rounded-pill py-2 fs-6">
                                            Download Prescription (<?php echo esc_html($pdfName); ?>)
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php } ?>
                    </div>
        <?php
                } else {
                    echo '<p class="text-danger">❌ Failed to load order: ' . esc_html($order_id) . '</p>';
                }
            }
        } else {
            hld_not_found("You have no orders.");
        }
        ?>
</div>
<div class="container pb-5 hld-order-detail-box hidden" id="hldOrderDetailBox">
</div>
<?php
    }
