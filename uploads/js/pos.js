let currentOrder = []; // Array of { id, name, price, quantity }

function addProductToOrder(id, name, price) {
    console.log("addProductToOrder called for:", { id, name, price });
    const qtyInput = document.getElementById(`qty-${id}`);
    const quantity = parseInt(qtyInput ? qtyInput.value : 1, 10);

    if (isNaN(quantity) || quantity < 1) {
        alert("Please enter a valid quantity.");
        return;
    }
    console.log("Calling addToOrder with quantity:", quantity);
    addToOrder(id, name, price, quantity);
}

function addToOrder(id, name, price, quantityToAdd) {
    console.log("addToOrder called:", { id, name, price, quantityToAdd });
    const existingItem = currentOrder.find(item => item.id === id);

    if (existingItem) {
        console.log("Item exists, increasing quantity.");
        existingItem.quantity += quantityToAdd;
    } else {
        console.log("Item is new, adding to order with quantity:", quantityToAdd);
        currentOrder.push({ id: id, name: name, price: price, quantity: quantityToAdd });
    }
    console.log("Current order after update:", currentOrder);
    renderOrder();
}

function decreaseQuantity(id) {
    const itemIndex = currentOrder.findIndex(item => item.id === id);

    if (itemIndex > -1) {
        currentOrder[itemIndex].quantity--;
        if (currentOrder[itemIndex].quantity <= 0) {
            currentOrder.splice(itemIndex, 1);
        }
    }

    renderOrder();
}

function increaseQuantity(id) {
    const item = currentOrder.find(item => item.id === id);
    if (item) {
        item.quantity++;
    }
    renderOrder();
}

function removeItemFromOrder(id) {
    const itemIndex = currentOrder.findIndex(item => item.id === id);
    if (itemIndex > -1) {
        currentOrder.splice(itemIndex, 1);
    }
    renderOrder();
}

function renderOrder() {
    console.log("renderOrder called. Current order:", currentOrder);
    const orderBody = document.getElementById('order-items-body');
    const orderTotalEl = document.getElementById('order-total');
    let total = 0;

    if (!orderBody || !orderTotalEl) {
        console.warn("Missing order DOM elements (#order-items-body or #order-total).");
        return;
    }

    orderBody.innerHTML = ''; // Clear the table

    currentOrder.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td class="text-center d-flex align-items-center justify-content-center">
                <button class="btn btn-secondary btn-sm me-1" onclick="decreaseQuantity(${item.id})">-</button>
                <span class="mx-2">${item.quantity}</span>
                <button class="btn btn-secondary btn-sm ms-1" onclick="increaseQuantity(${item.id})">+</button>
            </td>
            <td class="text-end">${itemTotal.toFixed(2)}</td>
            <td class="text-center">
                <button class="btn btn-outline-danger btn-sm" onclick="removeItemFromOrder(${item.id})">X</button>
            </td>
        `;
        orderBody.appendChild(row);
    });

    orderTotalEl.textContent = total.toFixed(2);
    console.log("Order rendered successfully. Total:", total.toFixed(2));
}

function updateTableHeaders() {
    const headerRow = document.querySelector('#order-items-table thead tr');
    if (headerRow) {
        headerRow.innerHTML = `
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Total</th>
            <th></th>
        `;
    }
}

// Init and attach handlers safely
document.addEventListener('DOMContentLoaded', () => {
    updateTableHeaders();

    const payBtn = document.getElementById('pay-btn');
    if (payBtn) {
        payBtn.addEventListener('click', async() => {
            if (currentOrder.length === 0) {
                alert("Cannot process payment for an empty order.");
                return;
            }

            const paymentAmountEl = document.getElementById('payment-amount');
            const paymentAmount = parseFloat(paymentAmountEl ? paymentAmountEl.value : NaN);
            const totalAmount = parseFloat(document.getElementById('order-total').textContent);

            if (isNaN(paymentAmount) || paymentAmount < totalAmount) {
                alert("Payment amount is insufficient or invalid.");
                return;
            }

            document.getElementById('order-status').textContent = 'Processing payment...';
            payBtn.disabled = true;

            try {
                const response = await fetch('api/submit_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(currentOrder)
                });

                const result = await response.json();

                if (result.success) {
                    const change = (paymentAmount - totalAmount).toFixed(2);
                    alert(`Thank you for your purchase!\n\nHere’s your ${change} pesos change.`);

                    currentOrder = [];
                    if (paymentAmountEl) paymentAmountEl.value = '';
                    document.getElementById('order-status').textContent = '';
                    renderOrder();
                } else {
                    document.getElementById('order-status').textContent = 'Error: ' + result.message;
                }
            } catch (error) {
                console.error('Submit Error:', error);
                document.getElementById('order-status').textContent = 'Error: Could not connect to server.';
            } finally {
                payBtn.disabled = false;
            }
        });
    } else {
        console.warn("#pay-btn not found");
    }
});