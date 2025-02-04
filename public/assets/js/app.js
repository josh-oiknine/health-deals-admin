// Function to get cookie value by name
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Function to set cookie with expiry
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=/`;
}

// Function to handle responsive sidebar behavior
function handleResponsiveSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    if (!sidebar || !content) return;

    const isMobileView = window.innerWidth < 768; // Common breakpoint for mobile devices
    
    if (isMobileView) {
        sidebar.classList.add('sidebar-collapsed');
        content.classList.add('content-expanded');
        setCookie('sidebar_collapsed', true, 30);
    }
}

// Initialize sidebar functionality
function initSidebar() {
    // Initialize sidebar state from cookie
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (!sidebar || !content || !sidebarToggle) return;

    // Handle responsive behavior first
    handleResponsiveSidebar();

    // Function to manage tooltips based on sidebar state
    function manageTooltips(isCollapsed) {
        const tooltipTriggers = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggers.forEach(el => {
            const tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
            if (isCollapsed) {
                new bootstrap.Tooltip(el, {
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
    }

    // Set initial state from cookie (only for non-mobile screens)
    if (window.innerWidth >= 768) {
        const isCollapsed = getCookie('sidebar_collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            content.classList.add('content-expanded');
            manageTooltips(true);
        }
    }

    // Toggle sidebar and save state to cookie
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-collapsed');
        content.classList.toggle('content-expanded');
        
        // Save state to cookie (expires in 30 days)
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        setCookie('sidebar_collapsed', isCollapsed, 30);
        
        // Manage tooltips based on new state
        manageTooltips(isCollapsed);
    });
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    
    // Add resize event listener
    window.addEventListener('resize', handleResponsiveSidebar);
}); 