/**
 * Sovereign Control - SuperAdmin Dashboard Logic
 * Handles Sidebar transitions and state-dependent UI toggles
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Element Selectors
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleBtn = document.getElementById('toggleSidebar');
    const openBtn = document.getElementById('openSidebarBtn');

    // 2. Safety Check: Only execute if elements exist on the page
    if (!sidebar || !toggleBtn || !openBtn) {
        console.warn("Sovereign UI: Navigation elements missing from DOM.");
        return;
    }

    /**
     * Updates the UI state between Full and Mini modes
     * @param {boolean} isMini 
     */
    function updateSidebarState(isMini) {
        if (isMini) {
            // Minimize State
            sidebar.classList.add('mini');
            mainContent.classList.add('expanded');
            
            // B) Transition toggle to Right Arrow
            toggleBtn.innerHTML = '→'; 
            toggleBtn.title = "Expand Sidebar";
        } else {
            // Full State
            sidebar.classList.remove('mini');
            mainContent.classList.remove('expanded');
            
            // B) Transition toggle back to Close Hybrid
            toggleBtn.innerHTML = '← &times;'; 
            toggleBtn.title = "Minimize Sidebar";
        }
    }

    // 3. Event Listeners

    // Sidebar Internal Toggle (← × / →)
    toggleBtn.addEventListener('click', () => {
        const isCurrentlyMini = sidebar.classList.contains('mini');
        updateSidebarState(!isCurrentlyMini);
    });

    // A) External Header Toggle (Open Sidebar →)
    openBtn.addEventListener('click', () => {
        updateSidebarState(false);
    });


const toggles = document.querySelectorAll('.submenu-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const submenu = document.getElementById(targetId);
            const arrow = this.querySelector('.arrow-icon');

            // FIX: Check if it's NOT already block (covers "" and "none")
            if (submenu.style.display !== "block") {
                submenu.style.display = "block";
                submenu.classList.remove('is-hidden'); // Sync class with style
                if(arrow) arrow.innerText = "▲";
            } else {
                submenu.style.display = "none";
                submenu.classList.add('is-hidden'); // Sync class with style
                if(arrow) arrow.innerText = "▼";
            }
        });
    });


});