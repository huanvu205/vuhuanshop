        </div> <!-- /.admin-content -->
    </main> <!-- /.admin-main -->
</div> <!-- /.admin-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Custom Admin Scripts -->
<script>
    // Theme Management (Light / Dark Mode)
    const themeToggleBtn = document.getElementById('themeToggle');
    
    function getTheme() {
        return localStorage.getItem('admin-theme') || 'light';
    }
    
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('admin-theme', theme);
        updateThemeIcon(theme);
    }
    
    function updateThemeIcon(theme) {
        if (!themeToggleBtn) return;
        const icon = themeToggleBtn.querySelector('i');
        if (theme === 'dark') {
            icon.className = 'fas fa-sun text-warning';
        } else {
            icon.className = 'fas fa-moon';
        }
    }
    
    // Initial theme setup on load
    updateThemeIcon(getTheme());
    
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = getTheme();
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
    }

    // Sidebar Toggling for Mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    }

    // CSRF token helper
    function getCsrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    // Custom Toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColors = { 
            success: '#10B981', 
            error: '#EF4444', 
            warning: '#F59E0B' 
        };
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: ${bgColors[type] || bgColors.success};
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            z-index: 9999;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
            animation: slideIn 0.3s ease;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Slide-in keyframe style injection
    if (!document.getElementById('toast-animation-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'toast-animation-styles';
        styleSheet.innerText = `
            @keyframes slideIn {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(styleSheet);
    }

    // Delete confirmation
    function confirmDelete(url, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa "${name}"?\nHành động này không thể hoàn tác.`)) {
            window.location.href = url;
        }
    }

    // Image upload preview
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        
        const file = input.files[0];
        if (!file) return;
        
        if (!file.type.match('image.*')) { 
            showToast('Vui lòng chọn một file ảnh hợp lệ', 'error'); 
            return; 
        }
        
        const reader = new FileReader();
        reader.onload = e => { 
            preview.src = e.target.result; 
            preview.style.display = 'block'; 
        };
        reader.readAsDataURL(file);
    }

    // Specs adding manager
    function addSpec() {
        const container = document.getElementById('specs-container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'spec-row';
        row.innerHTML = `
            <input type="text" name="spec_name[]" class="form-control" placeholder="Tên thông số (VD: RAM)">
            <input type="text" name="spec_value[]" class="form-control" placeholder="Giá trị (VD: 8GB)">
            <button type="button" class="btn-remove-spec" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    }

    // Variant capacity adding manager
    function addVariantCapacity() {
        const container = document.getElementById('variant-capacity-container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'd-flex gap-2 mb-2 align-items-center variant-row';
        row.innerHTML = `
            <input type="text" name="variant_capacity_name[]" class="form-control" placeholder="Tên phiên bản (VD: 256GB)" style="border-radius:8px;">
            <input type="number" name="variant_capacity_diff[]" class="form-control" placeholder="Giá cộng thêm (₫, VD: 2000000)" value="0" style="border-radius:8px;">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()" style="border-radius:8px;"><i class="fas fa-trash-alt"></i></button>
        `;
        container.appendChild(row);
    }

    // Variant color adding manager
    function addVariantColor() {
        const container = document.getElementById('variant-color-container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'd-flex gap-2 mb-2 align-items-center variant-row';
        row.innerHTML = `
            <input type="text" name="variant_color_name[]" class="form-control" placeholder="Tên màu (VD: Titan Tự Nhiên)" style="border-radius:8px;">
            <input type="color" name="variant_color_hex[]" class="form-control form-control-color" value="#333333" title="Chọn mã màu" style="width:50px; height:38px; border-radius:8px; padding:2px;">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()" style="border-radius:8px;"><i class="fas fa-trash-alt"></i></button>
        `;
        container.appendChild(row);
    }

    // Active Sidebar Navigation links highlighting
    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
        if (item.href === window.location.href) {
            item.classList.add('active');
            
            // Auto active parent label style if needed
            let parentSection = item.previousElementSibling;
            while(parentSection && !parentSection.classList.contains('sidebar-section-label')) {
                parentSection = parentSection.previousElementSibling;
            }
            if(parentSection) {
                parentSection.style.color = 'rgba(255,255,255,0.6)';
            }
        }
    });

    // Auto fadeout bootstrap/system alerts after 4s
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.querySelector('.btn-close')) return;
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        });
    }, 4000);

    // Notification handlers
    window.readNotification = function(id, type, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        fetch('<?= url("admin/notifications/mark-read") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'id=' + id + '&csrf_token=' + getCsrf()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find element and remove 'unread' class
                const element = document.querySelector(`.dropdown-item-custom[data-id="${id}"]`);
                if (element) {
                    element.classList.remove('unread');
                }
                
                // Check if there are any unread notifications left in the list
                const unreadCount = document.querySelectorAll('.dropdown-item-custom.unread').length;
                if (unreadCount === 0) {
                    const dot = document.getElementById('unreadNotifsDot');
                    if (dot) dot.remove();
                }
                
                // Redirect based on type
                if (type === 'order') {
                    window.location.href = '<?= url("admin/orders") ?>';
                } else if (type === 'review') {
                    window.location.href = '<?= url("admin/reviews") ?>';
                } else if (type === 'stock') {
                    window.location.href = '<?= url("admin/products") ?>';
                } else {
                    window.location.reload();
                }
            } else {
                console.error('Lỗi: ' + data.message);
            }
        })
        .catch(err => console.error(err));
    };

    window.markAllNotificationsRead = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        fetch('<?= url("admin/notifications/mark-all-read") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'csrf_token=' + getCsrf()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove 'unread' class from all items
                document.querySelectorAll('.dropdown-item-custom.unread').forEach(el => {
                    el.classList.remove('unread');
                });
                
                // Remove dot badge
                const dot = document.getElementById('unreadNotifsDot');
                if (dot) dot.remove();
            } else {
                alert('Lỗi khi đánh dấu đã đọc: ' + data.message);
            }
        })
        .catch(err => console.error(err));
    };
</script>

</body>
</html>
