/**
 * TechShop - Main JavaScript
 * Cart, Search AJAX, Wishlist, Toast, etc.
 */

const getAppUrl = () => {
    let url = document.querySelector('meta[name="app-url"]')?.content || window.location.origin;
    if (window.location.protocol === 'https:' && url.startsWith('http://')) {
        url = url.replace('http://', 'https://');
    }
    return url.replace(/\/+$/, '');
};
const APP_URL = getAppUrl();

/* ===== Toast Notifications ===== */
const Toast = {
    container: null,
    
    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },
    
    show(message, type = 'success', duration = 3500) {
        this.init();
        
        const icons = { success: 'fas fa-check-circle', error: 'fas fa-exclamation-circle', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
        const colors = { success: '#2E7D32', error: '#E53935', warning: '#FF6F00', info: '#1565C0' };
        
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <i class="${icons[type] || icons.info}" style="color:${colors[type]}; font-size:18px; flex-shrink:0"></i>
            <span style="font-size:13px; font-weight:500; flex:1">${message}</span>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#999;padding:0;margin-left:4px"><i class="fas fa-times"></i></button>
        `;
        
        this.container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    
    success: (msg) => Toast.show(msg, 'success'),
    error: (msg) => Toast.show(msg, 'error'),
    warning: (msg) => Toast.show(msg, 'warning'),
    info: (msg) => Toast.show(msg, 'info'),
};

/* ===== Cart Functions ===== */
const Cart = {
    count: parseInt(document.querySelector('.cart-count')?.textContent || '0'),
    
    updateCount(count) {
        this.count = count;
        document.querySelectorAll('.cart-count').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    },
    
    async add(productId, quantity = 1, btn = null, variantId = null) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang thêm...';
        }
        
        try {
            const res = await fetch(`${APP_URL}/cart/add`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    product_id: productId,
                    quantity: quantity,
                    variant_id: variantId || '',
                    csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || ''
                })
            });
            
            const data = await res.json();
            
            if (data.success) {
                Toast.success(data.message);
                this.updateCount(data.cartCount);
                this.animateCartIcon();
            } else {
                Toast.error(data.message);
            }
        } catch (e) {
            Toast.error('Lỗi kết nối. Vui lòng thử lại.');
        }
        
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ';
        }
    },
    
    animateCartIcon() {
        const icon = document.querySelector('.cart-icon');
        if (icon) {
            icon.classList.add('shake');
            setTimeout(() => icon.classList.remove('shake'), 600);
        }
    },
    
    async updateQuantity(itemId, quantity) {
        try {
            const res = await fetch(`${APP_URL}/cart/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ item_id: itemId, quantity, csrf_token: getCsrf() })
            });
            const data = await res.json();
            if (data.success) {
                document.querySelector('#subtotal')?.textContent !== undefined && (document.querySelector('#subtotal').textContent = data.subtotal);
                document.querySelector('#shipping-fee')?.textContent !== undefined && (document.querySelector('#shipping-fee').textContent = data.shippingFee);
                
                // Cập nhật chiết khấu động
                const discountRow = document.querySelector('#discount-row');
                const discountAmount = document.querySelector('#discount-amount');
                const appliedCoupon = document.querySelector('#applied-coupon');
                if (discountRow) {
                    if (data.discountRaw > 0) {
                        discountRow.classList.remove('d-none');
                        if (discountAmount) discountAmount.textContent = data.discount;
                        if (appliedCoupon && data.couponCode) appliedCoupon.textContent = data.couponCode;
                    } else {
                        discountRow.classList.add('d-none');
                        if (appliedCoupon && appliedCoupon.textContent !== '') {
                            location.reload();
                            return;
                        }
                    }
                }
                
                document.querySelector('#total-amount')?.textContent !== undefined && (document.querySelector('#total-amount').textContent = data.total);
                
                // Update item row subtotal
                const itemTotalEl = document.querySelector(`.item-total[data-item="${itemId}"]`);
                if (itemTotalEl && data.itemTotal) {
                    itemTotalEl.textContent = '= ' + data.itemTotal;
                }
                
                this.updateCount(data.cartCount);
            }
        } catch(e) {}
    },
    
    async remove(itemId) {
        try {
            await fetch(`${APP_URL}/cart/remove`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ item_id: itemId, csrf_token: getCsrf() })
            });
            window.location.reload();
        } catch(e) {
            window.location.reload();
        }
    }
};

/* ===== Wishlist ===== */
const Wishlist = {
    async toggle(productId, btn) {
        try {
            const res = await fetch(`${APP_URL}/wishlist/toggle`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ product_id: productId, csrf_token: getCsrf() })
            });
            const data = await res.json();
            if (data.success) {
                const icon = btn.querySelector('i');
                const actionsContainer = btn.closest('.product-card-actions');
                if (data.added) { 
                    if (icon) icon.className = 'fas fa-heart'; 
                    btn.classList.add('active'); 
                    btn.title = 'Bỏ yêu thích';
                    if (actionsContainer) actionsContainer.classList.add('has-active');
                } else { 
                    if (icon) icon.className = 'far fa-heart'; 
                    btn.classList.remove('active'); 
                    btn.title = 'Yêu thích';
                    if (actionsContainer) actionsContainer.classList.remove('has-active');
                }
                Toast.show(data.message, data.added ? 'success' : 'info');
            } else if (res.status === 302 || !data.success) {
                window.location.href = `${APP_URL}/auth/login`;
            }
        } catch(e) {}
    }
};

/* ===== AJAX Live Search (Desktop & Mobile) ===== */
const Search = {
    instances: [],
    
    init() {
        const searchConfigs = [
            { input: document.querySelector('#search-input'), suggestions: document.querySelector('#search-suggestions') },
            { input: document.querySelector('#search-input-mobile'), suggestions: document.querySelector('#search-suggestions-mobile') }
        ];

        searchConfigs.forEach(cfg => {
            if (!cfg.input || !cfg.suggestions) return;

            let timeout = null;
            const input = cfg.input;
            const suggestions = cfg.suggestions;

            input.addEventListener('input', () => {
                const q = input.value.trim();
                clearTimeout(timeout);
                
                if (q.length < 2) {
                    suggestions.classList.remove('show');
                    suggestions.innerHTML = '';
                    return;
                }

                suggestions.innerHTML = '<div class="p-3 text-muted text-center" style="font-size:13px"><i class="fas fa-spinner fa-spin me-2 text-danger"></i>Đang tìm kiếm sản phẩm...</div>';
                suggestions.classList.add('show');

                timeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`${APP_URL}/search?q=${encodeURIComponent(q)}&ajax=1`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        Search.render(suggestions, data.products || [], q);
                    } catch(e) {
                        suggestions.classList.remove('show');
                    }
                }, 250);
            });

            input.addEventListener('focus', () => {
                const q = input.value.trim();
                if (q.length >= 2 && suggestions.children.length > 0) {
                    suggestions.classList.add('show');
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    suggestions.classList.remove('show');
                    const val = input.value.trim();
                    if (val) {
                        window.location.href = `${APP_URL}/search?q=${encodeURIComponent(val)}`;
                    }
                }
                if (e.key === 'Escape') {
                    suggestions.classList.remove('show');
                }
            });
        });

        document.querySelectorAll('.mobile-search-form, .desktop-search-wrapper').forEach(form => {
            form.addEventListener('submit', (e) => {
                const input = form.querySelector('input[name="q"]');
                if (input && !input.value.trim()) {
                    e.preventDefault();
                }
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-wrapper') && !e.target.closest('.mobile-search-form') && !e.target.closest('.mobile-search-bar-row')) {
                document.querySelectorAll('.search-suggestions').forEach(el => el.classList.remove('show'));
            }
        });
    },

    render(suggestions, products, q) {
        if (!products.length) {
            suggestions.innerHTML = `<div class="p-3 text-muted text-center" style="font-size:13px"><i class="fas fa-search me-2"></i>Không tìm thấy sản phẩm nào khớp với "<strong>${q}</strong>"</div>`;
        } else {
            const listHtml = products.map(p => `
                <a href="${p.url}" class="suggestion-item text-decoration-none">
                    <img src="${p.image}" alt="${p.name}" onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
                    <div style="flex:1; min-width:0;">
                        <div class="suggestion-item-name text-truncate text-dark fw-bold mb-1">${p.name}</div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="suggestion-item-price text-danger fw-bold">${p.price}</span>
                            ${p.brand ? `<span class="badge bg-light text-secondary border" style="font-size:10px;">${p.brand}</span>` : ''}
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size:11px;"></i>
                </a>
            `).join('');

            const viewAll = `
                <a href="${APP_URL}/search?q=${encodeURIComponent(q)}" class="suggestion-item text-decoration-none justify-content-center text-danger fw-bold bg-light py-2" style="font-size:12.5px;">
                    <i class="fas fa-search me-2"></i>Xem tất cả kết quả cho "${q}"
                </a>
            `;
            suggestions.innerHTML = listHtml + viewAll;
        }
        suggestions.classList.add('show');
    }
};

/* ===== Gallery (Product Detail) ===== */
const Gallery = {
    init() {
        const mainImg = document.querySelector('#gallery-main');
        const thumbs = document.querySelectorAll('.gallery-thumb');
        
        if (!mainImg) return;
        
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                mainImg.src = thumb.dataset.full || thumb.querySelector('img').src;
                thumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });
    }
};

/* ===== Quantity Selector ===== */
const Quantity = {
    init() {
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.closest('.product-quantity-selector')?.querySelector('.qty-input');
                if (!input) return;
                
                let val = parseInt(input.value) || 1;
                const max = parseInt(input.max) || 999;
                const min = parseInt(input.min) || 1;
                
                if (btn.dataset.action === 'increase') val = Math.min(val + 1, max);
                else val = Math.max(val - 1, min);
                
                input.value = val;
            });
        });
    }
};

/* ===== Coupon Apply ===== */
const Coupon = {
    async apply() {
        const code = document.querySelector('#coupon-code')?.value?.trim();
        const amount = parseFloat(document.querySelector('#subtotal-raw')?.value || 0);
        
        if (!code) { Toast.warning('Vui lòng nhập mã giảm giá'); return; }
        
        const btn = document.querySelector('#apply-coupon-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
        
        try {
            const res = await fetch(`${APP_URL}/checkout/apply-coupon`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ coupon_code: code, order_amount: amount, csrf_token: getCsrf() })
            });
            const data = await res.json();
            
            if (data.success) {
                Toast.success(data.message);
                document.querySelector('#discount-row')?.classList.remove('d-none');
                document.querySelector('#discount-amount') && (document.querySelector('#discount-amount').textContent = data.discount);
                document.querySelector('#applied-coupon') && (document.querySelector('#applied-coupon').textContent = data.couponCode);
                // Recalculate total
                const subtotal = parseFloat(document.querySelector('#subtotal-raw')?.value || 0);
                const shipping = parseFloat(document.querySelector('#shipping-raw')?.value || 0);
                const discount = parseFloat(data.discountRaw || 0);
                const total = subtotal + shipping - discount;
                document.querySelector('#total-amount') && (document.querySelector('#total-amount').textContent = formatPrice(total));
                setTimeout(() => location.reload(), 1000);
            } else {
                Toast.error(data.message);
            }
        } catch(e) { Toast.error('Lỗi kết nối'); }
        
        if (btn) { btn.disabled = false; btn.innerHTML = 'Áp dụng'; }
    },
    
    async remove() {
        const btn = document.querySelector('#remove-coupon-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
        
        try {
            const res = await fetch(`${APP_URL}/checkout/remove-coupon`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ csrf_token: getCsrf() })
            });
            const data = await res.json();
            
            if (data.success) {
                Toast.success(data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                Toast.error(data.message || 'Lỗi khi gỡ mã giảm giá');
            }
        } catch(e) { Toast.error('Lỗi kết nối'); }
        
        if (btn) { btn.disabled = false; btn.innerHTML = 'Hủy'; }
    }
};

/* ===== Back to Top ===== */
const BackToTop = {
    btn: null,
    init() {
        this.btn = document.querySelector('.back-to-top');
        if (!this.btn) return;
        
        window.addEventListener('scroll', () => {
            this.btn.classList.toggle('show', window.scrollY > 400);
        });
        
        this.btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
};

/* ===== Countdown Timer (Hot Deal) ===== */
const Timer = {
    init() {
        const timerEl = document.querySelector('#countdown-timer');
        if (!timerEl) return;
        
        const endTime = new Date(timerEl.dataset.end || '').getTime();
        
        const update = () => {
            const now = Date.now();
            const diff = Math.max(0, endTime - now);
            
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            
            const fmt = n => String(n).padStart(2, '0');
            
            const hEl = timerEl.querySelector('.timer-h');
            const mEl = timerEl.querySelector('.timer-m');
            const sEl = timerEl.querySelector('.timer-s');
            
            if (hEl) hEl.textContent = fmt(h);
            if (mEl) mEl.textContent = fmt(m);
            if (sEl) sEl.textContent = fmt(s);
            
            if (diff === 0) clearInterval(interval);
        };
        
        const interval = setInterval(update, 1000);
        update();
    }
};

/* ===== Cart Page Actions ===== */
function initCartPage() {
    // Update qty
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        let timeout;
        input.addEventListener('change', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                Cart.updateQuantity(input.dataset.item, input.value);
            }, 500);
        });
    });
    
    // Remove item
    document.querySelectorAll('.cart-remove-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Xóa sản phẩm này khỏi giỏ hàng?')) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                Cart.remove(btn.dataset.item);
            }
        });
    });
}

/* ===== Payment Method Selection ===== */
function initPaymentMethods() {
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            
            // Show/hide bank info
            const method = card.dataset.method;
            document.querySelectorAll('.payment-info').forEach(el => el.classList.add('d-none'));
            const info = document.querySelector(`#payment-info-${method}`);
            if (info) info.classList.remove('d-none');
        });
    });
}

/* ===== Utilities ===== */
function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || 
           document.querySelector('input[name="csrf_token"]')?.value || '';
}

function formatPrice(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
}

function animateNumber(el, target, duration = 800) {
    const start = 0;
    const startTime = performance.now();
    
    const update = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(start + eased * (target - start)).toLocaleString();
        if (progress < 1) requestAnimationFrame(update);
    };
    
    requestAnimationFrame(update);
}

/* ===== Product Review Form ===== */
function initReviewForm() {
    const stars = document.querySelectorAll('.review-star');
    const ratingInput = document.querySelector('#review-rating');
    
    if (!stars.length) return;
    
    stars.forEach(star => {
        star.addEventListener('mouseover', () => {
            const rating = star.dataset.rating;
            stars.forEach(s => {
                s.classList.toggle('active', s.dataset.rating <= rating);
            });
        });
        
        star.addEventListener('click', () => {
            const rating = star.dataset.rating;
            if (ratingInput) ratingInput.value = rating;
        });
        
        star.addEventListener('mouseleave', () => {
            const selected = ratingInput?.value || 0;
            stars.forEach(s => s.classList.toggle('active', s.dataset.rating <= selected));
        });
    });
    
    const form = document.querySelector('#review-form');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            });
            const data = await res.json();
            
            if (data.success) {
                Toast.success(data.message);
                form.reset();
                stars.forEach(s => s.classList.remove('active'));
            } else {
                Toast.error(data.message || 'Gửi đánh giá thất bại');
            }
        } catch(e) { Toast.error('Lỗi kết nối'); }
        
        btn.disabled = false;
        btn.innerHTML = 'Gửi đánh giá';
    });
}

/* ===== Initialize ===== */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[method="get"]').forEach(form => {
        form.querySelectorAll('input[type="radio"][name="category"], input[type="radio"][name="brand"]').forEach(input => {
            input.addEventListener('change', () => form.submit());
        });
    });

    Search.init();
    Gallery.init();
    Quantity.init();
    BackToTop.init();
    Timer.init();
    initCartPage();
    initPaymentMethods();
    initReviewForm();
    
    // Add to cart buttons
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.dataset.product;
            const qty = parseInt(document.querySelector('.qty-input')?.value || 1);
            const variantId = typeof window.getSelectedVariantInfo === 'function' ? window.getSelectedVariantInfo().variantId : null;
            Cart.add(productId, qty, btn, variantId);
        });
    });
    
    // Buy now buttons
    document.querySelectorAll('.btn-buy-now').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const productId = btn.dataset.product;
            const qty = parseInt(document.querySelector('.qty-input')?.value || 1);
            const variantId = typeof window.getSelectedVariantInfo === 'function' ? window.getSelectedVariantInfo().variantId : null;
            
            try {
                const res = await fetch(`${APP_URL}/checkout/buy-now`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        product_id: productId,
                        quantity: qty,
                        variant_id: variantId || '',
                        csrf_token: getCsrf()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = `${APP_URL}/checkout`;
                } else {
                    Toast.error(data.message || 'Lỗi xử lý mua ngay');
                }
            } catch(err) {
                Toast.error('Lỗi kết nối. Vui lòng thử lại.');
            }
        });
    });
    
    // Wishlist buttons
    document.querySelectorAll('.btn-wishlist').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            Wishlist.toggle(btn.dataset.product, btn);
        });
    });
    
    // Apply coupon
    document.querySelector('#apply-coupon-btn')?.addEventListener('click', () => Coupon.apply());
    document.querySelector('#remove-coupon-btn')?.addEventListener('click', () => Coupon.remove());
    document.querySelector('#coupon-code')?.addEventListener('keypress', (e) => { if (e.key === 'Enter') Coupon.apply(); });
    
    // Newsletter signup
    const newsletterBtn = document.querySelector('#newsletter-btn');
    const newsletterEmail = document.querySelector('#newsletter-email');
    if (newsletterBtn && newsletterEmail) {
        const subscribe = async () => {
            const email = newsletterEmail.value.trim();
            if (!email) {
                Toast.warning('Vui lòng nhập địa chỉ email');
                return;
            }
            
            newsletterBtn.disabled = true;
            const originalText = newsletterBtn.textContent;
            newsletterBtn.textContent = 'Đang gửi...';
            
            try {
                const res = await fetch(`${APP_URL}/home/subscribe`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        email: email,
                        csrf_token: getCsrf()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Toast.success(data.message);
                    newsletterEmail.value = '';
                } else {
                    Toast.error(data.message || 'Đăng ký thất bại');
                }
            } catch (err) {
                Toast.error('Lỗi kết nối. Vui lòng thử lại.');
            } finally {
                newsletterBtn.disabled = false;
                newsletterBtn.textContent = originalText;
            }
        };
        newsletterBtn.addEventListener('click', subscribe);
        newsletterEmail.addEventListener('keypress', (e) => { if (e.key === 'Enter') subscribe(); });
    }
    
    // CSS shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-4px)} 75%{transform:translateX(4px)} }
        .shake { animation: shake 0.5s ease; }
    `;
    document.head.appendChild(style);
});
