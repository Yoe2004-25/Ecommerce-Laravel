<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Commerce API Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .auth-buttons button, .logout-btn {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: transform 0.2s;
        }

        .auth-buttons button:hover, .logout-btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: #48bb78;
            color: white;
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .product-price {
            font-size: 20px;
            color: #667eea;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-stock {
            color: #48bb78;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .product-description {
            color: #718096;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .btn-buy {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-buy:hover {
            background: #5a67d8;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2d3748;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-email {
            margin-right: 15px;
            color: #4a5568;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="nav">
                <div class="logo">🛒 E-Commerce API</div>
                <div id="authSection">
                    <!-- Dynamic auth buttons -->
                </div>
            </div>
        </div>

        <div id="message"></div>

        <div id="productsContainer">
            <h2 style="color: white;">Our Products</h2>
            <div class="products-grid" id="productsGrid"></div>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h2>Login</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="loginEmail" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="loginPassword" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
                <button type="button" onclick="closeModal('loginModal')" class="btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <h2>Register</h2>
            <form id="registerForm">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="registerName" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="registerEmail" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="registerPassword" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="registerPasswordConfirmation" required>
                </div>
                <button type="submit" class="btn-primary">Register</button>
                <button type="button" onclick="closeModal('registerModal')" class="btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Order Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h2>Place Order</h2>
            <div id="orderProductInfo"></div>
            <form id="orderForm">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="orderQuantity" min="1" required>
                </div>
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea id="shippingAddress" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn-primary">Place Order</button>
                <button type="button" onclick="closeModal('orderModal')" class="btn-secondary">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '/api';
        let currentUser = null;
        let selectedProduct = null;

        // Check authentication on load
        window.addEventListener('load', () => {
            checkAuth();
            loadProducts();
        });

        async function checkAuth() {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const response = await fetch(`${API_URL}/me`, {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        currentUser = await response.json();
                        updateAuthUI(true);
                    } else {
                        logout();
                    }
                } catch (error) {
                    logout();
                }
            } else {
                updateAuthUI(false);
            }
        }

        function updateAuthUI(isLoggedIn) {
            const authSection = document.getElementById('authSection');
            if (isLoggedIn && currentUser) {
                authSection.innerHTML = `
                    <div class="user-info">
                        <span class="user-email">👤 ${currentUser.email}</span>
                        <button onclick="logout()" class="btn-danger logout-btn">Logout</button>
                    </div>
                `;
            } else {
                authSection.innerHTML = `
                    <div class="auth-buttons">
                        <button onclick="showModal('loginModal')" class="btn-primary">Login</button>
                        <button onclick="showModal('registerModal')" class="btn-secondary">Register</button>
                    </div>
                `;
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch(`${API_URL}/products`);
                const data = await response.json();
                displayProducts(data.data);
            } catch (error) {
                showMessage('Error loading products', 'error');
            }
        }

        function displayProducts(products) {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = products.map(product => `
                <div class="product-card">
                    <div class="product-title">${product.name}</div>
                    <div class="product-price">${product.price_formatted}</div>
                    <div class="product-stock">📦 In Stock: ${product.stock_quantity}</div>
                    <div class="product-description">${product.description.substring(0, 100)}...</div>
                    ${currentUser ? `<button onclick="showOrderModal(${product.id}, '${product.name}', ${product.price})" class="btn-buy">Buy Now</button>` : '<button disabled class="btn-buy" style="background: #cbd5e0;">Login to Buy</button>'}
                </div>
            `).join('');
        }

        function showOrderModal(productId, productName, price) {
            selectedProduct = { id: productId, name: productName, price };
            document.getElementById('orderProductInfo').innerHTML = `
                <p><strong>Product:</strong> ${productName}</p>
                <p><strong>Price:</strong> $${price}</p>
            `;
            showModal('orderModal');
        }

        // Auth handlers
        document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            try {
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();
                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    currentUser = data.user;
                    updateAuthUI(true);
                    closeModal('loginModal');
                    showMessage('Login successful!', 'success');
                    loadProducts();
                } else {
                    showMessage(data.message || 'Login failed', 'error');
                }
            } catch (error) {
                showMessage('Error during login', 'error');
            }
        });

        document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const password_confirmation = document.getElementById('registerPasswordConfirmation').value;

            try {
                const response = await fetch(`${API_URL}/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password, password_confirmation })
                });

                const data = await response.json();
                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    currentUser = data.user;
                    updateAuthUI(true);
                    closeModal('registerModal');
                    showMessage('Registration successful!', 'success');
                    loadProducts();
                } else {
                    showMessage(data.message || 'Registration failed', 'error');
                }
            } catch (error) {
                showMessage('Error during registration', 'error');
            }
        });

        document.getElementById('orderForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const quantity = document.getElementById('orderQuantity').value;
            const shipping_address = document.getElementById('shippingAddress').value;

            const orderData = {
                items: [{
                    product_id: selectedProduct.id,
                    quantity: parseInt(quantity)
                }],
                shipping_address: shipping_address
            };

            try {
                const response = await fetch(`${API_URL}/orders`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(orderData)
                });

                if (response.ok) {
                    closeModal('orderModal');
                    showMessage('Order placed successfully!', 'success');
                    loadProducts();
                } else {
                    const error = await response.json();
                    showMessage(error.message || 'Order failed', 'error');
                }
            } catch (error) {
                showMessage('Error placing order', 'error');
            }
        });

        function logout() {
            localStorage.removeItem('token');
            currentUser = null;
            updateAuthUI(false);
            showMessage('Logged out successfully', 'success');
            loadProducts();
        }

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            if (modalId === 'orderModal') {
                document.getElementById('orderForm').reset();
            }
        }

        function showMessage(msg, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 3000);
        }
    </script>
</body>
</html>