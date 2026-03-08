/*
 * radji e-shopping website - Main JavaScript File
 */

// Base URL for API calls
const BASE_URL = window.location.pathname.includes('/radji/public/') ? '/radji/public/' : '/public/';

// Cart object for managing shopping cart
const cart = {
    items: [],
    
    init: function() {
        this.loadFromStorage();
    },
    
    addItem: function(product) {
        const existingItem = this.items.find(item => item.product_id === product.product_id);
        
        if (existingItem) {
            existingItem.quantity += product.quantity;
        } else {
            this.items.push(product);
        }
        
        this.saveToStorage();
        this.showNotification('Product added to cart!');
    },
    
    removeItem: function(productId) {
        this.items = this.items.filter(item => item.product_id !== productId);
        this.saveToStorage();
    },
    
    updateQuantity: function(productId, quantity) {
        const item = this.items.find(item => item.product_id === productId);
        if (item) {
            item.quantity = quantity;
            this.saveToStorage();
        }
    },
    
    getTotal: function() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    
    loadFromStorage: function() {
        const stored = localStorage.getItem('cart');
        if (stored) {
            this.items = JSON.parse(stored);
        }
    },
    
    saveToStorage: function() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    },
    
    clear: function() {
        this.items = [];
        this.saveToStorage();
    }
};

// Notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#2ecc71' : '#e74c3c'};
        color: white;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease-in-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Add CSS animation for notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    cart.init();
    
    // Add to cart button functionality
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const quantityInput = document.getElementById('quantity');
            const quantity = parseInt(quantityInput.value) || 1;
            
            const product = {
                product_id: getProductIdFromURL(),
                name: document.getElementById('productName').textContent,
                price: parseFloat(document.getElementById('productPrice').textContent),
                quantity: quantity,
                image: document.getElementById('mainImage').src
            };
            
            cart.addItem(product);
        });
    }
    
    // Login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Form will be submitted to process_login.php
            this.submit();
        });
    }
    
    // Register form submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }
            
            // Form will be submitted to process_register.php
            this.submit();
        });
    }
    
    // Review, product, and contact forms use server-side processing.
});

// Get product ID from URL
function getProductIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

// Load product reviews
function loadProductReviews(productId) {
    // This would fetch and display reviews via AJAX
    console.log('Loading reviews for product:', productId);
}

// Search products
function searchProducts(query) {
    const params = new URLSearchParams({
        search: query
    });
    window.location.href = BASE_URL + 'products.php?' + params.toString();
}

// Filter products by category
function filterByCategory(categoryId) {
    const params = new URLSearchParams({
        category: categoryId
    });
    window.location.href = BASE_URL + 'products.php?' + params.toString();
}

// Filter products by price
function filterByPrice(priceRange) {
    const params = new URLSearchParams({
        price: priceRange
    });
    window.location.href = BASE_URL + 'products.php?' + params.toString();
}

// Handle product image click for detail view
document.addEventListener('click', function(e) {
    if (e.target.closest('.product-card')) {
        const productId = e.target.closest('.product-card').dataset.productId;
        if (productId) {
            window.location.href = BASE_URL + 'product-detail.php?id=' + productId;
        }
    }
    
    // Thumbnail image click
    if (e.target.closest('.thumbnail-images img')) {
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail-images img');
        
        thumbnails.forEach(img => img.classList.remove('active'));
        e.target.classList.add('active');
        mainImage.src = e.target.src;
    }
});
