<?php
use Pickups\Model\Product;
use Pickups\Helpers\Data;

$products = Data::fetch_items(Product::class);
$menus = ['spanish' => [], 'english' => [], 'italian' => [], 'ca' => [], 'french' => []];

foreach ($products as $p) {
    if (!$p) continue;
    $cat = $p->getCategory();
    $sub = $p->getSubcategory();
    if(empty($cat)) continue;

    $item_es = ['name' => $p->getName_es(), 'description' => $p->getDesc_es(), 'price' => $p->getPrice(), 'type' => $p->getType_es(), 'image'=> $p->getImage()];
    $item_en = ['name' => $p->getName_en(), 'description' => $p->getDesc_en(), 'price' => $p->getPrice(), 'type' => $p->getType_en(), 'image'=> $p->getImage()];
    $item_it = ['name' => $p->getName_it(), 'description' => $p->getDesc_it(), 'price' => $p->getPrice(), 'type' => $p->getType_it(), 'image'=> $p->getImage()];
    $item_ca = ['name' => $p->getName_ca(), 'description' => $p->getDesc_ca(), 'price' => $p->getPrice(), 'type' => $p->getType_ca(), 'image'=> $p->getImage()];
    $item_fr = ['name' => $p->getName_fr(), 'description' => $p->getDesc_fr(), 'price' => $p->getPrice(), 'type' => $p->getType_fr(), 'image'=> $p->getImage()];

    if ($cat === 'specialty') {
        $menus['spanish'][$cat]['types'][] = $item_es;
        $menus['english'][$cat]['types'][] = $item_en;
        $menus['italian'][$cat]['types'][] = $item_it;
        $menus['ca'][$cat]['types'][] = $item_ca;
        $menus['french'][$cat]['types'][] = $item_fr;
    } elseif ($cat === 'drinks' && !empty($sub)) {
        $menus['spanish'][$cat][$sub][] = $item_es;
        $menus['english'][$cat][$sub][] = $item_en;
        $menus['italian'][$cat][$sub][] = $item_it;
        $menus['ca'][$cat][$sub][] = $item_ca;
        $menus['french'][$cat][$sub][] = $item_fr;
    } else {
        $menus['spanish'][$cat][] = $item_es;
        $menus['english'][$cat][] = $item_en;
        $menus['italian'][$cat][] = $item_it;
        $menus['ca'][$cat][] = $item_ca;
        $menus['french'][$cat][] = $item_fr;
    }
}

$data = ['menus' => $menus];
$jsonData = json_encode($data);

// Default language
$langKey = 'spanish';
$menu = $data['menus'][$langKey] ?? [];

// Helper to safely get nested array values
function get_val($array, $path, $default = '') {
    $keys = explode('.', $path);
    $current = $array;
    foreach($keys as $key) {
        if(isset($current[$key])) {
            $current = $current[$key];
        } else {
            return $default;
        }
    }
    return $current;
}

$sectionTitles = [
    'spanish' => [
        'specialty' => 'Especialidades',
        'to_share' => 'Para Compartir',
        'salads' => 'Ensaladas',
        'sandwiches' => 'Sándwiches',
        'burgers' => 'Hamburguesas',
        'desserts' => 'Postres',
        'drinks' => 'Bebidas',
        'wine_list' => 'Vinos',
        'soft_drinks' => 'Refrescos',
        'coffee_and_tea' => 'Café y Té',
        'beers' => 'Cervezas'
    ],
    'english' => [
        'specialty' => 'Specialties',
        'to_share' => 'To Share',
        'salads' => 'Salads',
        'sandwiches' => 'Sandwiches',
        'burgers' => 'Burgers',
        'desserts' => 'Desserts',
        'drinks' => 'Drinks',
        'wine_list' => 'Wine List',
        'soft_drinks' => 'Soft Drinks',
        'coffee_and_tea' => 'Coffee & Tea',
        'beers' => 'Beers'
    ],
    'italian' => [
        'specialty' => 'Specialità',
        'to_share' => 'Da Condividere',
        'salads' => 'Insalate',
        'sandwiches' => 'Tramezzini',
        'burgers' => 'Hamburger',
        'desserts' => 'Dolci',
        'drinks' => 'Bevande',
        'wine_list' => 'Vini',
        'soft_drinks' => 'Bevande analcoliche',
        'coffee_and_tea' => 'Caffè e Tè',
        'beers' => 'Birre'
    ],
    'ca' => [
        'specialty' => 'Especialitats',
        'to_share' => 'Per Compartir',
        'salads' => 'Amanides',
        'sandwiches' => 'Entrepans',
        'burgers' => 'Hamburgueses',
        'desserts' => 'Postres',
        'drinks' => 'Begudes',
        'wine_list' => 'Vins',
        'soft_drinks' => 'Refrescs',
        'coffee_and_tea' => 'Cafè i Te',
        'beers' => 'Cerveses'
    ],
    'french' => [
        'specialty' => 'Spécialités',
        'to_share' => 'À Partager',
        'salads' => 'Salades',
        'sandwiches' => 'Sandwichs',
        'burgers' => 'Hamburgers',
        'desserts' => 'Desserts',
        'drinks' => 'Boissons',
        'wine_list' => 'Vins',
        'soft_drinks' => 'Boissons Sans Alcool',
        'coffee_and_tea' => 'Café et Thé',
        'beers' => 'Bières'
    ]
];
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Work+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .editorial-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
        }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-high": "#e1e3e3",
                        "outline-variant": "#acadad",
                        "secondary": "#5c5b5b",
                        "surface-container-low": "#f0f1f1",
                        "primary-fixed": "#ff766d",
                        "on-surface-variant": "#5a5c5c",
                        "on-tertiary-fixed": "#340800",
                        "surface-dim": "#d2d5d5",
                        "tertiary-container": "#ff9475",
                        "secondary-container": "#e5e2e1",
                        "surface-container-highest": "#dbdddd",
                        "inverse-surface": "#0c0f0f",
                        "inverse-primary": "#ff544e",
                        "outline": "#757777",
                        "primary": "#e43b16",
                        "tertiary": "#ab2d00",
                        "surface-variant": "#dbdddd",
                        "on-primary-container": "#4f0004",
                        "surface-tint": "#e43b16",
                        "primary-fixed-dim": "#ff5a53",
                        "inverse-on-surface": "#9c9d9d",
                        "primary-dim": "#a40113",
                        "error-dim": "#9f0519",
                        "error": "#b31b25",
                        "on-primary": "#ffefed",
                        "on-error-container": "#570008",
                        "tertiary-fixed": "#ff9475",
                        "tertiary-dim": "#962700",
                        "background": "#f6f6f6",
                        "on-secondary-container": "#525151",
                        "error-container": "#fb5151",
                        "on-secondary-fixed-variant": "#5c5b5b",
                        "on-primary-fixed-variant": "#600007",
                        "primary-container": "#ff766d",
                        "on-surface": "#2d2f2f",
                        "on-tertiary": "#ffefeb",
                        "on-error": "#ffefee",
                        "secondary-fixed": "#e5e2e1",
                        "surface-container-lowest": "#ffffff",
                        "on-background": "#2d2f2f",
                        "surface": "#f6f6f6",
                        "secondary-dim": "#504f4f",
                        "on-secondary-fixed": "#403f3f",
                        "secondary-fixed-dim": "#d6d4d3",
                        "on-secondary": "#f5f2f1",
                        "tertiary-fixed-dim": "#ff7d57",
                        "surface-bright": "#f6f6f6",
                        "on-tertiary-container": "#601500",
                        "surface-container": "#e7e8e8",
                        "on-tertiary-fixed-variant": "#6f1a00",
                        "on-primary-fixed": "#000000"
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk"],
                        "body": ["Work Sans"],
                        "label": ["Work Sans"]
                    },
                    borderRadius: { "DEFAULT": "0px", "lg": "0px", "xl": "0px", "full": "9999px" },
                },
            },
        }
    </script>
</head>

<body class="bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container">
    
    <a href="#" id="back-to-side" onclick="backToSite(event)"
        class="fixed top-8 mt-5 md:mt-0 z-50 right-8 z-[60] bg-[#e43b16] text-[#f6f6f6] w-12 h-12 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300">
        <span class="material-symbols-outlined">close</span>
    </a>
    
    <!-- JSON DATA FOR CLIENT SIDE SWITCHING -->
    <script>
        function backToSite(event) {
            event.preventDefault();
            window.dispatchEvent(new Event('close-all'));
        }

        const menuData = <?= $jsonData ?>;
        const sectionTitles = <?= json_encode($sectionTitles) ?>;
        
        function changeLanguage(langKey) {
            const selectedMenu = menuData.menus[langKey];
            if (!selectedMenu) return;

            document.querySelectorAll('[data-i18n-path]').forEach(el => {
                const path = el.getAttribute('data-i18n-path');
                const keys = path.split('.');
                let val = selectedMenu;
                for (let k of keys) {
                    if (val && val[k] !== undefined) {
                        val = val[k];
                    } else {
                        val = null;
                        break;
                    }
                }
                if (val !== null) {
                    el.innerText = val;
                }
            });

            document.querySelectorAll('[data-i18n-title-key]').forEach(el => {
                const key = el.getAttribute('data-i18n-title-key');
                if (sectionTitles[langKey] && sectionTitles[langKey][key]) {
                    el.innerText = sectionTitles[langKey][key];
                }
            });
        }
    </script>

    <!-- TopAppBar -->
    <header class="fixed top-0 w-full z-50 bg-[#E43B16]  border-none pt-8">
        <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto">
            <div class="text-3xl font-black text-[#2d2f2f] dark:text-[#f6f6f6] uppercase tracking-tighter font-headline flex items-center">
                <img src="/wp-content/plugins/pickups/logo.png" alt="COME Logo" class="h-10 object-contain invert dark:invert-0" />
            </div>
            <div class="hidden md:flex items-center gap-6 font-headline font-bold text-md tracking-tight flex-wrap">
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#specialty" data-i18n-title-key="specialty"><?= $sectionTitles[$langKey]['specialty'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#to_share" data-i18n-title-key="to_share"><?= $sectionTitles[$langKey]['to_share'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#salads" data-i18n-title-key="salads"><?= $sectionTitles[$langKey]['salads'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#sandwiches" data-i18n-title-key="sandwiches"><?= $sectionTitles[$langKey]['sandwiches'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#burgers" data-i18n-title-key="burgers"><?= $sectionTitles[$langKey]['burgers'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#desserts" data-i18n-title-key="desserts"><?= $sectionTitles[$langKey]['desserts'] ?></a>
                <a class="text-[#f3f3f3ff] hover:text-[#949494ff]" href="#drinks" data-i18n-title-key="drinks"><?= $sectionTitles[$langKey]['drinks'] ?></a>
            </div>
            <div class="flex items-center gap-4 mr-14 md:mr-0">
                <select onchange="changeLanguage(this.value)" class="bg-[#f6f6f6] dark:bg-stone-900 font-headline font-bold text-sm uppercase px-2 py-1 border border-outline-variant cursor-pointer">
                    <option value="spanish">ES</option>
                    <option value="ca">CA</option>
                    <option value="french">FR</option>
                    <option value="english">EN</option>
                    <option value="italian">IT</option>
                </select>
            </div>
        </nav>
    </header>

    <main class="pt-24 md:pt-32">

        <!-- Specialty Section -->
        <?php if (isset($menu['specialty'])): ?>
        <section class="py-16 bg-surface-container-low" id="specialty">
            <div class="max-w-7xl mx-auto px-8">
                <div class="flex justify-between items-baseline mb-8 border-b border-outline-variant/20 pb-4">
                    <h2 class="font-headline text-4xl font-bold text-primary uppercase tracking-tight" data-i18n-title-key="specialty"><?= $sectionTitles[$langKey]['specialty'] ?></h2>
                    <span class="font-label text-on-surface-variant text-sm tracking-widest hidden md:inline">ARTISANAL CURATION</span>
                </div>
                <div class="mb-10 text-on-surface-variant font-body max-w-2xl" data-i18n-path="specialty.description">
                    <?= htmlspecialchars(get_val($menu, 'specialty.description')) ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <?php if (isset($menu['specialty']['types'])): ?>
                        <?php foreach ($menu['specialty']['types'] as $index => $item): ?>
                            <div class="group">
                                <div class="aspect-square bg-surface-container-highest mb-6 overflow-hidden">
                                     <!-- Placeholder image -->
                                    <img alt="Specialty" class="w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500" src="<?= $item['image'] ?>" />
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-headline font-bold text-xl uppercase tracking-tighter" data-i18n-path="specialty.types.<?= $index ?>.name">
                                        <?= htmlspecialchars($item['name'] ?? '') ?>
                                    </h3>
                                    <span class="font-headline font-bold text-primary whitespace-nowrap ml-4" data-i18n-path="specialty.types.<?= $index ?>.price">
                                        <?= htmlspecialchars($item['price'] ?? '') ?>
                                    </span>
                                </div>
                                <p class="font-body text-on-surface-variant text-sm leading-relaxed" data-i18n-path="specialty.types.<?= $index ?>.description">
                                    <?= htmlspecialchars($item['description'] ?? '') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- List Sections (To Share, Salads, Desserts) -->
        <section class="py-16 bg-surface-container-high" id="lists">
            <div class="max-w-7xl mx-auto px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                    
                    <?php 
                    $listSections = ['to_share', 'salads', 'desserts'];
                    foreach($listSections as $secKey): 
                        if (isset($menu[$secKey])):
                    ?>
                    <div id="<?= $secKey ?>">
                        <div class="mb-10 border-b-2 border-primary inline-block pb-1">
                            <h2 class="font-headline text-3xl font-bold text-primary uppercase" data-i18n-title-key="<?= $secKey ?>">
                                <?= $sectionTitles[$langKey][$secKey] ?>
                            </h2>
                        </div>
                        <ul class="space-y-6">
                            <?php foreach ($menu[$secKey] as $i => $item): ?>
                                <li class="flex flex-col group">
                                    <div class="flex justify-between items-baseline w-full">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-primary transition-colors" data-i18n-path="<?= $secKey ?>.<?= $i ?>.name">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                        </span>
                                        <span class="flex-grow border-b border-dotted border-outline-variant mx-4"></span>
                                        <span class="font-headline font-bold whitespace-nowrap ml-2">
                                            <span data-i18n-path="<?= $secKey ?>.<?= $i ?>.price"><?= htmlspecialchars($item['price'] ?? '') ?></span>
                                        </span>
                                    </div>
                                    <?php if (!empty($item['description'])): ?>
                                        <div class="text-sm font-body text-on-surface-variant mt-2 w-full pr-12" data-i18n-path="<?= $secKey ?>.<?= $i ?>.description">
                                            <?= htmlspecialchars($item['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </section>

        <!-- Burgers & Sandwiches -->
        <section class="py-24 bg-surface" id="mains">
            <div class="max-w-7xl mx-auto px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                    <?php 
                    $mainSections = ['sandwiches', 'burgers'];
                    foreach($mainSections as $secKey): 
                        if (isset($menu[$secKey])):
                    ?>
                    <div id="<?= $secKey ?>">
                        <h2 class="font-headline text-5xl font-black text-on-surface uppercase leading-none tracking-tighter mb-12" data-i18n-title-key="<?= $secKey ?>">
                            <?= $sectionTitles[$langKey][$secKey] ?>
                        </h2>
                        <div class="grid grid-cols-1 gap-12">
                            <?php foreach ($menu[$secKey] as $i => $item): ?>
                                <div class="border-b border-outline-variant/30 pb-6 w-full max-w-md">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-headline font-bold text-2xl uppercase tracking-tight" data-i18n-path="<?= $secKey ?>.<?= $i ?>.name">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                        </h4>
                                        <span class="font-headline font-bold text-primary text-xl whitespace-nowrap ml-4" data-i18n-path="<?= $secKey ?>.<?= $i ?>.price">
                                            <?= htmlspecialchars($item['price'] ?? '') ?>
                                        </span>
                                    </div>
                                    <p class="font-body text-sm text-on-surface-variant" data-i18n-path="<?= $secKey ?>.<?= $i ?>.description">
                                        <?= htmlspecialchars($item['description'] ?? '') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </section>

        <!-- Drinks & Wine section -->
        <section class="py-16 bg-surface-container-high" id="drinks_section">
            <div class="max-w-7xl mx-auto px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-24">
                    
                    <!-- Main drinks -->
                    <?php if (isset($menu['drinks'])): ?>
                    <div id="drinks">
                        <div class="mb-12">
                            <h2 class="font-headline text-3xl font-bold text-tertiary uppercase border-b-2 border-tertiary inline-block pb-1" data-i18n-title-key="drinks">
                                <?= $sectionTitles[$langKey]['drinks'] ?>
                            </h2>
                        </div>
                        
                        <?php 
                        $drinkSubs = ['soft_drinks', 'coffee_and_tea', 'beers'];
                        foreach ($drinkSubs as $subKey): 
                            if (isset($menu['drinks'][$subKey])):
                        ?>
                            <h3 class="font-headline text-xl font-bold uppercase mt-8 mb-4 text-secondary" data-i18n-title-key="<?= $subKey ?>">
                                <?= $sectionTitles[$langKey][$subKey] ?>
                            </h3>
                            <ul class="space-y-4 mb-8">
                                <?php foreach ($menu['drinks'][$subKey] as $i => $item): ?>
                                    <li class="flex justify-between items-baseline group">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-tertiary transition-colors" data-i18n-path="drinks.<?= $subKey ?>.<?= $i ?>.name">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                        </span>
                                        <span class="flex-grow border-b border-dotted border-outline-variant mx-4"></span>
                                        <span class="font-headline font-bold">
                                             <span data-i18n-path="drinks.<?= $subKey ?>.<?= $i ?>.price"><?= htmlspecialchars($item['price'] ?? '') ?></span>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php endif; ?>

                    <!-- Wine List -->
                    <?php if (isset($menu['wine_list'])): ?>
                    <div id="wine_list">
                         <div class="mb-12">
                             <h2 class="font-headline text-3xl font-bold text-tertiary uppercase mt-8 md:mt-0 border-b-2 border-tertiary inline-block pb-1" data-i18n-title-key="wine_list">
                                <?= $sectionTitles[$langKey]['wine_list'] ?>
                             </h2>
                         </div>
                         <ul class="space-y-6">
                             <?php foreach ($menu['wine_list'] as $i => $item): ?>
                                 <li class="flex justify-between items-baseline group mt-4">
                                    <div class="flex flex-col">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-tertiary transition-colors" data-i18n-path="wine_list.<?= $i ?>.name">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                        </span>
                                        <span class="text-xs text-on-surface-variant font-label mt-1" data-i18n-path="wine_list.<?= $i ?>.type">
                                            <?= htmlspecialchars($item['type'] ?? '') ?>
                                        </span>
                                    </div>
                                    <span class="flex-grow border-b border-dotted border-outline-variant mx-4"></span>
                                    <span class="font-headline font-bold flex flex-col items-end">
                                        <span data-i18n-path="wine_list.<?= $i ?>.price"><?= htmlspecialchars($item['price'] ?? '') ?></span>
                                    </span>
                                </li>
                             <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-[#f6f6f6] dark:bg-stone-900 w-full py-16 mt-8">
        <div class="flex flex-col items-center justify-center gap-8 w-full px-8 max-w-7xl mx-auto">
            
            <p class="font-['Work_Sans'] text-xs uppercase tracking-widest text-on-surface-variant opacity-60">
                © 2024 COME. El Ahumado Editorial.
            </p>
        </div>
    </footer>
</body>
</html>
