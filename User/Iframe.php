<?php
namespace Pickups\User; 

class Iframe {
    /**
     * Renders a button that opens a fixed-position iframe.
     * 
     * @param string $url The URL to load in the iframe.
     * @param string $top The top offset for the fixed iframe.
     * @param string $text The text for the trigger button.
     * @param string $style Custom CSS for the iframe element.
     * @param bool $reload Whether to reload the iframe each time it is opened.
     * @param bool $blur Whether to show a blur backdrop effect.
     */
    public static function render($url, $top = '80px', $text = 'Menu', $style = '', $reload = false, $blur = false) {
        $uniqueId = 'iframe_' . substr(md5($url), 0, 8);
        $reloadAttr = $reload ? 'true' : 'false';
        ?>
        <style>
            .iframe-backdrop-<?= $uniqueId ?> {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.3);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                z-index: 99998;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .iframe-backdrop-<?= $uniqueId ?>.show {
                display: block;
                opacity: 1;
            }
            .iframe-container-fixed-<?= $uniqueId ?> {
                position: fixed;
                top: <?= esc_attr($top) ?>;
                left: 0;
                width: 100%;
                height: calc(100% - <?= esc_attr($top) ?>);
                z-index: 99999;
                background: white;
                box-shadow: 0 -5px 25px rgba(0,0,0,0.1);
                transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                transform: translateY(100%);
                visibility: hidden;
                display: flex;
                background: transparent;
            }
            .iframe-container-fixed-<?= $uniqueId ?>.show {
                transform: translateY(0);
                visibility: visible;
            }
            .iframe-close-btn-<?= $uniqueId ?> {
                position: absolute;
                top: 15px;
                right: 25px;
                z-index: 100000;
                background: #e43b16;
                color: white;
                border: none;
                border-radius: 50%;
                width: 44px;
                height: 44px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                transition: transform 0.2s, background 0.2s;
            }
            .iframe-close-btn-<?= $uniqueId ?>:hover {
                transform: scale(1.1);
                background: #ff4d26;
            }
            .iframe-close-btn-<?= $uniqueId ?>:active {
                transform: scale(0.95);
            }
        </style>
        
        <script>
            function openIframe_<?= $uniqueId ?>() {
                const container = document.getElementById('container_<?= $uniqueId ?>');
                const backdrop = document.getElementById('backdrop_<?= $uniqueId ?>');
                const iframe = document.getElementById('iframe_<?= $uniqueId ?>');
                
                if (<?= $reloadAttr ?>) {
                    iframe.src = iframe.src;
                }
                
                if (backdrop) {
                    backdrop.style.display = 'block';
                    setTimeout(() => backdrop.classList.add('show'), 10);
                }
                container.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
            function closeIframe_<?= $uniqueId ?>() {
                const container = document.getElementById('container_<?= $uniqueId ?>');
                const backdrop = document.getElementById('backdrop_<?= $uniqueId ?>');
                
                container.classList.remove('show');
                if (backdrop) {
                    backdrop.classList.remove('show');
                    setTimeout(() => backdrop.style.display = 'none', 300);
                }
                document.body.style.overflow = ''; // Restore scrolling
            }
        </script>

        <button style="color: black; background: white; padding: .5rem 1.5rem; margin: 1rem; min-width: 8rem; border-radius: 31px; min-height: 2.75rem !important; font-weight: bold; cursor: pointer; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" 
                onclick="openIframe_<?= $uniqueId ?>()"><?= esc_html($text) ?></button>
        
        <?php if ($blur): ?>
        <div id="backdrop_<?= $uniqueId ?>" class="iframe-backdrop-<?= $uniqueId ?>" onclick="closeIframe_<?= $uniqueId ?>()"></div>
        <?php endif; ?>

        <div id="container_<?= $uniqueId ?>" class="iframe-container-fixed-<?= $uniqueId ?>">
            <button class="iframe-close-btn-<?= $uniqueId ?>" onclick="closeIframe_<?= $uniqueId ?>()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <iframe id="iframe_<?= $uniqueId ?>" src="<?= esc_url($url) ?>" style="width: 100%; height: 100%; border: none; <?= esc_attr($style) ?>"></iframe>
        </div>
        <?php
    }
}