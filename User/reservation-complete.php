<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#E43B16", // Main Brand Color
                        "on-primary": "#ffffff",
                        "surface": "#f6f6f6",
                        "on-surface": "#2d2f2f",
                        "on-surface-variant": "#5a5c5c",
                        "surface-container": "#e7e8e8",
                        "surface-container-high": "#e1e3e3",
                        "outline-variant": "#acadad",
                    },
                    "borderRadius": {
                        "DEFAULT": "0px",
                        "lg": "0px",
                        "xl": "0px",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Space Grotesk"],
                        "body": ["Work Sans"],
                        "label": ["Work Sans"]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
        }

        .brush-font {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 900;
        }

        .editorial-shadow {
            box-shadow: 0 40px 60px -15px rgba(45, 47, 47, 0.08);
        }
    </style>
</head>

<body class="bg-surface text-on-surface font-body selection:bg-primary/20 selection:text-primary">

    <header class="bg-primary">
        <div class="flex justify-between items-center w-full px-8 py-6 max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-4">
                <img src="/wp-content/plugins/pickups/logo.png" alt="COME Logo" class="h-10 object-contain" />
            </div>
            <nav class="hidden md:flex gap-12 font-['Space_Grotesk'] font-bold tracking-tight uppercase">
                <a class="text-white  hover:text-primary transition-colors duration-200" href="/" data-i18n="menu">MENÚ</a>
            </nav>
        </div>
    </header>

    <main class="min-h-[716px] flex flex-col items-center justify-center px-4 relative overflow-hidden">
        <!-- Background Artisanal Imagery -->
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
            <img alt="Background texture" class="w-full h-full object-cover grayscale"/>
        </div>

        <!-- Success Message Content -->
        <div class="relative z-10 w-full max-w-4xl mx-auto flex flex-col items-center text-center">
            <!-- Hand-Drawn Branding Overlay -->
            <div class="absolute -top-16 -left-8 md:-left-16 opacity-10 select-none pointer-events-none">
                
            </div>

            <!-- Confirmation Title -->
            <h1 class="font-headline font-black text-5xl md:text-7xl text-primary uppercase tracking-tighter mb-4 leading-none" data-i18n="title">
                ¡TU MESA ESTÁ LISTA!
            </h1>
            <p class="font-body text-on-surface-variant text-lg md:text-xl max-w-lg mb-12" data-i18n="message">
                Ahumamos con paciencia para que tú solo te preocupes de disfrutar. Te esperamos en el templo de la panceta.
            </p>

            <!-- CTA Actions -->
            <div class="flex flex-col md:flex-row gap-6 w-full max-w-md">
                <a id="redirect-btn" href="/" class="flex-1 bg-primary text-on-primary font-headline font-bold py-6 px-8 transition-all hover:bg-opacity-90 active:scale-95 uppercase tracking-tight text-center" data-i18n="button_home">
                    VOLVER AL INICIO
                </a>
            </div>

            <!-- Secondary Info -->
            <div class="mt-12 flex flex-col items-center gap-4">
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    <span class="font-body text-sm uppercase tracking-widest" data-i18n="address">Carrer de la Marina 124, Barcelona</span>
                </div>
                <p class="text-xs text-on-surface-variant italic" data-i18n="policy">Se mantendrá la reserva durante un máximo de 15 minutos.</p>
            </div>
        </div>

        <!-- Decorative Elements -->
        
    </main>

    <footer class="bg-[#f6f6f6] dark:bg-[#2d2f2f] mt-24 pt-12 pb-16 border-t border-outline-variant/30">
        <div class="flex flex-col md:flex-row justify-between items-center w-full px-8 max-w-screen-2xl mx-auto gap-4">
            
            <div class="font-body text-xs uppercase tracking-widest text-[#5a5c5c] dark:text-[#dbdddd] text-center" data-i18n="copyright">
                © 2024 AHUMAMOS. LA PANCETA CATALANA. TODOS LOS DERECHOS RESERVADOS.
            </div>
            <div class="flex gap-6 font-body text-xs uppercase tracking-widest">
                <a class="text-[#5a5c5c] dark:text-[#dbdddd] hover:text-primary transition-colors" href="#" data-i18n="instagram">INSTAGRAM</a>
                <a class="text-[#5a5c5c] dark:text-[#dbdddd] hover:text-primary transition-colors" href="#" data-i18n="legal">AVISO LEGAL</a>
                <a class="text-[#5a5c5c] dark:text-[#dbdddd] hover:text-primary transition-colors" href="#" data-i18n="privacy">POLÍTICA DE PRIVACIDAD</a>
            </div>
        </div>
    </footer>

    <script>
        const translations = {
            es: {
                menu: "MENÚ",
                reservations: "RESERVAS",
                contact: "CONTACTO",
                title: "¡TU MESA ESTÁ LISTA!",
                message: "Ahumamos con paciencia para que tú solo te preocupes de disfrutar. Te esperamos en el templo de la panceta.",
                button_home: "VOLVER AL INICIO",
                button_pdf: "DESCARGAR PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "Se mantendrá la reserva durante un máximo de 15 minutos.",
                copyright: "© 2024 AHUMAMOS. LA PANCETA CATALANA. TODOS LOS DERECHOS RESERVADOS.",
                instagram: "INSTAGRAM",
                legal: "AVISO LEGAL",
                privacy: "POLÍTICA DE PRIVACIDAD"
            },
            spanish: {
                menu: "MENÚ",
                reservations: "RESERVAS",
                contact: "CONTACTO",
                title: "¡TU MESA ESTÁ LISTA!",
                message: "Ahumamos con paciencia para que tú solo te preocupes de disfrutar. Te esperamos en el templo de la panceta.",
                button_home: "VOLVER AL INICIO",
                button_pdf: "DESCARGAR PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "Se mantendrá la reserva durante un máximo de 15 minutos.",
                copyright: "© 2024 AHUMAMOS. LA PANCETA CATALANA. TODOS LOS DERECHOS RESERVADOS.",
                instagram: "INSTAGRAM",
                legal: "AVISO LEGAL",
                privacy: "POLÍTICA DE PRIVACIDAD"
            },
            ca: {
                menu: "MENÚ",
                reservations: "RESERVES",
                contact: "CONTACTE",
                title: "¡LA TEVA TAULA ESTÀ LLESTA!",
                message: "Fumem amb paciència perquè tu només et preocupis de gaudir. T'esperem al temple de la cansalada.",
                button_home: "TORNAR A L'INICI",
                button_pdf: "DESCARREGAR PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "Es mantindrà la reserva durant un màxim de 15 minuts.",
                copyright: "© 2024 FUMEM. LA CANSALADA CATALANA. TOTS ELS DRETS RESERVATS.",
                instagram: "INSTAGRAM",
                legal: "AVÍS LEGAL",
                privacy: "POLÍTICA DE PRIVACITAT"
            },
            catalan: {
                menu: "MENÚ",
                reservations: "RESERVES",
                contact: "CONTACTE",
                title: "¡LA TEVA TAULA ESTÀ LLESTA!",
                message: "Fumem amb paciència perquè tu només et preocupis de gaudir. T'esperem al temple de la cansalada.",
                button_home: "TORNAR A L'INICI",
                button_pdf: "DESCARREGAR PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "Es mantindrà la reserva durant un màxim de 15 minuts.",
                copyright: "© 2024 FUMEM. LA CANSALADA CATALANA. TOTS ELS DRETS RESERVATS.",
                instagram: "INSTAGRAM",
                legal: "AVÍS LEGAL",
                privacy: "POLÍTICA DE PRIVACITAT"
            },
            en: {
                menu: "MENU",
                reservations: "RESERVATIONS",
                contact: "CONTACT",
                title: "YOUR TABLE IS READY!",
                message: "We smoke with patience so you only worry about enjoying. We wait for you in the temple of bacon.",
                button_home: "RETURN TO HOME",
                button_pdf: "DOWNLOAD PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "The reservation will be kept for a maximum of 15 minutes.",
                copyright: "© 2024 WE SMOKE. THE CATALAN BACON. ALL RIGHTS RESERVED.",
                instagram: "INSTAGRAM",
                legal: "LEGAL NOTICE",
                privacy: "PRIVACY POLICY"
            },
            english: {
                menu: "MENU",
                reservations: "RESERVATIONS",
                contact: "CONTACT",
                title: "YOUR TABLE IS READY!",
                message: "We smoke with patience so you only worry about enjoying. We wait for you in the temple of bacon.",
                button_home: "RETURN TO HOME",
                button_pdf: "DOWNLOAD PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "The reservation will be kept for a maximum of 15 minutes.",
                copyright: "© 2024 WE SMOKE. THE CATALAN BACON. ALL RIGHTS RESERVED.",
                instagram: "INSTAGRAM",
                legal: "LEGAL NOTICE",
                privacy: "PRIVACY POLICY"
            },
            fr: {
                menu: "MENU",
                reservations: "RÉSERVATIONS",
                contact: "CONTACT",
                title: "VOTRE TABLE EST PRÊTE !",
                message: "Nous fumons avec patience pour que vous ne vous souciiez que de profiter. Nous vous attendons au temple de la pancetta.",
                button_home: "RETOUR À L'ACCUEIL",
                button_pdf: "TÉLÉCHARGER LE PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "La réservation sera conservée pendant un maximum de 15 minutes.",
                copyright: "© 2024 NOUS FUMONS. LA PANCETTA CATALANE. TOUS DROITS RÉSERVÉS.",
                instagram: "INSTAGRAM",
                legal: "MENTIONS LÉGALES",
                privacy: "POLITIQUE DE CONFIDENTIALITÉ"
            },
            french: {
                menu: "MENU",
                reservations: "RÉSERVATIONS",
                contact: "CONTACT",
                title: "VOTRE TABLE EST PRÊTE !",
                message: "Nous fumons avec patience pour que vous ne vous souciiez que de profiter. Nous vous attendons au temple de la pancetta.",
                button_home: "RETOUR À l'ACCUEIL",
                button_pdf: "TÉLÉCHARGER LE PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "La réservation sera conservée pendant un maximum de 15 minutes.",
                copyright: "© 2024 NOUS FUMONS. LA PANCETTA CATALANE. TOUS DROITS RÉSERVÉS.",
                instagram: "INSTAGRAM",
                legal: "MENTIONS LÉGALES",
                privacy: "POLITIQUE DE CONFIDENTIALITÉ"
            },
            it: {
                menu: "MENU",
                reservations: "PRENOTAZIONI",
                contact: "CONTATTO",
                title: "IL TUO TAVOLO È PRONTO!",
                message: "Affumichiamo con pazienza così che tu debba solo preoccuparti di goderti. Ti aspettiamo nel tempio della pancetta.",
                button_home: "TORNA ALLA HOME",
                button_pdf: "SCARICA IL PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "La prenotazione sarà mantenuta per un massimo di 15 minuti.",
                copyright: "© 2024 AFFUMICHIAMO. LA PANCETTA CATALANA. TUTTI I DIRITTI RISERVATI.",
                instagram: "INSTAGRAM",
                legal: "NOTE LEGALI",
                privacy: "INFORMATIVA SULLA PRIVACY"
            },
            italian: {
                menu: "MENU",
                reservations: "PRENOTAZIONI",
                contact: "CONTATTO",
                title: "IL TUO TAVOLO È PRONTO!",
                message: "Affumichiamo con pazienza così che tu debba solo preoccuparti di goderti. Ti aspettiamo nel tempio della pancetta.",
                button_home: "TORNA ALLA HOME",
                button_pdf: "SCARICA IL PDF",
                address: "Carrer de la Marina 124, Barcelona",
                policy: "La prenotazione sarà mantenuta per un massimo di 15 minuti.",
                copyright: "© 2024 AFFUMICHIAMO. LA PANCETTA CATALANA. TUTTI I DIRITTI RISERVATI.",
                instagram: "INSTAGRAM",
                legal: "NOTE LEGALI",
                privacy: "INFORMATIVA SULLA PRIVACY"
            }
        };

        function localize() {
            const lang = localStorage.getItem('lang') || localStorage.getItem('pickups_lang') || 'es';
            const translation = translations[lang] || translations[lang.substring(0, 2)] || translations.es;

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translation[key]) {
                    el.innerText = translation[key];
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            localize();

            const btn = document.getElementById('redirect-btn');
            if (!btn) return;

            let seconds = 8;
            const baseText = btn.innerText;

            const updateButton = (s) => {
                btn.innerText = `${baseText} (${s}s)`;
            };

            updateButton(seconds);

            const timer = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = '/';
                } else {
                    updateButton(seconds);
                }
            }, 1000);
        });
    </script>
</body>

</html>
