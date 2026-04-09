<?php
namespace Pickups\User; 

use Pickups\Helpers\Data;
use Pickups\Model\Location;
use Pickups\Model\Product;
use Pickups\Model\Order;


class Menu {
    // Class implementation goes here

    
    public static function render($html=null, $defaultLang = 'spanish') {
        $uid = uniqid();

        if (empty($html)) {
            ob_start();
            // Pass $uid to the template
            require __DIR__ . "/menu-template.php";
            $menuHtml = ob_get_clean();
        } else {
            $menuHtml = $html;
        }

        $data = [
            'menuHtml' => $menuHtml,
        ];
    
        ?>
        <script>
            (function() {
                const { menuHtml } = <?= json_encode($data) ?>;
                const menuHtml_<?= $uid ?> = menuHtml;
                window.menuHtml_<?= $uid ?> = menuHtml;

                window.openMenu_<?= $uid ?> = function(event) {
                    document.getElementById('order-now-container-<?= $uid ?>').classList.add('show');
                    const ifr = document.getElementById('order-iframe-<?= $uid ?>');
                    ifr.style.display = '';
                };

                window.closeMenu_<?= $uid ?> = function(event) {
                    document.getElementById('order-now-container-<?= $uid ?>').classList.remove('show');
                    const ifr = document.getElementById('order-iframe-<?= $uid ?>');
                    ifr.style.display = 'none';
                };

                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('order-now-container-<?= $uid ?>');
                    if (container) document.body.appendChild(container);

                    const ifr = document.getElementById('order-iframe-<?= $uid ?>');
                    if (!ifr) return;
                    
                    ifr.contentDocument.open();
                    ifr.contentDocument.write(menuHtml_<?= $uid ?>);
                    ifr.contentDocument.close();
                    
                    ifr.contentWindow.addEventListener('close-all', function(event) {
                        window.closeMenu_<?= $uid ?>(event);
                    });
                });
            })();
        </script>
       
        <button style="color: black; background: white; padding: .25rem; margin: 1rem; min-width: 7rem; border-radius: 31px; min-height: 2.5rem !important" onclick="openMenu_<?= $uid ?>(event)">Menu</button>
        <div id="order-now-container-<?= $uid ?>" class="order-now-container">
            <iframe id="order-iframe-<?= $uid ?>" class="order-iframe" style="display: none">
            </iframe>
        </div>
        
       

        <?php
        
    }


    public static function getMenuHtml(){

        $menu = json_decode(file_get_contents(__DIR__. '/menu-import.json'), true);
        $wine = json_decode(file_get_contents(__DIR__. '/wine-menu.json'), true);
        $lang = $_GET['lang'] ?? 'es';

        $lang = $lang ?? 'es';
        $menuData = $menu['menu'][$lang] ?? [];
        $wineData = $wine['wine_menu'][$lang] ?? [];

    
        ob_start();
?>

<!DOCTYPE html>
<html class="light" lang="<?= $lang ?>">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Come Porketa - Carta</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ec5b13",
                        "background-light": "#f8f6f6",
                        "background-dark": "#221610",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }

        function backToSite(event) {
            window.dispatchEvent(new Event('close-all'))

        }
    </script>
    <style>
        /* Suavizar el scroll al hacer clic en los enlaces */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Clase para el estado activo de la navegación */
        .nav-link-active {
            opacity: 1 !important;
            color: #ec5b13 !important;
            font-weight: 800;
        }

        .nav-link.active-nav-link {
            color: #ec5b13 !important;
            border-bottom: 2px solid #ec5b13;
            opacity: 1;
        }
    </style>
</head>


<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 transition-colors duration-300">
<a href="#" id="back-to-side" onclick="backToSite(event)"
        class="fixed top-8 z-50 right-8 z-[60] bg-primary text-white w-12 h-12 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300  translate-y-4">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <div class="relative flex min-h-screen flex-col overflow-x-hidden">
<main class="max-w-6xl mx-auto px-6 py-10">

<!-- NAV DINÁMICO -->
<div class="sticky top-0 bg-white z-50 mb-10 flex justify-between items-center px-6 py-4 border-b">
    <div class="flex gap-6 overflow-x-auto no-scrollbar md:flex-wrap">
        <?php foreach($menuData['sections'] as $section): 
            $id = strtolower(str_replace(' ', '-', $section['name']));
        ?>
        <a href="#<?= $id ?>" class="nav-item opacity-50 whitespace-nowrap">
            <span class="font-bold"><?= $section['name'] ?></span>
        </a>
        <?php endforeach; ?>

        <?php if(!empty($wineData)): ?>
        <a href="#vinos" class="nav-item opacity-50 font-bold whitespace-nowrap">🍷 Vinos</a>
        <?php endif; ?>
    </div>
    <button id="mobile-menu-btn" class="md:hidden p-2 text-primary flex items-center">
        <span class="material-symbols-outlined text-3xl">menu</span>
    </button>
</div>

<!-- SECCIONES MENU -->
<?php foreach($menuData['sections'] as $section): 
    $id = strtolower(str_replace(' ', '-', $section['name']));
?>
<section id="<?= $id ?>" class="mb-20">

<h2 class="text-3xl font-extrabold mb-8">
    <?= $section['name'] ?>
</h2>

<div class="grid md:grid-cols-2 gap-8">

<?php foreach($section['items'] as $item): ?>

<?php 
// Soporta string o objeto
if(is_string($item)){
    $item = [
        "name" => $item,
        "description" => "",
        "price" => rand(8,18),
        "image" => "https://via.placeholder.com/300"
    ];
}
?>

<div class="flex gap-6">
    <img src="<?= $item['image'] ?>" class="w-32 h-32 object-cover rounded-xl"/>

    <div class="flex-1">
        <div class="flex justify-between">
            <h3 class="font-bold text-lg"><?= $item['name'] ?></h3>
            <?php if(isset($item['price'])): ?>
                <span class="text-primary font-bold">
                    <?= formatPrice($item['price']) ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if(!empty($item['description'])): ?>
        <p class="text-sm text-gray-500 mt-2">
            <?= $item['description'] ?>
        </p>
        <?php endif; ?>
    </div>
</div>

<?php endforeach; ?>

</div>
</section>
<?php endforeach; ?>

<!-- VINOS -->
<?php if(!empty($wineData)): ?>
<section id="vinos" class="mb-20">

<h2 class="text-3xl font-extrabold mb-8">
    <?= $wineData['title'] ?>
</h2>

<div class="space-y-6">

<?php foreach($wineData['sections'][0]['items'] as $wine): ?>

<div class="flex justify-between border-b pb-4">

<div>
    <h4 class="font-bold"><?= $wine['name'] ?></h4>

    <p class="text-sm text-gray-500">
        <?= $wine['type'] ?> 
        <?php if(!empty($wine['grapes'])): ?>
            (<?= implode(', ', $wine['grapes']) ?>)
        <?php endif; ?>
        - <?= $wine['description'] ?>
    </p>

    <span class="text-xs text-gray-400">
        <?= $wine['region'] ?>
    </span>
</div>

<div class="text-right">
    <?php if(isset($wine['price']['glass'])): ?>
        <div>Copa: <?= formatPrice($wine['price']['glass']) ?></div>
    <?php endif; ?>

    <?php if(isset($wine['price']['bottle'])): ?>
        <div>Botella: <?= formatPrice($wine['price']['bottle']) ?></div>
    <?php endif; ?>
</div>

</div>

<?php endforeach; ?>

</div>

<!-- NOTAS -->
<div class="mt-10 text-center text-gray-500 text-sm">
<?php foreach($wineData['notes'] as $note): ?>
    <div><?= $note ?></div>
<?php endforeach; ?>
</div>

</section>
<?php endif; ?>

</main>

    </div>

    <!-- Mobile Drawer -->
    <div id="mobile-drawer" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none md:hidden">
        <div id="drawer-content" class="fixed right-0 top-0 h-full w-72 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col p-8 z-[101]">
            <div class="flex justify-between items-center mb-10">
                <span class="text-2xl font-extrabold text-primary uppercase">Menú</span>
                <button id="close-drawer-btn" class="text-gray-500">
                    <span class="material-symbols-outlined text-4xl">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-6 font-display font-bold text-lg tracking-tight overflow-y-auto no-scrollbar">
                <?php foreach($menuData['sections'] as $section): 
                    $id = strtolower(str_replace(' ', '-', $section['name']));
                ?>
                <a class="nav-link text-slate-700 border-l-4 border-transparent pl-4" href="#<?= $id ?>"><?= $section['name'] ?></a>
                <?php endforeach; ?>
                <?php if(!empty($wineData)): ?>
                <a class="nav-link text-slate-700 border-l-4 border-transparent pl-4" href="#vinos">🍷 Vinos</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
// ScrollSpy and Drawer Logic
function initMenu() {
    const sections = document.querySelectorAll('section');
    const links = document.querySelectorAll('.nav-item, .nav-link');

    if (!sections.length || !links.length) {
        setTimeout(initMenu, 100);
        return;
    }

    const observerOptions = {
        root: null,
        rootMargin: '-10% 0px -80% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                links.forEach(link => {
                    link.classList.remove('nav-link-active', 'active-nav-link');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('nav-link-active', 'active-nav-link');
                    }
                });
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        observer.observe(section);
    });

    // Mobile Drawer Toggle logic
    const mobileDrawer = document.getElementById('mobile-drawer');
    const drawerContent = document.getElementById('drawer-content');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const closeDrawerBtn = document.getElementById('close-drawer-btn');

    function toggleDrawer(open) {
        if (!mobileDrawer || !drawerContent) return;
        if (open) {
            mobileDrawer.classList.remove('opacity-0', 'pointer-events-none');
            drawerContent.classList.remove('translate-x-full');
        } else {
            mobileDrawer.classList.add('opacity-0', 'pointer-events-none');
            drawerContent.classList.add('translate-x-full');
        }
    }

    if (mobileMenuBtn) mobileMenuBtn.onclick = () => toggleDrawer(true);
    if (closeDrawerBtn) closeDrawerBtn.onclick = () => toggleDrawer(false);
    if (mobileDrawer) mobileDrawer.onclick = (e) => { 
        if (e.target === mobileDrawer) toggleDrawer(false); 
    };

    document.querySelectorAll('#mobile-drawer .nav-link').forEach(link => {
        link.onclick = () => toggleDrawer(false);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMenu);
} else {
    initMenu();
}
</script>

</body>
</html>


    <?php
        $html = ob_get_contents();
        ob_clean();
        return $html;
        
    }

  

}