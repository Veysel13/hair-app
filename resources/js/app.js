// Alpine.js
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

// Chart.js
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
window.Chart = Chart;

// Global Date/Time Picker click-open handler
document.addEventListener('click', function (e) {
    if (e.target && (e.target.matches('input[type="date"]') || e.target.matches('input[type="time"]'))) {
        try {
            e.target.showPicker();
        } catch (err) {
            // fail-safe
        }
    }
});

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .catch(err => console.error('Service worker registration failed', err));
    });
}

// Helper to check if PWA banner was dismissed in the last 7 days
window.isPwaDismissed = function() {
    const dismissedAt = localStorage.getItem('pwa-dismissed-at');
    if (!dismissedAt) return false;
    const sevenDaysInMs = 7 * 24 * 60 * 60 * 1000;
    return (Date.now() - parseInt(dismissedAt, 10)) < sevenDaysInMs;
};

// Global PWA Installation Banner handler
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const pwaBanner = document.getElementById('pwa-install-banner');
    if (pwaBanner && !window.isPwaDismissed()) {
        pwaBanner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
        pwaBanner.classList.add('translate-y-0', 'opacity-100');
    }
});

// Setup PWA install button click listener
document.addEventListener('DOMContentLoaded', () => {
    const installBtn = document.getElementById('pwa-install-btn');
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            window.dismissPwaBanner();
        });
    }

    // iOS/iPadOS device detection and guide customization
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                  (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    
    if (isIOS && !isStandalone && !window.isPwaDismissed()) {
        const pwaBanner = document.getElementById('pwa-install-banner');
        const pwaTitle = document.getElementById('pwa-title');
        const pwaDescription = document.getElementById('pwa-description');
        const pwaButtons = document.getElementById('pwa-buttons-container');
        
        if (pwaBanner) {
            if (pwaTitle) pwaTitle.textContent = "Salonim'i iPhone'unuza Yükleyin 📲";
            if (pwaDescription) {
                pwaDescription.innerHTML = `
                    Uygulamayı yüklemek için:
                    <ol class="list-decimal pl-4 mt-2 space-y-1.5 text-white/90 text-[11px] sm:text-xs">
                        <li>Safari alt menüsündeki <span class="font-bold text-[#e879f9]">Paylaş (Share) ⎙</span> butonuna basın.</li>
                        <li>Aşağı kaydırıp <span class="font-bold text-[#e879f9]">'Ana Ekrana Ekle'</span> seçeneğine dokunun.</li>
                    </ol>
                `;
            }
            if (pwaButtons) {
                pwaButtons.innerHTML = `
                    <button onclick="window.dismissPwaBanner()" class="btn btn-primary text-xs py-2 px-4 w-full font-bold">
                        Anladım 👍
                    </button>
                `;
            }
            // Show the banner with a slight delay
            setTimeout(() => {
                pwaBanner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
                pwaBanner.classList.add('translate-y-0', 'opacity-100');
            }, 1500);
        }
    }

    // Show or hide PWA promo section on the homepage
    if (!isStandalone) {
        const promoSection = document.getElementById('pwa-homepage-promo');
        if (promoSection) {
            promoSection.style.display = 'block';
            
            // Customize the promo for iOS devices
            if (isIOS) {
                const promoDesc = document.getElementById('pwa-promo-desc');
                const promoAction = document.getElementById('pwa-promo-action');
                if (promoDesc) {
                    promoDesc.innerHTML = `
                        iPhone/iPad cihazlarında uygulamayı kurmak için:
                        <ol class="list-decimal text-left max-w-sm mx-auto pl-4 mt-3 space-y-1.5 text-white/90 text-[11px] sm:text-xs">
                            <li>Safari alt çubuğundaki <span class="font-bold text-[#e879f9]">Paylaş (Share) ⎙</span> simgesine basın.</li>
                            <li>Aşağı kaydırıp <span class="font-bold text-[#e879f9]">'Ana Ekrana Ekle'</span> seçeneğine dokunun.</li>
                        </ol>
                    `;
                }
                if (promoAction) {
                    promoAction.innerHTML = `
                        <span class="text-xs text-[#7070a0] font-semibold">Safari tarayıcısı ile uyumludur </span>
                    `;
                }
            }
        }
    }
});

window.dismissPwaBanner = function() {
    const pwaBanner = document.getElementById('pwa-install-banner');
    if (pwaBanner) {
        pwaBanner.classList.remove('translate-y-0', 'opacity-100');
        pwaBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
    }
    // Dismiss for exactly 7 days
    localStorage.setItem('pwa-dismissed-at', Date.now());
};

window.triggerPwaInstall = function() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(({ outcome }) => {
            deferredPrompt = null;
            window.dismissPwaBanner();
        });
    } else {
        const pwaBanner = document.getElementById('pwa-install-banner');
        if (pwaBanner) {
            pwaBanner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            pwaBanner.classList.add('translate-y-0', 'opacity-100');
        }
    }
};
