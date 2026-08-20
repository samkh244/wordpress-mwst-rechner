<?php
/**
 * Statistik-Modul für Mehrwertsteuer Rechner
 * Zeigt Nutzungsstatistiken im WordPress Admin-Dashboard
 * 
 * @package Mehrwertsteuer_Rechner
 * @version 1.2.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class MWST_Rechner_Statistics {
    
    /**
     * Option-Name für Statistiken
     */
    const OPTION_NAME = 'mwst_rechner_stats';
    
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
        // Admin-Menü hinzufügen
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // AJAX-Handler für Statistik-Tracking
        add_action('wp_ajax_mwst_track_calculation', array($this, 'track_calculation'));
        add_action('wp_ajax_nopriv_mwst_track_calculation', array($this, 'track_calculation'));
        
        // JavaScript für Tracking einbinden - WICHTIG: Priorität 20 (nach dem Script!)
        add_action('wp_enqueue_scripts', array($this, 'enqueue_tracking_script'), 20);
        
        // Dashboard Widget
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }
    
    /**
     * Admin-Menü hinzufügen
     */
    public function add_admin_menu() {
        add_menu_page(
            'MwSt Rechner Statistik',           // Seitentitel
            'MwSt Statistik',                   // Menütitel
            'manage_options',                   // Berechtigung
            'mwst-rechner-stats',               // Slug
            array($this, 'render_stats_page'),  // Callback
            'dashicons-chart-line',             // Icon
            80                                  // Position
        );
    }
    
    /**
     * Tracking-Script einbinden
     */
    public function enqueue_tracking_script() {
        global $post;
        
        // Nur laden, wenn Shortcode auf der Seite vorhanden ist
        if (is_a($post, 'WP_Post') && (
            has_shortcode($post->post_content, 'mwst_rechner_de') ||
            has_shortcode($post->post_content, 'mwst_rechner_en') ||
            has_shortcode($post->post_content, 'mwst_rechner_fr') ||
            has_shortcode($post->post_content, 'mwst_rechner_tr')
        )) {
            // Prüfe ob das Script bereits geladen wurde
            if (wp_script_is('mwst-rechner-script', 'enqueued')) {
                wp_localize_script('mwst-rechner-script', 'mwstAjax', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('mwst_tracking_nonce')
                ));
            }
        }
    }
    
    /**
     * AJAX: Berechnung tracken
     */
    public function track_calculation() {
        // Sicherheitsprüfung
        check_ajax_referer('mwst_tracking_nonce', 'nonce');
        
        // Daten holen
        $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : array();
        
        if (empty($data)) {
            wp_send_json_error('Keine Daten erhalten');
            return;
        }
        
        // Statistik aktualisieren
        $this->increment_calculation_count($data);
        
        wp_send_json_success('Statistik aktualisiert');
    }
    
    /**
     * Berechnung zählen
     */
    private function increment_calculation_count($data) {
        $stats = get_option(self::OPTION_NAME, $this->get_default_stats());
        
        // Gesamtzahl erhöhen
        $stats['total_calculations']++;
        
        // Heutiges Datum
        $today = date('Y-m-d');
        
        // Tagesstatistik
        if (!isset($stats['daily'][$today])) {
            $stats['daily'][$today] = 0;
        }
        $stats['daily'][$today]++;
        
        // Nur letzte 30 Tage behalten
        if (count($stats['daily']) > 30) {
            $stats['daily'] = array_slice($stats['daily'], -30, null, true);
        }
        
        // Sprache zählen
        $lang = isset($data['lang']) ? sanitize_text_field($data['lang']) : 'de';
        if (!isset($stats['by_language'][$lang])) {
            $stats['by_language'][$lang] = 0;
        }
        $stats['by_language'][$lang]++;
        
        // Steuersatz zählen
        $rate = isset($data['rate']) ? floatval($data['rate']) : 0;
        $rate_key = number_format($rate, 2);
        if (!isset($stats['by_rate'][$rate_key])) {
            $stats['by_rate'][$rate_key] = 0;
        }
        $stats['by_rate'][$rate_key]++;
        
        // Letzte Aktualisierung
        $stats['last_updated'] = current_time('mysql');
        
        update_option(self::OPTION_NAME, $stats);
    }
    
    /**
     * Standard-Statistik
     */
    private function get_default_stats() {
        return array(
            'total_calculations' => 0,
            'daily' => array(),
            'by_language' => array(),
            'by_rate' => array(),
            'last_updated' => current_time('mysql')
        );
    }
    
    /**
     * Statistik-Seite rendern
     */
    public function render_stats_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Sie haben keine Berechtigung, diese Seite zu sehen.'));
        }
        
        $stats = get_option(self::OPTION_NAME, $this->get_default_stats());
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="mwst-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                
                <!-- Gesamtzahl -->
                <div class="mwst-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 14px; opacity: 0.9;">Gesamte Berechnungen</h3>
                    <div style="font-size: 36px; font-weight: bold;"><?php echo number_format($stats['total_calculations'], 0, ',', '.'); ?></div>
                </div>
                
                <!-- Heute -->
                <div class="mwst-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 14px; opacity: 0.9;">Berechnungen Heute</h3>
                    <div style="font-size: 36px; font-weight: bold;"><?php 
                        $today = date('Y-m-d');
                        echo isset($stats['daily'][$today]) ? number_format($stats['daily'][$today], 0, ',', '.') : '0';
                    ?></div>
                </div>
                
                <!-- Durchschnitt -->
                <div class="mwst-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 14px; opacity: 0.9;">Ø pro Tag (30 Tage)</h3>
                    <div style="font-size: 36px; font-weight: bold;"><?php 
                        $avg = !empty($stats['daily']) ? round(array_sum($stats['daily']) / count($stats['daily']), 1) : 0;
                        echo number_format($avg, 1, ',', '.');
                    ?></div>
                </div>
                
                <!-- Beliebteste Sprache -->
                <div class="mwst-stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px; font-size: 14px; opacity: 0.9;">Beliebteste Sprache</h3>
                    <div style="font-size: 36px; font-weight: bold;"><?php 
                        if (!empty($stats['by_language'])) {
                            arsort($stats['by_language']);
                            $top_lang = array_key_first($stats['by_language']);
                            $lang_names = array('de' => 'DE', 'en' => 'EN', 'fr' => 'FR', 'tr' => 'TR');
                            echo isset($lang_names[$top_lang]) ? $lang_names[$top_lang] : strtoupper($top_lang);
                        } else {
                            echo '-';
                        }
                    ?></div>
                </div>
                
            </div>
            
            <!-- Verlauf (letzte 30 Tage) -->
            <div class="mwst-stats-section" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
                <h2>Verlauf (letzte 30 Tage)</h2>
                <canvas id="mwstChart" style="max-height: 400px;"></canvas>
            </div>
            
            <!-- Sprachen -->
            <div class="mwst-stats-section" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
                <h2>Nutzung nach Sprache</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Sprache</th>
                            <th>Anzahl</th>
                            <th>Prozent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $lang_names = array(
                            'de' => '🇩🇪 Deutsch',
                            'en' => '🇬🇧 Englisch',
                            'fr' => '🇫🇷 Französisch',
                            'tr' => '🇹🇷 Türkisch'
                        );
                        
                        if (!empty($stats['by_language'])) {
                            arsort($stats['by_language']);
                            foreach ($stats['by_language'] as $lang => $count) {
                                $percent = ($stats['total_calculations'] > 0) ? round(($count / $stats['total_calculations']) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><?php echo isset($lang_names[$lang]) ? $lang_names[$lang] : strtoupper($lang); ?></td>
                                    <td><?php echo number_format($count, 0, ',', '.'); ?></td>
                                    <td>
                                        <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                                            <div style="background: linear-gradient(90deg, #667eea, #764ba2); width: <?php echo $percent; ?>%; padding: 5px 10px; color: white; font-weight: bold; min-width: 50px;">
                                                <?php echo $percent; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="3">Noch keine Daten vorhanden</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Steuersätze -->
            <div class="mwst-stats-section" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
                <h2>Meistgenutzte Steuersätze</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Steuersatz</th>
                            <th>Anzahl</th>
                            <th>Prozent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($stats['by_rate'])) {
                            arsort($stats['by_rate']);
                            $top_rates = array_slice($stats['by_rate'], 0, 10, true);
                            foreach ($top_rates as $rate => $count) {
                                $percent = ($stats['total_calculations'] > 0) ? round(($count / $stats['total_calculations']) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td><strong><?php echo str_replace('.', ',', $rate); ?>%</strong></td>
                                    <td><?php echo number_format($count, 0, ',', '.'); ?></td>
                                    <td>
                                        <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                                            <div style="background: linear-gradient(90deg, #f093fb, #f5576c); width: <?php echo $percent; ?>%; padding: 5px 10px; color: white; font-weight: bold; min-width: 50px;">
                                                <?php echo $percent; ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="3">Noch keine Daten vorhanden</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Letzte Aktualisierung -->
            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                <strong>Letzte Aktualisierung:</strong> <?php echo esc_html($stats['last_updated']); ?>
            </p>
            
            <!-- Reset Button -->
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field('mwst_reset_stats', 'mwst_reset_nonce'); ?>
                <button type="submit" name="mwst_reset_stats" class="button button-secondary" onclick="return confirm('Möchten Sie wirklich alle Statistiken zurücksetzen?');">
                    Statistik zurücksetzen
                </button>
            </form>
            
        </div>
        
        <!-- Chart.js einbinden -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('mwstChart');
            if (ctx) {
                const data = <?php echo json_encode(array_values($stats['daily'])); ?>;
                const labels = <?php echo json_encode(array_map(function($date) {
                    return date('d.m', strtotime($date));
                }, array_keys($stats['daily']))); ?>;
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Berechnungen pro Tag',
                            data: data,
                            borderColor: 'rgb(102, 126, 234)',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });
        </script>
        <?php
        
        // Statistik zurücksetzen
        if (isset($_POST['mwst_reset_stats']) && check_admin_referer('mwst_reset_stats', 'mwst_reset_nonce')) {
            delete_option(self::OPTION_NAME);
            echo '<div class="notice notice-success"><p>Statistik wurde zurückgesetzt.</p></div>';
            echo '<script>window.location.reload();</script>';
        }
    }
    
    /**
     * Dashboard Widget hinzufügen
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'mwst_rechner_dashboard_widget',
            'MwSt Rechner Statistik',
            array($this, 'render_dashboard_widget')
        );
    }
    
    /**
     * Dashboard Widget rendern
     */
    public function render_dashboard_widget() {
        $stats = get_option(self::OPTION_NAME, $this->get_default_stats());
        $today = date('Y-m-d');
        $today_count = isset($stats['daily'][$today]) ? $stats['daily'][$today] : 0;
        
        ?>
        <div class="mwst-dashboard-widget">
            <div style="display: flex; justify-content: space-around; text-align: center; padding: 10px 0;">
                <div>
                    <div style="font-size: 24px; font-weight: bold; color: #667eea;"><?php echo number_format($stats['total_calculations'], 0, ',', '.'); ?></div>
                    <div style="font-size: 12px; color: #666;">Gesamt</div>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: bold; color: #f5576c;"><?php echo number_format($today_count, 0, ',', '.'); ?></div>
                    <div style="font-size: 12px; color: #666;">Heute</div>
                </div>
            </div>
            <p style="text-align: center; margin-top: 10px;">
                <a href="<?php echo admin_url('admin.php?page=mwst-rechner-stats'); ?>" class="button button-small">Vollständige Statistik →</a>
            </p>
        </div>
        <?php
    }
}

// Statistik initialisieren
function mwst_rechner_init_statistics() {
    return MWST_Rechner_Statistics::get_instance();
}
add_action('plugins_loaded', 'mwst_rechner_init_statistics');