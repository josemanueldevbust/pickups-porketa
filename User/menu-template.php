<?php
use Pickups\Model\Product;
use Pickups\Helpers\Data;

$products = Data::fetch_items(Product::class);

// Sort products by order
usort($products, function($a, $b) {
    $orderA = (int)$a->getProduct_order();
    $orderB = (int)$b->getProduct_order();
    if ($orderA === $orderB) return 0;
    return ($orderA < $orderB) ? -1 : 1;
});

$menus = ['spanish' => [], 'english' => [], 'italian' => [], 'ca' => [], 'french' => []];

$allergensLegend = get_option('pickups_allergen_meta', []);
if (empty($allergensLegend)) {
    $allergensLegend = [
        'C' => ['spanish' => 'Gluten', 'english' => 'Gluten', 'ca' => 'Gluten', 'italian' => 'Glutine', 'french' => 'Gluten'],
        'O' => ['spanish' => 'Soja', 'english' => 'Soy', 'ca' => 'Soja', 'italian' => 'Soia', 'french' => 'Soja'],
        'M' => ['spanish' => 'Leche', 'english' => 'Milk', 'ca' => 'Llet', 'italian' => 'Latte', 'french' => 'Lait'],
        'E' => ['spanish' => 'Mostaza', 'english' => 'Mustard', 'ca' => 'Mostassa', 'italian' => 'Senape', 'french' => 'Moutarde'],
        'P' => ['spanish' => 'Huevo', 'english' => 'Egg', 'ca' => 'Ou', 'italian' => 'Uovo', 'french' => 'Œuf'],
        'R' => ['spanish' => 'Frutos de cascara', 'english' => 'Nuts', 'ca' => 'Fruit de closca', 'italian' => 'Frutta a guscio', 'french' => 'Fruits à coque'],
        'T' => ['spanish' => 'Sesamo', 'english' => 'Sesame', 'ca' => 'Sèsam', 'italian' => 'Sesamo', 'french' => 'Sésame']
    ];
}

foreach ($products as $p) {
    if (!$p) continue;
    $cat = $p->getCategory();
    $sub = $p->getSubcategory();
    if(empty($cat)) continue;

    $item_es = ['name' => $p->getName_es(), 'description' => $p->getDesc_es(), 'price' => $p->getPrice(), 'type' => $p->getType_es(), 'image'=> $p->getImage(), 'allergens' => $p->getAllergens()];
    $item_en = ['name' => $p->getName_en(), 'description' => $p->getDesc_en(), 'price' => $p->getPrice(), 'type' => $p->getType_en(), 'image'=> $p->getImage(), 'allergens' => $p->getAllergens()];
    $item_it = ['name' => $p->getName_it(), 'description' => $p->getDesc_it(), 'price' => $p->getPrice(), 'type' => $p->getType_it(), 'image'=> $p->getImage(), 'allergens' => $p->getAllergens()];
    $item_ca = ['name' => $p->getName_ca(), 'description' => $p->getDesc_ca(), 'price' => $p->getPrice(), 'type' => $p->getType_ca(), 'image'=> $p->getImage(), 'allergens' => $p->getAllergens()];
    $item_fr = ['name' => $p->getName_fr(), 'description' => $p->getDesc_fr(), 'price' => $p->getPrice(), 'type' => $p->getType_fr(), 'image'=> $p->getImage(), 'allergens' => $p->getAllergens()];

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
$langKey = $_GET['lang'] ?? $defaultLang ?? 'spanish';
if (!in_array($langKey, ['spanish', 'english', 'italian', 'ca', 'french'])) $langKey = $defaultLang ?? 'spanish';
if (!in_array($langKey, ['spanish', 'english', 'italian', 'ca', 'french'])) $langKey = 'spanish';
$menu = $data['menus'][$langKey] ?? [];

// Helper to safely get nested array values
if(!function_exists('get_val2')){

function get_val2($array, $path, $default = '') {
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
        'wine_list' => 'Vinos', // fixed from Vins ? user had Vins earlier but let's stick to what's there or fix it.
        'soft_drinks' => 'Boissons Sans Alcool',
        'coffee_and_tea' => 'Café et Thé',
        'beers' => 'Bières'
    ]
];

// Reorder categories based on stored meta
$cat_meta = get_option('pickups_category_meta', []);
$category_order = array_keys($sectionTitles[$langKey]);
// Remove subcategories from main order if they exist
$category_order = array_diff($category_order, ['soft_drinks', 'coffee_and_tea', 'beers']);

usort($category_order, function($a, $b) use ($cat_meta) {
    $orderA = (int)($cat_meta[$a]['order'] ?? 0);
    $orderB = (int)($cat_meta[$b]['order'] ?? 0);
    return $orderA <=> $orderB;
});
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
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 160px; /* Slightly more to be safe */
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .editorial-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .nav-link.active-nav-link {
            color: #ffffff !important;
            border-bottom-color: #ffffff;
            opacity: 1;
            font-weight: 800;
        }

        .nav-link {
            transition: all 0.3s ease;
            opacity: 0.8;
            color: #f3f3f3;
        }

        .nav-link:hover {
            opacity: 1;
            color: #ffffff;
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

<body class="bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container pt-24 lg:pt-2">
    
    <a href="#" id="back-to-side" onclick="backToSite_<?= $uid ?>(event)"
        class="fixed top-8 mt-5 md:mt-0 z-50 right-8 z-[60] bg-[#e43b16] text-[#f6f6f6] w-12 h-12 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300">
        <span class="material-symbols-outlined">close</span>
    </a>
    
    <!-- JSON DATA FOR CLIENT SIDE SWITCHING -->
    <script>
        window.backToSite_<?= $uid ?> = function(event) {
            event.preventDefault();
            window.dispatchEvent(new Event('close-all'));
        }

        const menuData_<?= $uid ?> = <?= $jsonData ?>;
        const sectionTitles_<?= $uid ?> = <?= json_encode($sectionTitles) ?>;
        const allergensLegend_<?= $uid ?> = <?= json_encode($allergensLegend) ?>;
        const categoryMeta_<?= $uid ?> = <?= json_encode($cat_meta) ?>;
        let currentLang_<?= $uid ?> = '<?= $langKey ?>';
        
        window.changeLanguage_<?= $uid ?> = function(langKey) {
            currentLang_<?= $uid ?> = langKey;
            const selectedMenu = menuData_<?= $uid ?>.menus[langKey];
            if (!selectedMenu) return;

            // Update category descriptions
            document.querySelectorAll('.category-description').forEach(el => {
                const catKey = el.getAttribute('data-i18n-cat-desc');
                if (categoryMeta_<?= $uid ?>[catKey] && categoryMeta_<?= $uid ?>[catKey].description) {
                    const desc = categoryMeta_<?= $uid ?>[catKey].description[langKey] || categoryMeta_<?= $uid ?>[catKey].description['spanish'] || '';
                    el.innerText = desc;
                }
            });

            // Update allergens legend in footer
            const legendContainer = document.getElementById('allergens-legend-container');
            if (legendContainer) {
                legendContainer.innerHTML = '';
                for (const code in allergensLegend_<?= $uid ?>) {
                    const name = allergensLegend_<?= $uid ?>[code][langKey] || allergensLegend_<?= $uid ?>[code]['spanish'] || '';
                    const item = document.createElement('div');
                    item.className = 'flex items-center gap-1.5';
                    item.innerHTML = `<span class="bg-on-surface/10 px-1 rounded">${code}</span><span>${name}</span>`;
                    legendContainer.appendChild(item);
                }
            }

            // Update allergens modal content if open (or just for next time)
            const modalList = document.getElementById('allergen-modal-list');
            if (modalList) {
                modalList.innerHTML = '';
                for (const code in allergensLegend_<?= $uid ?>) {
                    const name = allergensLegend_<?= $uid ?>[code][langKey] || allergensLegend_<?= $uid ?>[code]['spanish'] || '';
                    const item = document.createElement('div');
                    item.id = `modal-allergen-${code}`;
                    item.className = 'flex items-center gap-3 transition-all duration-300 opacity-40';
                    item.innerHTML = `<span class="bg-on-surface/10 px-2 py-0.5 rounded font-bold text-sm">${code}</span><span class="font-body text-sm font-medium">${name}</span>`;
                    modalList.appendChild(item);
                }
            }

            // Update modal UI labels
            const labels = {
                spanish: { title: 'Alergenos', close: 'Entendido' },
                english: { title: 'Allergens', close: 'Got it' },
                ca: { title: 'Al·lèrgens', close: 'Entès' },
                italian: { title: 'Allergeni', close: 'Ho capito' },
                french: { title: 'Allergènes', close: 'Compris' }
            };
            const l = labels[langKey] || labels.spanish;
            const modalTitle = document.querySelector('#allergen-modal h3');
            const modalBtn = document.querySelector('#allergen-modal button.bg-primary');
            if (modalTitle) modalTitle.innerText = l.title;
            if (modalBtn) modalBtn.innerText = l.close;
            
            const legendTitle = document.querySelector('#legend-title');
            if (legendTitle) legendTitle.innerText = `${labels.spanish.title} / ${labels.english.title}`;

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
                
                // Special handling for title with allergens if present
                const allergensAttr = el.getAttribute('data-i18n-allergens-path');
                if (allergensAttr) {
                    const aKeys = allergensAttr.split('.');
                    let aVal = selectedMenu;
                    for (let k of aKeys) {
                        if (aVal && aVal[k] !== undefined) {
                            aVal = aVal[k];
                        } else {
                            aVal = null;
                            break;
                        }
                    }
                    if (aVal) {
                        const span = document.createElement('span');
                        span.className = 'text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2';
                        span.innerText = `(${aVal})`;
                        span.onclick = (e) => {
                            e.stopPropagation();
                            showAllergenModal_<?= $uid ?>(aVal);
                        };
                        el.appendChild(span);
                    }
                }
            });

            document.querySelectorAll('[data-i18n-title-key]').forEach(el => {
                const key = el.getAttribute('data-i18n-title-key');
                if (sectionTitles_<?= $uid ?>[langKey] && sectionTitles_<?= $uid ?>[langKey][key]) {
                    el.innerText = sectionTitles_<?= $uid ?>[langKey][key];
                }
            });
        }
        
        // Wrap everything in a function to be more robust
        function initMenu_<?= $uid ?>() {
            const sections = document.querySelectorAll('section[id], div[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            if (!sections.length || !navLinks.length) {
                // Try again in a moment if not ready, especially for iframes
                setTimeout(initMenu_<?= $uid ?>, 100);
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
                        navLinks.forEach(link => {
                            link.classList.remove('active-nav-link');
                            if (link.getAttribute('href') === '#' + id) {
                                link.classList.add('active-nav-link');
                                // Scroll active link into view in the horizontal navbar (both desktop and mobile)
                                if (link.offsetParent !== null && !link.closest('#mobile-drawer')) {
                                    link.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                                }
                            }
                        });
                    }
                });
            }, observerOptions);

            sections.forEach(section => {
                if (Array.from(navLinks).some(link => link.getAttribute('href') === '#' + section.getAttribute('id'))) {
                    observer.observe(section);
                }
            });

         }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu_<?= $uid ?>);
        } else {
            initMenu_<?= $uid ?>();
        }
    </script>

    <!-- TopAppBar -->
    <header class="fixed top-0 w-full z-50 bg-[#E43B16] border-none pt-8">
        <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto">
            <div class="text-3xl font-black text-[#2d2f2f] dark:text-[#f6f6f6] uppercase tracking-tighter font-headline flex items-center">
                <img src="/wp-content/plugins/pickups/logo.png" alt="COME Logo" class="h-10 object-contain invert dark:invert-0" />
            </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex flex-1 items-center justify-center gap-6 font-headline font-bold text-sm tracking-tight px-4 no-scrollbar overflow-x-auto">
                <?php foreach($category_order as $secKey): 
                    if (!isset($menu[$secKey]) && $secKey !== 'drinks' && $secKey !== 'wine_list') continue;
                ?>
                    <a class="nav-link text-[#f3f3f3ff] whitespace-nowrap border-b-2 border-transparent pb-0.5" href="#<?= $secKey ?>" data-i18n-title-key="<?= $secKey ?>"><?= $sectionTitles[$langKey][$secKey] ?></a>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center gap-4 mr-14 md:mr-0">
                <div class="relative inline-block">
                    <select onchange="changeLanguage_<?= $uid ?>(this.value)" class="appearance-none bg-[#E43B16] text-white font-headline font-bold text-sm uppercase pl-4 pr-10 py-1.5 border-2 border-white focus:border-stone-300 rounded-full cursor-pointer focus:outline-none transition-all">
                        <option value="spanish" <?= $langKey === 'spanish' ? 'selected' : '' ?>>ES</option>
                        <option value="ca" <?= $langKey === 'ca' ? 'selected' : '' ?>>CA</option>
                        <option value="french" <?= $langKey === 'french' ? 'selected' : '' ?>>FR</option>
                        <option value="english" <?= $langKey === 'english' ? 'selected' : '' ?>>EN</option>
                        <option value="italian" <?= $langKey === 'italian' ? 'selected' : '' ?>>IT</option>
                    </select>
                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-white">
                        <span class="material-symbols-outlined text-xl">expand_more</span>
                    </div>
                </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Horizontal Category Slider -->
        <div class="flex md:hidden overflow-x-auto whitespace-nowrap px-8 pb-4 gap-6 no-scrollbar font-headline font-bold text-sm tracking-tight border-t border-white/10 pt-4">
            <?php foreach($category_order as $secKey): 
                if (!isset($menu[$secKey]) && $secKey !== 'drinks' && $secKey !== 'wine_list') continue;
            ?>
                <a class="nav-link text-[#f3f3f3ff] whitespace-nowrap border-b-2 border-transparent pb-0.5" href="#<?= $secKey ?>" data-i18n-title-key="<?= $secKey ?>"><?= $sectionTitles[$langKey][$secKey] ?></a>
            <?php endforeach; ?>
        </div>
    </header>

    </div>

    <main class="pt-24 md:pt-32">

        <?php 
        $renderedSections = [];
        foreach($category_order as $secKey): 
            if (!isset($menu[$secKey]) && $secKey !== 'drinks' && $secKey !== 'wine_list') continue;
            if (in_array($secKey, $renderedSections)) continue;

            $catImage = $cat_meta[$secKey]['image'] ?? '';
            $bgColor = count($renderedSections) % 2 == 0 ? 'bg-surface-container-low' : 'bg-surface-container-high';
            if ($secKey === 'sandwiches' || $secKey === 'burgers') $bgColor = 'bg-surface';
        ?>

            <!-- Category Section: <?= $secKey ?> -->
            <section class="py-16 <?= $bgColor ?>" id="<?= $secKey ?>">
                <div class="max-w-7xl mx-auto px-8">
                    
                    <?php if (!empty($catImage)): ?>
                        <div class="w-full h-64 md:h-96 mb-12 overflow-hidden rounded-3xl shadow-lg">
                            <img src="<?= esc_url($catImage) ?>" alt="<?= esc_attr($secKey) ?>" class="w-full h-full object-cover" />
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-baseline mb-4 border-b border-outline-variant/20 pb-4">
                        <h2 class="font-headline text-4xl font-bold text-primary uppercase tracking-tight" data-i18n-title-key="<?= $secKey ?>"><?= $sectionTitles[$langKey][$secKey] ?></h2>
                        <?php if($secKey === 'specialty'): ?>
                            <span class="font-label text-on-surface-variant text-sm tracking-widest hidden md:inline">ARTISANAL CURATION</span>
                        <?php endif; ?>
                    </div>

                    <?php 
                    $catDesc = $cat_meta[$secKey]['description'][$langKey] ?? $cat_meta[$secKey]['description']['spanish'] ?? '';
                    ?>
                    <div class="mb-8 text-on-surface-variant font-body max-w-2xl category-description" data-i18n-cat-desc="<?= $secKey ?>">
                        <?= htmlspecialchars($catDesc) ?>
                    </div>

                    <?php if ($secKey === 'specialty'): ?>
                        <div class="mb-10 text-on-surface-variant font-body max-w-2xl" data-i18n-path="specialty.description">
                            <?= htmlspecialchars(get_val2($menu, 'specialty.description')) ?>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                            <?php if (isset($menu['specialty']['types'])): ?>
                                <?php foreach ($menu['specialty']['types'] as $index => $item): ?>
                                    <div class="group">
                                        <div class="h-64 bg-surface-container-highest mb-8 overflow-hidden rounded-2xl shadow-sm">
                                            <img alt="Specialty" class="w-full h-full object-cover scale-100 group-hover:scale-105 transition-transform duration-500" src="<?= $item['image'] ?>" />
                                        </div>
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-headline font-bold text-xl uppercase tracking-tighter" data-i18n-path="specialty.types.<?= $index ?>.name" data-i18n-allergens-path="specialty.types.<?= $index ?>.allergens">
                                                <?= htmlspecialchars($item['name'] ?? '') ?>
                                                <?php if(!empty($item['allergens'])): ?>
                                                    <span class="text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2" onclick="showAllergenModal_<?= $uid ?>('<?= esc_attr($item['allergens']) ?>')">(<?= htmlspecialchars($item['allergens']) ?>)</span>
                                                <?php endif; ?>
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

                    <?php elseif (in_array($secKey, ['to_share', 'salads', 'desserts'])): ?>
                        <ul class="space-y-6 max-w-3xl">
                            <?php foreach ($menu[$secKey] as $i => $item): ?>
                                <li class="flex flex-col group">
                                    <div class="flex justify-between items-baseline w-full">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-primary transition-colors" data-i18n-path="<?= $secKey ?>.<?= $i ?>.name" data-i18n-allergens-path="<?= $secKey ?>.<?= $i ?>.allergens">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                            <?php if(!empty($item['allergens'])): ?>
                                                <span class="text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2" onclick="showAllergenModal_<?= $uid ?>('<?= esc_attr($item['allergens']) ?>')">(<?= htmlspecialchars($item['allergens']) ?>)</span>
                                            <?php endif; ?>
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

                    <?php elseif ($secKey === 'sandwiches' || $secKey === 'burgers'): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <?php foreach ($menu[$secKey] as $i => $item): ?>
                                <div class="border-b border-outline-variant/30 pb-6 w-full max-w-md">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-headline font-bold text-2xl uppercase tracking-tight" data-i18n-path="<?= $secKey ?>.<?= $i ?>.name" data-i18n-allergens-path="<?= $secKey ?>.<?= $i ?>.allergens">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                            <?php if(!empty($item['allergens'])): ?>
                                                <span class="text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2" onclick="showAllergenModal_<?= $uid ?>('<?= esc_attr($item['allergens']) ?>')">(<?= htmlspecialchars($item['allergens']) ?>)</span>
                                            <?php endif; ?>
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

                    <?php elseif ($secKey === 'drinks'): ?>
                        <?php 
                        $drinkSubs = ['soft_drinks', 'coffee_and_tea', 'beers'];
                        foreach ($drinkSubs as $subKey): 
                            if (isset($menu['drinks'][$subKey])):
                        ?>
                            <h3 class="font-headline text-xl font-bold uppercase mt-8 mb-4 text-secondary" data-i18n-title-key="<?= $subKey ?>">
                                <?= $sectionTitles[$langKey][$subKey] ?>
                            </h3>
                            <ul class="space-y-4 mb-8 max-w-2xl">
                                <?php foreach ($menu['drinks'][$subKey] as $i => $item): ?>
                                    <li class="flex justify-between items-baseline group">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-tertiary transition-colors" data-i18n-path="drinks.<?= $subKey ?>.<?= $i ?>.name" data-i18n-allergens-path="drinks.<?= $subKey ?>.<?= $i ?>.allergens">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                            <?php if(!empty($item['allergens'])): ?>
                                                <span class="text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2" onclick="showAllergenModal_<?= $uid ?>('<?= esc_attr($item['allergens']) ?>')">(<?= htmlspecialchars($item['allergens']) ?>)</span>
                                            <?php endif; ?>
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

                    <?php elseif ($secKey === 'wine_list'): ?>
                         <ul class="space-y-6 max-w-2xl">
                             <?php foreach ($menu['wine_list'] as $i => $item): ?>
                                 <li class="flex justify-between items-baseline group mt-4">
                                    <div class="flex flex-col">
                                        <span class="font-body font-bold uppercase tracking-tight group-hover:text-tertiary transition-colors" data-i18n-path="wine_list.<?= $i ?>.name" data-i18n-allergens-path="wine_list.<?= $i ?>.allergens">
                                            <?= htmlspecialchars($item['name'] ?? '') ?>
                                            <?php if(!empty($item['allergens'])): ?>
                                                <span class="text-xs text-primary ml-1 italic font-normal uppercase cursor-pointer hover:underline underline-offset-2" onclick="showAllergenModal_<?= $uid ?>('<?= esc_attr($item['allergens']) ?>')">(<?= htmlspecialchars($item['allergens']) ?>)</span>
                                            <?php endif; ?>
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
                    <?php endif; ?>

                </div>
            </section>
        <?php 
            $renderedSections[] = $secKey;
        endforeach; 
        ?>

    </main>

    <!-- Footer -->
    <footer class="bg-[#f6f6f6] dark:bg-stone-900 w-full py-16 mt-8">
        <div class="flex flex-col items-center justify-center gap-8 w-full px-8 max-w-7xl mx-auto">
            
            <p class="font-['Work_Sans'] text-xs uppercase tracking-widest text-on-surface-variant opacity-60">
                © COMEPORKETA – 2026
            </p>

            <!-- Allergens Legend -->
            <div class="mt-8 pt-8 border-t border-outline-variant/20 w-full text-center">
                <h3 id="legend-title" class="font-headline font-bold text-sm uppercase tracking-widest mb-4 opacity-70">Alergenos / Allergens</h3>
                <div id="allergens-legend-container" class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-[10px] uppercase font-bold opacity-50 px-4">
                    <?php foreach ($allergensLegend as $code => $langs): ?>
                        <div class="flex items-center gap-1.5">
                            <span class="bg-on-surface/10 px-1 rounded"><?= esc_html($code) ?></span>
                            <span><?= esc_html($langs[$langKey] ?? $langs['spanish'] ?? '') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>


    <!-- Allergen Modal -->
    <div id="allergen-modal" class="fixed inset-0 z-[150] bg-black/60 backdrop-blur-md transition-opacity duration-300 opacity-0 pointer-events-none flex items-center justify-center p-6">
        <div id="allergen-modal-content" class="bg-surface-container-low max-w-lg w-full rounded-2xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col">
            <div class="px-8 py-6 border-b border-outline-variant/20 flex justify-between items-center bg-surface-container">
                <h3 class="font-headline font-bold text-xl uppercase tracking-widest text-primary">Alergenos / Allergens</h3>
                <button onclick="closeAllergenModal_<?= $uid ?>()" class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>
            <div class="p-8 overflow-y-auto max-h-[70vh]">
                <div id="allergen-modal-list" class="grid grid-cols-2 gap-x-8 gap-y-4">
                    <?php foreach ($allergensLegend as $code => $langs): ?>
                        <div id="modal-allergen-<?= esc_attr($code) ?>" class="flex items-center gap-3 transition-all duration-300 opacity-40">
                            <span class="bg-on-surface/10 px-2 py-0.5 rounded font-bold text-sm"><?= esc_html($code) ?></span>
                            <span class="font-body text-sm font-medium"><?= esc_html($langs[$langKey] ?? $langs['spanish'] ?? '') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="px-8 py-6 bg-surface-container border-t border-outline-variant/20 text-center">
                <button onclick="closeAllergenModal_<?= $uid ?>()" class="bg-primary text-white px-8 py-2 rounded-full font-bold uppercase tracking-wider text-sm hover:scale-105 active:scale-95 transition-all">Entendido / Got it</button>
            </div>
        </div>
    </div>

    <script>
        window.showAllergenModal_<?= $uid ?> = function(codesString) {
            const modal = document.getElementById('allergen-modal');
            const content = document.getElementById('allergen-modal-content');
            const codes = codesString.split(',').map(c => c.trim().toUpperCase());

            // Reset all
            document.querySelectorAll('[id^="modal-allergen-"]').forEach(el => {
                el.classList.remove('opacity-100', 'text-primary', 'scale-105');
                el.classList.add('opacity-40');
            });

            // Highlight selected
            codes.forEach(code => {
                const el = document.getElementById(`modal-allergen-${code}`);
                if (el) {
                    el.classList.remove('opacity-40');
                    el.classList.add('opacity-100', 'text-primary', 'scale-105');
                }
            });

            // Show modal
            modal.classList.remove('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-95');
        }

        window.closeAllergenModal_<?= $uid ?> = function() {
            const modal = document.getElementById('allergen-modal');
            const content = document.getElementById('allergen-modal-content');
            modal.classList.add('opacity-0', 'pointer-events-none');
            content.classList.add('scale-95');
        }

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') window.closeAllergenModal_<?= $uid ?>();
        });

        // Close on outside click
        document.getElementById('allergen-modal').addEventListener('click', (e) => {
            if (e.target.id === 'allergen-modal') window.closeAllergenModal_<?= $uid ?>();
        });
    </script>
</body>
</html>
