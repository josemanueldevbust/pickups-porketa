<?php
namespace Pickups\User; 

use Pickups\Helpers\Data;
use Pickups\Model\Location;
use Pickups\Model\Product;
use Pickups\Model\Order;


class Menu {
    // Class implementation goes here

    
    public static function render($html=null) {

        if (empty($html)) {
            ob_start();
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
            const { menuHtml } = <?= json_encode($data) ?>;
            window.menuHtml = menuHtml;
            function openMenu(event) {
                document.querySelector('.order-now-container').classList.add('show');
                const ifr =  document.querySelector('#order-iframe');
                ifr.style.display = ''
                
            }

            function closeMenu(event){
                document.querySelector('.order-now-container').classList.remove('show');
                const ifr =  document.querySelector('#order-iframe');
                ifr.style.display = 'none';

            }
            document.addEventListener('DOMContentLoaded', function() {
                const ifr =  document.querySelector('#order-iframe');
                ifr.contentDocument.write(menuHtml)
                console.log(window.menuHtml)
                ifr.contentWindow.addEventListener('close-all', function(event) {
                    closeMenu(event);
                })
            })
        </script>
       
        <button style="    color: black;  background: white;    padding: .25rem;    margin: 1rem;    min-width: 7rem;    border-radius: 31px;" onclick="openMenu(event)">Menu</button>
        <div class="order-now-container">

            <iframe id="order-iframe" style="display: none">


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
            opacity: 100 !important;
            color: #ec5b13 !important;
        }

        .nav-link-active div {
            background-color: #ec5b13 !important;
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
<div class="sticky top-0 bg-white z-50 mb-10 overflow-x-auto flex gap-6">
<?php foreach($menuData['sections'] as $section): 
    $id = strtolower(str_replace(' ', '-', $section['name']));
?>
<a href="#<?= $id ?>" class="nav-item opacity-50">
    <span class="font-bold"><?= $section['name'] ?></span>
</a>
<?php endforeach; ?>

<?php if(!empty($wineData)): ?>
<a href="#vinos" class="nav-item opacity-50 font-bold">🍷 Vinos</a>
<?php endif; ?>
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

<script>
// ScrollSpy simple
const sections = document.querySelectorAll('section');
const links = document.querySelectorAll('.nav-item');

window.addEventListener('scroll', () => {
    let current = "";

    sections.forEach(section => {
        const top = section.offsetTop;
        if(scrollY >= top - 150){
            current = section.getAttribute('id');
        }
    });

    links.forEach(link => {
        link.classList.remove('nav-link-active');
        if(link.getAttribute('href') === '#' + current){
            link.classList.add('nav-link-active');
        }
    });
});
</script>

</body>
</html>


    <?php
        $html = ob_get_contents();
        ob_clean();
        return $html;
        
    }

  

}