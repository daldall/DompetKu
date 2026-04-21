(function() {
    let deferredPrompt;
    let isInstallable = false;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        isInstallable = true;

        // Update UI notify the user they can install the PWA
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
        }

        // Dispatch a custom event for developers to listen to
        window.dispatchEvent(new CustomEvent('pwa-installable', { detail: { prompt: e } }));
    });

    window.addEventListener('appinstalled', (event) => {
        // Log install to analytics or update UI
        console.log('PWA was installed');
        isInstallable = false;
        deferredPrompt = null;
        
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }

        // Dispatch a custom event
        window.dispatchEvent(new CustomEvent('pwa-installed'));
    });

    window.laravelPwaInstall = {
        /**
         * Check if the app is installable (beforeinstallprompt has fired)
         * @returns {boolean}
         */
        canInstall: function() {
            return isInstallable;
        },

        /**
         * Show the install prompt
         * @returns {Promise<string|undefined>} 'accepted', 'dismissed', or undefined if not installable
         */
        showPrompt: async function() {
            if (!deferredPrompt) {
                console.warn('[Laravel PWA] Install prompt not available.');
                return;
            }

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`[Laravel PWA] User response to the install prompt: ${outcome}`);
            
            if (outcome === 'accepted') {
                isInstallable = false;
                const installBtn = document.getElementById('pwa-install-btn');
                if (installBtn) {
                    installBtn.style.display = 'none';
                }
            }
            
            deferredPrompt = null;
            return outcome;
        },

        /**
         * Check if the app is already installed/running in standalone mode
         * @returns {boolean}
         */
        isStandalone: function() {
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        }
    };

    // Initialize default button behavior if it exists
    document.addEventListener('DOMContentLoaded', () => {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.addEventListener('click', () => {
                window.laravelPwaInstall.showPrompt();
            });
        }
    });
})();
