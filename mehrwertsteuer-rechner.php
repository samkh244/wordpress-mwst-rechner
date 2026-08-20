<?php
/**
 * Plugin Name: Mehrwertsteuer Rechner
 * Plugin URI: https://mointools.com
 * Description: Professioneller Mehrwertsteuer-Rechner mit mehrsprachiger Unterstützung (DE, EN, FR, TR)
 * Version: 1.2.7
 * Author: Samad Khakpour
 * Author URI: https://mointools.com
 * Text Domain: mwst-rechner
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Verhindere direkten Zugriff
if (!defined('ABSPATH')) {
    exit;
}

// Plugin-Konstanten
define('MWST_RECHNER_VERSION', '1.2.7');
define('MWST_RECHNER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MWST_RECHNER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MWST_UPDATE_JSON_URL', 'https://mehrwertsteuer-rechner.xan-webdesign.com/plugin_updates/mehrwertsteuer_rechner.json');

/**
 * Hauptklasse für das Mehrwertsteuer Rechner Plugin
 */
class MWST_Rechner_Plugin {
    
    /**
     * Singleton-Instanz
     */
    private static $instance = null;
    
    /**
     * Singleton-Instanz abrufen
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Konstruktor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Hooks initialisieren
     */
    private function init_hooks() {
        // Shortcodes registrieren
        add_shortcode('mwst_rechner_de', array($this, 'render_calculator_de'));
        add_shortcode('mwst_rechner_en', array($this, 'render_calculator_en'));
        add_shortcode('mwst_rechner_fr', array($this, 'render_calculator_fr'));
        add_shortcode('mwst_rechner_tr', array($this, 'render_calculator_tr'));
        
        // Assets enqueue
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Admin-Hinweise
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * CSS und JavaScript einbinden
     */
    public function enqueue_assets() {
        // Nur laden, wenn Shortcode auf der Seite vorhanden ist
        global $post;
        if (is_a($post, 'WP_Post') && (
            has_shortcode($post->post_content, 'mwst_rechner_de') ||
            has_shortcode($post->post_content, 'mwst_rechner_en') ||
            has_shortcode($post->post_content, 'mwst_rechner_fr') ||
            has_shortcode($post->post_content, 'mwst_rechner_tr')
        )) {
            // CSS einbinden
            wp_enqueue_style(
                'mwst-rechner-style',
                MWST_RECHNER_PLUGIN_URL . 'assets/css/style.css',
                array(),
                MWST_RECHNER_VERSION
            );
            
            // JavaScript einbinden
            wp_enqueue_script(
                'mwst-rechner-script',
                MWST_RECHNER_PLUGIN_URL . 'assets/js/script.js',
                array(),
                MWST_RECHNER_VERSION,
                true
            );

            // Plugin-URL an das Script übergeben (für lokales jsPDF + PDF-Schrift)
            wp_localize_script('mwst-rechner-script', 'mwstConfig', array(
                'pluginUrl' => MWST_RECHNER_PLUGIN_URL,
            ));
        }
    }
    
    /**
     * Rechner-HTML generieren
     */
    private function get_calculator_html($default_lang = 'de') {
        ob_start();
        ?>

        
        <div id="rechner_container">
    

      <div id="mwst-rechner" class="mwst-rechner" data-default-lang="<?php echo esc_attr($default_lang); ?>" aria-live="polite">
   
    
    <!-- Sprachwahl -->
    <div class="language-row">
  
                <div class="language-buttons" role="tablist" aria-label="Sprachen">
                    <button class="lang-btn" data-lang="de" aria-pressed="<?php echo $default_lang === 'de' ? 'true' : 'false'; ?>" title="Deutsch">
                        <img src="<?php echo esc_url(MWST_RECHNER_PLUGIN_URL . 'assets/img/flagsde.svg'); ?>" alt="Deutsch" width="20"/>
                    </button>
                    <button class="lang-btn" data-lang="en" aria-pressed="<?php echo $default_lang === 'en' ? 'true' : 'false'; ?>" title="English">
                        <img src="<?php echo esc_url(MWST_RECHNER_PLUGIN_URL . 'assets/img/flagsen.svg'); ?>" alt="English" width="20"/>
                    </button>
                    <button class="lang-btn" data-lang="fr" aria-pressed="<?php echo $default_lang === 'fr' ? 'true' : 'false'; ?>" title="Français">
                        <img src="<?php echo esc_url(MWST_RECHNER_PLUGIN_URL . 'assets/img/flagsfr.svg'); ?>" alt="Français" width="20"/>
                    </button>
					 <button class="lang-btn" data-lang="tr" aria-pressed="<?php echo $default_lang === 'tr' ? 'true' : 'false'; ?>" title="Türkçe">
                    <img src="<?php echo esc_url(MWST_RECHNER_PLUGIN_URL . 'assets/img/flagstr.svg'); ?>" alt="Türkçe" width="20"/>

                   </button>
                </div>
            </div>

    <!-- Eingaben -->
    <div class="group floating-label-group"> 
    <input type="number" id="netto" autocomplete="off" step="any"> 
    <label for="netto" class="floating-label">
        <?php esc_html_e('Netto', 'mwst-rechner'); ?> *
    </label> 
</div>
<div class="group floating-label-group">
    <input type="number" id="brutto" autocomplete="off" step="any">
    <label for="brutto" class="floating-label">
        <?php esc_html_e('Brutto', 'mwst-rechner'); ?> *
    </label>
</div>

    <div class="group">
      <select id="mwstSelect" aria-label="Mehrwertsteuersatz">
        <option value="19" selected>19 % (DE)</option>
        <option value="7">7 % (DE)</option>
        <option value="7.7">7.7 % (CH)</option>
        <option value="8.1">8.1 % (CH)</option>
        <option value="2.6">2.6 % (CH)</option>
        <option value="3.8">3.8 % (CH)</option>
        <option value="20">20 % (AT)</option>
        <option value="custom" data-label="customRate">Individuell</option> 
      </select>
    </div>

    <div class="group" id="customRateGroup" style="display: none;">
    <input type="number" id="customRate" step="any" placeholder="z.B. 7,5">
    <label for="customRate">
        <?php esc_html_e('Eigener Satz (%)', 'mwst-rechner'); ?>
    </label>
</div>



    <!-- Ergebnisanzeige -->

   <div class="group">
   <button id="berechnenBtn" class="primary" type="button" data-label="berechnen">
    <?php esc_html_e('Berechnen', 'mwst-rechner'); ?>
   </button>
</div>



<!-- Ergebnisanzeige -->
<div class="ergebnis-box" id="ergebnis-box">
  <h3 data-label="ergebnis">Ergebnis</h3> 
  
  <div class="ergebnis-row">
    <span data-label="netto">Netto:</span>
    <span class="ergebnis-netto" id="ergebnisNetto">0,00 €</span>
  </div>
      
 <div class="ergebnis-row">
    <span class="ergebnis-label-group">
        <span data-label="mwst">MwSt: </span>
        <span>(<span id="mwstSatzText">19,00%</span>):</span>
    </span>
    <span class="ergebnis-mwst" id="ergebnisMwst">0,00 €</span>
  </div>
  
  <div class="ergebnis-row">
    <span data-label="brutto">Brutto:</span>
    <span class="ergebnis-brutto" id="ergebnisBrutto">0,00 €</span>
  </div>
</div>


    
    <!-- Verlauf -->
    <div class="verlauf-container">
      <div class="verlauf-title" data-label="calculations">Letzte Berechnungen</div>

      <div id="verlauf" role="region" aria-label="Verlauf"></div>

      <div class="verlauf-footer">
        <button id="kopierenBtn" class="btn-copy" data-title="Alle Daten kopieren"><span class="btn-label">Kopieren</span></button>
        <button id="csvBtn" class="btn-csv" data-title="CSV herunterladen"><span class="btn-label">CSV Export</span></button>
        <button id="pdfBtn" class="btn-pdf" data-title="PDF speichern"><span class="btn-label">PDF Export</span></button>
        <button id="loeschenBtn" class="btn-delete" data-title="Verlauf löschen"><span class="btn-label">Verlauf leeren</span></button>
      </div>
    </div>
  </div>
  </div>
    
        <?php
        return ob_get_clean();
    }
    
    /**
     * Shortcode für Deutsche Sprache
     */
    public function render_calculator_de($atts) {
        return $this->get_calculator_html('de');
    }
    
    /**
     * Shortcode für Englische Sprache
     */
    public function render_calculator_en($atts) {
        return $this->get_calculator_html('en');
    }
    
    /**
     * Shortcode für Französische Sprache
     */
    public function render_calculator_fr($atts) {
        return $this->get_calculator_html('fr');
    }

    /**
     * Shortcode für Türkische Sprache
     */
    public function render_calculator_tr($atts) {
        return $this->get_calculator_html('tr');
    }
    
    /**
     * Admin-Hinweise anzeigen
     */
    public function admin_notices() {
        // Optionale Hinweise für Admins
    }
}

/**
 * Statistik-Modul laden
 */
require_once MWST_RECHNER_PLUGIN_DIR . 'includes/class-statistics.php';  

/**
 * Plugin initialisieren
 */
function mwst_rechner_init() {
    return MWST_Rechner_Plugin::get_instance();
}

// Plugin starten
add_action('plugins_loaded', 'mwst_rechner_init');

/**
 * Update-System initialisieren (Plugin Update Checker von Yahnis Elsts)
 *
 * Die Bibliothek muss im Ordner liegen:
 *   includes/plugin-update-checker/plugin-update-checker.php
 * (Den heruntergeladenen Ordner "plugin-update-checker-master"
 *  einfach in "plugin-update-checker" umbenennen.)
 */
function mwst_rechner_init_updater() {
    // Beide möglichen Ordnernamen unterstützen
    $puc_paths = array(
        MWST_RECHNER_PLUGIN_DIR . 'includes/plugin-update-checker/plugin-update-checker.php',
        MWST_RECHNER_PLUGIN_DIR . 'includes/plugin-update-checker-master/plugin-update-checker.php',
    );

    $puc_file = '';
    foreach ($puc_paths as $path) {
        if (file_exists($path)) {
            $puc_file = $path;
            break;
        }
    }

    // Bibliothek nicht gefunden? Dann ohne Update-Checker weiterlaufen.
    if (empty($puc_file)) {
        return;
    }

    require_once $puc_file;

    // VARIANTE A (aktiv): JSON-Datei auf dem eigenen Server
    $update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        MWST_UPDATE_JSON_URL,
        __FILE__,
        'mehrwertsteuer_rechner' // Muss dem Plugin-Ordnernamen entsprechen!
    );

    // Den Link "Nach Updates suchen" in der Plugin-Liste ausblenden
    // (leerer String = Link wird nicht angezeigt)
    add_filter('puc_manual_check_link-mehrwertsteuer_rechner', '__return_empty_string');

    /*
    // VARIANTE B (alternativ): Direkt von GitHub-Releases aktualisieren.
    // Dazu Variante A auskommentieren und diese hier aktivieren:
    $update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/DEIN-GITHUB-BENUTZERNAME/mehrwertsteuer-rechner/',
        __FILE__,
        'mehrwertsteuer_rechner'
    );
    // Nur stabile Releases verwenden und das angehängte ZIP-Asset nutzen:
    $update_checker->getVcsApi()->enableReleaseAssets();
    */
}
add_action('init', 'mwst_rechner_init_updater');

/**
 * Autor-Link in der Plugin-Liste in neuem Tab öffnen
 *
 * WordPress bietet dafür keinen eigenen Filter, daher fügt dieses kleine
 * Script auf der Plugin-Seite target="_blank" zu den mointools.com-Links
 * in der Zeile dieses Plugins hinzu.
 */
function mwst_rechner_author_link_new_tab() {
    $plugin_file = plugin_basename(__FILE__);
    ?>
    <script>
    document.querySelectorAll('tr[data-plugin="<?php echo esc_js($plugin_file); ?>"] a[href*="mointools.com"]').forEach(function (link) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener');
    });
    </script>
    <?php
}
add_action('admin_footer-plugins.php', 'mwst_rechner_author_link_new_tab');

/**
 * Aktivierungs-Hook
 */
register_activation_hook(__FILE__, function() {
    // Flush rewrite rules (falls nötig)
    flush_rewrite_rules();
});

/**
 * Deaktivierungs-Hook
 */
register_deactivation_hook(__FILE__, function() {
    // Aufräumen (optional)
    flush_rewrite_rules();
});