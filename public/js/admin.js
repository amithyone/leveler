// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // Handle nav parent clicks to toggle submenus
    const navParents = document.querySelectorAll('.nav-parent');
    navParents.forEach(parent => {
        parent.addEventListener('click', function(e) {
            // Don't toggle if clicking on a link
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return;
            }
            
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('nav-submenu')) {
                const isActive = this.classList.contains('active');
                
                // Close all other submenus in the same group
                const allSubmenus = document.querySelectorAll('.nav-submenu');
                const allParents = document.querySelectorAll('.nav-parent');
                
                allSubmenus.forEach(menu => {
                    if (menu !== submenu) {
                        menu.classList.remove('show');
                    }
                });
                
                allParents.forEach(p => {
                    if (p !== this) {
                        p.classList.remove('active');
                    }
                });
                
                // Toggle current submenu
                if (isActive) {
                    this.classList.remove('active');
                    submenu.classList.remove('show');
                } else {
                    this.classList.add('active');
                    submenu.classList.add('show');
                }
            }
        });
    });

    // Handle nested submenu parents (Manage Trainees)
    const nestedParents = document.querySelectorAll('.nav-submenu .nav-parent');
    nestedParents.forEach(parent => {
        parent.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return;
            }
            
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('nav-submenu')) {
                const isActive = this.classList.contains('active');
                
                if (isActive) {
                    this.classList.remove('active');
                    submenu.classList.remove('show');
                } else {
                    this.classList.add('active');
                    submenu.classList.add('show');
                }
            }
        });
    });

    // Auto-expand submenus if active route
    if (document.querySelector('.trainees-submenu.show')) {
        const manageTraineesSubmenu = document.querySelector('.manage-trainees-submenu');
        const manageTraineesParent = document.querySelector('.manage-trainees-parent');
        if (manageTraineesSubmenu && manageTraineesParent) {
            manageTraineesSubmenu.classList.add('show');
            manageTraineesParent.classList.add('active');
        }
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (sidebar && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        }
    });
});

