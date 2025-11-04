<?php
require 'session.php';
check_role(['admin', 'superadmin']);
require 'db.php';

// Fetch all products to display
$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Blend S</title>
</head>
<body style="overflow-x: hidden;">

    <div class="logo-container text-center mt-4">
        <img src="https://images.seeklogo.com/logo-png/46/1/blend-s-logo-png_seeklogo-463176.png" alt="Blend S Logo" class="blend-s-logo">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- MENU SECTION -->
    <section class="row d-flex text-center container-fluid">
    <div class="col-lg-8">
        <h1 class="text-choco fw-bolder" style="margin-top: 80px;">Menu</h1>
        <!-- ROW 1 -->
        <div class="row mt-5">
          <?php foreach ($products as $index => $product): ?>
            <?php if ($index < 3): ?>
              <div class="col-sm-6 col-lg-4">
                <div class="box">
                  <div>
                    <div class="img-box">
                      <img style="width: 20vw;" src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="detail-box p-3">
                      <h5 class="text-choco fw-bold">
                        <?= htmlspecialchars($product['name']) ?>
                      </h5>
                      <div class="options">
                      <h6>
                        ₱<?= number_format($product['price'], 2) ?>
                      </h6>
                      <input type="number" class="form-control w-50 my-2" style="margin-left: 4.5vw;" min="1" id="qty-<?= $product['id'] ?>">
                      <button class="btn btn-highlight text-main" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">ADD TO CART</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <!-- ROW 2 -->
        <div class="row mt-5">
          <?php foreach ($products as $index => $product): ?>
            <?php if ($index >= 3 && $index < 6): ?>
              <div class="col-sm-6 col-lg-4">
                <div class="box">
                  <div>
                    <div class="img-box">
                      <img style="width: 20vw; height: 230px;" src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="detail-box p-3">
                      <h5 class="text-choco fw-bold">
                        <?= htmlspecialchars($product['name']) ?>
                      </h5>
                      <div class="options">
                        <h6>
                          ₱<?= number_format($product['price'], 2) ?>
                        </h6>
                        <input type="number" class="form-control w-50 my-2" style="margin-left: 4.5vw;" min="1" id="qty-<?= $product['id'] ?>">
                        <button class="btn btn-highlight text-main" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">ADD TO CART</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <!-- ROW 3 -->
        <div class="row mt-5">
          <?php foreach ($products as $index => $product): ?>
            <?php if ($index >= 6): ?>
              <div class="col-sm-6 col-lg-4">
                <div class="box">
                  <div>
                    <div class="img-box">
                      <img style="width: 20vw; height: 230px;" src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <div class="detail-box p-3">
                      <h5 class="text-choco fw-bold">
                        <?= htmlspecialchars($product['name']) ?>
                      </h5>
                      <div class="options">
                        <h6>
                          ₱<?= number_format($product['price'], 2) ?>
                        </h6>
                        <input type="number" class="form-control w-50 my-2" style="margin-left: 4.5vw;" min="1" id="qty-<?= $product['id'] ?>">
                        <button class="btn btn-highlight text-main" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>">ADD TO CART</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="row">
            <h1 class="text-choco fw-bolder fs-3" style="margin-top: 80px;">RECEIPT</h1>
            <div id="cart-items"></div>
        <h1 class="text-choco fw-bold fs-4" style="margin-top: 50px;">TOTAL: <span id="cart-total">₱0</span></h1>
        <input type="number" class="form-control w-50 my-2" style="margin-left: 4.5vw;" min="1" placeholder="Pay Here" id="payment-amount">
        <button class="btn btn-highlight text-main mt-4" style="width: 15vw; margin-left: 4.5vw;" id="pay-btn">PAY</button>
        <p id="order-status" class="text-center mt-2"></p>
        </div>
    </div>
    </section>

  <!-- FOOTER -->
  <footer class="bg-choco text-center text-white py-3" style="margin-top: 100px;">
    <p class="mb-0">© 2025 Blend S. All rights reserved.</p>
  </footer>


  <script>
    $(document).ready(function() {

      // Initializing cart array
      const cart = [];

      // Initializing disabled buttons first before buying
      $(".btn[data-name]").prop('disabled', true);
      $("#pay-btn").prop('disabled', true);

      // If any input with a type of number (item quantity input) has 0 quantity, disable the ADD TO CART buttons. If greater, enable ADD TO CART buttons.
      $("input[type='number']").on("input", function() {
        const quantity = parseInt($(this).val());
        const buyBtn = $(this).siblings("input[type='number']").siblings(".btn[data-name]");
        buyBtn.prop('disabled', isNaN(quantity) || quantity <= 0);
      });

      function updateCart() {
        $("#cart-items").empty();
        let total = 0;

        cart.forEach(item => {
          const itemTotal = item.price * item.quantity;
          total += itemTotal;

          const cartItem = $(`
                <div class="d-flex justify-content-between align-items-center my-2">
                    <p class="fw-bold">${item.name}</p>
                    <p>${item.quantity}</p>
                </div>
          `);

          $("#cart-items").append(cartItem);
        });

        $("#cart-total").text(`₱${total.toFixed(2)}`);

        // Enable or disable PAY button
        $("#pay-btn").prop('disabled', cart.length === 0);
      }

      // Handles the on click function of the ADD TO CART button.
      $(".btn[data-name]").on("click", function() {
        const id = $(this).data("id");
        const name = $(this).data("name");
        const price = parseFloat($(this).data("price"));
        const quantityInput = $(this).siblings("input[type='number']");
        const quantity = parseInt(quantityInput.val());

        const existingItem = cart.find(item => item.id === id);

        if (isNaN(quantity) || quantity <= 0) {
            quantityInput.addClass('border-danger');
            setTimeout(() => quantityInput.removeClass('border-danger'), 1000);
            return;
        }

        if(existingItem) {
          existingItem.quantity += quantity;
        } else {
          cart.push({id, name, price, quantity});
        }

        quantityInput.val('');
        updateCart();
      });

          // Handle PAY button click
    $("#pay-btn").on("click", function() {
        const paymentAmount = parseFloat($('#payment-amount').val());
        const totalAmount = parseFloat($("#cart-total").text().replace(/[^\d.]/g, ""));

        if (isNaN(totalAmount) || totalAmount <= 0) {
            alert("Error: The total amount is invalid.");
            return;
        }

        if (isNaN(paymentAmount) || paymentAmount <= 0) {
            alert("Please enter a valid payment amount.");
            return;
        }

        if (paymentAmount >= totalAmount) {
            const change = paymentAmount - totalAmount;
            $("#order-status").text(`Payment Successful! Here is your ₱${change.toFixed(2)} change.`);
            $("#pay-btn").prop('disabled', true);

            // Submit order to server
            $.ajax({
                url: 'api/submit_order.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(cart),
                success: function(response) {
                    if (response.success) {
                        cart.length = 0;
                        $("#cart-items").empty();
                        $("#cart-total").text("₱0.00");
                        $('#payment-amount').val('');
                        $("#order-status").text('Order recorded successfully!');
                        setTimeout(() => $("#order-status").text(''), 3000);
                    } else {
                        $("#order-status").text('Error: ' + response.message);
                    }
                },
                error: function() {
                    $("#order-status").text('Error: Could not connect to server.');
                }
            });
        } else {
            $("#order-status").text(`Insufficient payment. Please add ₱${(totalAmount - paymentAmount).toFixed(2)} more.`);
        }
    });

    });
  </script>


</body>
</html>
