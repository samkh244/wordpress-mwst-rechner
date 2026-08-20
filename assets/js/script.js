/**
 * Mehrwertsteuer Rechner - JavaScript mit automatischer Berechnung
 * Version 1.2.7
 */

(function() {
    'use strict';

    // ===== ÜBERSETZUNGEN (mit Verlauf-Buttons!) =====
    const translations = {
        de: {
            title: 'Mehrwertsteuer Rechner',
            netto: 'Netto',
            brutto: 'Brutto',
            customRate: 'Eigener Satz (%)',
            berechnen: 'Berechnen',
            ergebnis: 'Ergebnis',
            mwst: 'MwSt',
            calculations: 'Letzte Berechnungen',
            copy: 'Kopieren',
            csv: 'CSV Export',
            pdf: 'PDF Export',
            delete: 'Verlauf leeren',
            deleteEntry: 'Eintrag löschen',
            copied: 'Kopiert!',
            copyError: 'Kopieren fehlgeschlagen',
            deleteConfirm: 'Wirklich löschen?',
            noHistory: 'Noch keine Berechnungen',
            customRateOption: 'Individuell',
            customRatePlaceholder: 'z.B. 7,5',
            notePlaceholder: 'Notiz hinzufügen (z.B. Kundenname)',
            note: 'Notiz',
            date: 'Datum',
            mwstRate: 'MwSt',
            mwstAmount: 'MwSt-Betrag',
            inputError: 'Bitte Netto oder Brutto eingeben'
        },
        en: {
            title: 'VAT Calculator',
            netto: 'Net',
            brutto: 'Gross',
            customRate: 'Custom Rate (%)',
            berechnen: 'Calculate',
            ergebnis: 'Result',
            mwst: 'VAT',
            calculations: 'Recent Calculations',
            copy: 'Copy',
            csv: 'CSV Export',
            pdf: 'PDF Export',
            delete: 'Clear History',
            deleteEntry: 'Delete Entry',
            copied: 'Copied!',
            copyError: 'Copy failed',
            deleteConfirm: 'Really delete?',
            noHistory: 'No calculations yet',
            customRateOption: 'Custom',
            customRatePlaceholder: 'e.g. 7.5',
            notePlaceholder: 'Add note (e.g. customer name)',
            note: 'Note',
            date: 'Date',
            mwstRate: 'VAT',
            mwstAmount: 'VAT Amount',
            inputError: 'Please enter net or gross'
        },
        fr: {
            title: 'Calculateur de TVA',
            netto: 'Net',
            brutto: 'Brut',
            customRate: 'Taux personnalisé (%)',
            berechnen: 'Calculer',
            ergebnis: 'Résultat',
            mwst: 'TVA',
            calculations: 'Calculs récents',
            copy: 'Copier',
            csv: 'Export CSV',
            pdf: 'Export PDF',
            delete: 'Effacer l\'historique',
            deleteEntry: 'Supprimer l\'entrée',
            copied: 'Copié!',
            copyError: 'Échec de la copie',
            deleteConfirm: 'Vraiment supprimer?',
            noHistory: 'Pas encore de calculs',
            customRateOption: 'Personnalisé',
            customRatePlaceholder: 'p.ex. 7,5',
            notePlaceholder: 'Ajouter une note (p.ex. nom du client)',
            note: 'Note',
            date: 'Date',
            mwstRate: 'TVA',
            mwstAmount: 'Montant TVA',
            inputError: 'Veuillez saisir net ou brut'
        },
        tr: {
            title: 'KDV Hesaplayıcı',
            netto: 'Net',
            brutto: 'Brüt',
            customRate: 'Özel Oran (%)',
            berechnen: 'Hesapla',
            ergebnis: 'Sonuç',
            mwst: 'KDV',
            calculations: 'Son Hesaplamalar',
            copy: 'Kopyala',
            csv: 'CSV Dışa Aktar',
            pdf: 'PDF Dışa Aktar',
            delete: 'Geçmişi Temizle',
            deleteEntry: 'Girişi Sil',
            copied: 'Kopyalandı!',
            copyError: 'Kopyalama başarısız',
            deleteConfirm: 'Gerçekten silinsin mi?',
            noHistory: 'Henüz hesaplama yok',
            customRateOption: 'Özel',
            customRatePlaceholder: 'örn. 7,5',
            notePlaceholder: 'Not ekle (örn. müşteri adı)',
            note: 'Not',
            date: 'Tarih',
            mwstRate: 'KDV',
            mwstAmount: 'KDV Tutarı',
            inputError: 'Lütfen net veya brüt girin'
        }
    };

    // Datums-Locale je Sprache
    const dateLocales = {
        de: 'de-DE',
        en: 'en-GB',
        fr: 'fr-FR',
        tr: 'tr-TR'
    };

    // ===== GLOBALE VARIABLEN =====
    let currentLang = 'de';
    let verlauf = [];

    // ===== DOM READY =====
    document.addEventListener('DOMContentLoaded', function() {
        initializeCalculator();
    });

    // ===== INITIALISIERUNG =====
    function initializeCalculator() {
        const container = document.getElementById('mwst-rechner');
        if (!container) return;

        currentLang = container.getAttribute('data-default-lang') || 'de';
        loadHistory();
        setupEventListeners();
        updateLanguage(currentLang);
        initFloatingLabels();
    }

    // ===== EVENT LISTENERS =====
    function setupEventListeners() {
        // Sprachbuttons
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const lang = this.getAttribute('data-lang');
                switchLanguage(lang);
            });
        });

        // BERECHNUNG nur mit Button oder Enter-Taste
        const nettoInput = document.getElementById('netto');
        const bruttoInput = document.getElementById('brutto');
        const mwstSelect = document.getElementById('mwstSelect');
        const customRateInput = document.getElementById('customRate');
        const berechnenBtn = document.getElementById('berechnenBtn');

        // Berechnen-Button (falls vorhanden)
        if (berechnenBtn) {
            berechnenBtn.addEventListener('click', berechnen);
        }

        if (nettoInput) {
            // Brutto leeren wenn Netto eingegeben wird
            nettoInput.addEventListener('input', function() {
                if (this.value) {
                    bruttoInput.value = '';
                }
                this.classList.remove('input-error');
            });

            // Enter-Taste: Berechnen
            nettoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    berechnen();
                }
            });
        }

        if (bruttoInput) {
            // Netto leeren wenn Brutto eingegeben wird
            bruttoInput.addEventListener('input', function() {
                if (this.value) {
                    nettoInput.value = '';
                }
                this.classList.remove('input-error');
            });

            // Enter-Taste: Berechnen
            bruttoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    berechnen();
                }
            });
        }

        if (mwstSelect) {
            mwstSelect.addEventListener('change', function() {
                const customGroup = document.getElementById('customRateGroup');
                if (this.value === 'custom') {
                    customGroup.style.display = 'block';
                } else {
                    customGroup.style.display = 'none';
                }
            });
        }

        if (customRateInput) {
            // Enter-Taste: Berechnen
            customRateInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    berechnen();
                }
            });
        }

        // Footer Buttons
        document.getElementById('kopierenBtn')?.addEventListener('click', copyToClipboard);
        document.getElementById('csvBtn')?.addEventListener('click', exportCSV);
        document.getElementById('pdfBtn')?.addEventListener('click', exportPDF);
        document.getElementById('loeschenBtn')?.addEventListener('click', clearHistory);

        // Verlauf: Notizen und Löschen per Event-Delegation
        // (sicherer als Inline-Handler im HTML)
        const verlaufDiv = document.getElementById('verlauf');
        if (verlaufDiv) {
            verlaufDiv.addEventListener('change', function(e) {
                if (e.target.classList.contains('verlauf-note-input')) {
                    const index = parseInt(e.target.getAttribute('data-index'), 10);
                    if (verlauf[index]) {
                        verlauf[index].notiz = e.target.value;
                        saveHistory();
                    }
                }
            });

            verlaufDiv.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.verlauf-delete');
                if (deleteBtn) {
                    const index = parseInt(deleteBtn.getAttribute('data-index'), 10);
                    if (!isNaN(index)) {
                        verlauf.splice(index, 1);
                        saveHistory();
                        renderHistory();
                    }
                }
            });
        }
    }

    // ===== FLOATING LABELS =====
    function initFloatingLabels() {
        const floatingGroups = document.querySelectorAll('.floating-label-group');

        floatingGroups.forEach(group => {
            const input = group.querySelector('input');

            if (input) {
                input.addEventListener('input', function() {
                    if (this.value) {
                        group.classList.add('has-content');
                    } else {
                        group.classList.remove('has-content');
                    }
                });

                if (input.value) {
                    group.classList.add('has-content');
                }
            }
        });
    }

    // ===== SPRACHE WECHSELN =====
    function switchLanguage(lang) {
        currentLang = lang;

        document.querySelectorAll('.lang-btn').forEach(btn => {
            if (btn.getAttribute('data-lang') === lang) {
                btn.classList.add('active-lang');
                btn.setAttribute('aria-pressed', 'true');
            } else {
                btn.classList.remove('active-lang');
                btn.setAttribute('aria-pressed', 'false');
            }
        });

        updateLanguage(lang);
    }

    // ===== TEXTE AKTUALISIEREN (inkl. Verlauf-Buttons!) =====
    function updateLanguage(lang) {
        const t = translations[lang];

        const titleEl = document.getElementById('title');
        if (titleEl) titleEl.textContent = t.title;

        // Labels mit data-label Attribut
        document.querySelectorAll('[data-label]').forEach(el => {
            const key = el.getAttribute('data-label');
            if (t[key]) {
                el.textContent = t[key];
            }
        });

        // Button-Labels (innerhalb von span.btn-label)
        const copyBtn = document.querySelector('#kopierenBtn .btn-label');
        if (copyBtn) copyBtn.textContent = t.copy;

        const csvBtn = document.querySelector('#csvBtn .btn-label');
        if (csvBtn) csvBtn.textContent = t.csv;

        const pdfBtn = document.querySelector('#pdfBtn .btn-label');
        if (pdfBtn) pdfBtn.textContent = t.pdf;

        const deleteBtn = document.querySelector('#loeschenBtn .btn-label');
        if (deleteBtn) deleteBtn.textContent = t.delete;

        // Floating Labels
        const nettoLabel = document.querySelector('label[for="netto"]');
        if (nettoLabel) nettoLabel.innerHTML = t.netto + ' <span>*</span>';

        const bruttoLabel = document.querySelector('label[for="brutto"]');
        if (bruttoLabel) bruttoLabel.innerHTML = t.brutto + ' <span>*</span>';

        const customRateLabel = document.querySelector('label[for="customRate"]');
        if (customRateLabel) customRateLabel.textContent = t.customRate;

        const customRateInput = document.getElementById('customRate');
        if (customRateInput) customRateInput.placeholder = t.customRatePlaceholder;

        const customOption = document.querySelector('#mwstSelect option[value="custom"]');
        if (customOption) customOption.textContent = t.customRateOption;

        // Verlauf neu rendern
        renderHistory();
    }

    // ===== ZAHLEN SICHER PARSEN (Komma UND Punkt erlauben) =====
    function parseNumber(value) {
        if (typeof value !== 'string') return NaN;
        // "1.234,56" -> "1234.56" | "7,5" -> "7.5"
        const normalized = value.trim().replace(/\./g, function(match, offset, str) {
            // Punkt nur entfernen, wenn auch ein Komma vorhanden ist (Tausendertrennzeichen)
            return str.indexOf(',') > -1 ? '' : '.';
        }).replace(',', '.');
        return parseFloat(normalized);
    }

    // Kaufmännisch auf 2 Nachkommastellen runden
    function round2(value) {
        return Math.round((value + Number.EPSILON) * 100) / 100;
    }

    // ===== BERECHNUNG (nur auf Button-Klick oder Enter) =====
    function berechnen() {
        const nettoInput = document.getElementById('netto');
        const bruttoInput = document.getElementById('brutto');
        const mwstSelect = document.getElementById('mwstSelect');
        const customRateInput = document.getElementById('customRate');

        const netto = parseNumber(nettoInput.value);
        const brutto = parseNumber(bruttoInput.value);

        const hasNetto = !isNaN(netto) && netto > 0;
        const hasBrutto = !isNaN(brutto) && brutto > 0;

        // Prüfe ob genau ein Wert eingegeben wurde
        if (!hasNetto && !hasBrutto) {
            // Visuelles Feedback: Eingabefelder kurz rot markieren
            [nettoInput, bruttoInput].forEach(input => {
                input.classList.add('input-error');
                setTimeout(() => input.classList.remove('input-error'), 1500);
            });
            return;
        }

        let mwstSatz;
        if (mwstSelect.value === 'custom') {
            mwstSatz = parseNumber(customRateInput.value);
            // 0 % ist erlaubt (z.B. innergemeinschaftliche Lieferung, Export)
            if (isNaN(mwstSatz) || mwstSatz < 0 || mwstSatz > 100) {
                customRateInput.classList.add('input-error');
                setTimeout(() => customRateInput.classList.remove('input-error'), 1500);
                return;
            }
        } else {
            mwstSatz = parseFloat(mwstSelect.value);
        }

        let mwstBetrag, nettoErgebnis, bruttoErgebnis;

        if (hasNetto) {
            // Netto -> Brutto: MwSt runden, Brutto = Netto + MwSt (immer konsistent)
            nettoErgebnis = round2(netto);
            mwstBetrag = round2(nettoErgebnis * (mwstSatz / 100));
            bruttoErgebnis = round2(nettoErgebnis + mwstBetrag);
        } else {
            // Brutto -> Netto: Netto runden, MwSt = Brutto - Netto (immer konsistent)
            bruttoErgebnis = round2(brutto);
            nettoErgebnis = round2(bruttoErgebnis / (1 + mwstSatz / 100));
            mwstBetrag = round2(bruttoErgebnis - nettoErgebnis);
        }

        displayResult(nettoErgebnis, mwstBetrag, bruttoErgebnis, mwstSatz);
        addToHistory(nettoErgebnis, mwstBetrag, bruttoErgebnis, mwstSatz);

        // Statistik-Tracking
        trackCalculation(mwstSatz);

        // Floating Labels aktualisieren
        document.querySelectorAll('.floating-label-group').forEach(g => {
            const input = g.querySelector('input');
            if (input && input.value) {
                g.classList.add('has-content');
            }
        });
    }

    // ===== ERGEBNIS ANZEIGEN =====
    function displayResult(netto, mwst, brutto, satz) {
        document.getElementById('ergebnisNetto').textContent = formatCurrency(netto);
        document.getElementById('ergebnisMwst').textContent = formatCurrency(mwst);
        document.getElementById('ergebnisBrutto').textContent = formatCurrency(brutto);
        document.getElementById('mwstSatzText').textContent = formatPercent(satz);

        const box = document.getElementById('ergebnis-box');
        if (box) {
            box.style.display = 'block';
        }
    }

    // ===== STATISTIK-TRACKING =====
    function trackCalculation(rate) {
        if (typeof mwstAjax === 'undefined') {
            return;
        }

        const data = {
            action: 'mwst_track_calculation',
            nonce: mwstAjax.nonce,
            data: JSON.stringify({
                lang: currentLang,
                rate: rate,
                timestamp: Date.now()
            })
        };

        fetch(mwstAjax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        }).catch(function() {
            // Tracking-Fehler bewusst ignorieren (darf den Rechner nie stören)
        });
    }

    // ===== HTML ESCAPEN (Schutz vor XSS über Notizen) =====
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ===== VERLAUF =====
    function addToHistory(netto, mwst, brutto, satz) {
        const entry = {
            timestamp: Date.now(),
            netto: netto,
            mwst: mwst,
            brutto: brutto,
            satz: satz,
            notiz: ''
        };

        verlauf.unshift(entry);

        if (verlauf.length > 10) {
            verlauf = verlauf.slice(0, 10);
        }

        saveHistory();
        renderHistory();
    }

    function renderHistory() {
        const verlaufDiv = document.getElementById('verlauf');
        if (!verlaufDiv) return;

        const t = translations[currentLang];

        if (verlauf.length === 0) {
            verlaufDiv.innerHTML = `<p>${t.noHistory}</p>`;
            return;
        }

        verlaufDiv.innerHTML = verlauf.map((entry, index) => `
            <div class="verlauf-item">
                <div class="verlauf-left">
                    <div class="verlauf-time">${formatDate(entry.timestamp)}</div>
                    <div class="verlauf-values">
                        <div class="verlauf-row">
                            <span class="v-label">${t.netto}:</span>
                            <span class="v-netto">${formatCurrency(entry.netto)}</span>
                        </div>
                        <div class="verlauf-row">
                            <span class="v-label">${t.mwst} (<span class="mwst-prozent">${formatPercent(entry.satz)}</span>):</span>
                            <span class="v-mwst">${formatCurrency(entry.mwst)}</span>
                        </div>
                        <div class="verlauf-row">
                            <span class="v-label">${t.brutto}:</span>
                            <span class="v-brutto">${formatCurrency(entry.brutto)}</span>
                        </div>
                    </div>
                </div>
                <div class="verlauf-bottom-row">
                    <textarea
                        class="verlauf-note-input"
                        data-index="${index}"
                        placeholder="${t.notePlaceholder}"
                    >${escapeHtml(entry.notiz || '')}</textarea>
                    <button class="verlauf-delete" data-index="${index}" title="${t.deleteEntry}" type="button">
                        ✕
                    </button>
                </div>
            </div>
        `).join('');
    }

    function clearHistory() {
        const t = translations[currentLang];
        const btn = document.getElementById('loeschenBtn');

        if (!btn.classList.contains('btn-delete-confirm')) {
            btn.classList.add('btn-delete-confirm');
            btn.querySelector('.btn-label').textContent = t.deleteConfirm;

            setTimeout(() => {
                btn.classList.remove('btn-delete-confirm');
                btn.querySelector('.btn-label').textContent = t.delete;
            }, 3000);

            return;
        }

        verlauf = [];
        saveHistory();
        renderHistory();
        btn.classList.remove('btn-delete-confirm');
        btn.querySelector('.btn-label').textContent = t.delete;
    }

    // ===== EXPORT: KOPIEREN =====
    function copyToClipboard() {
        const t = translations[currentLang];

        if (verlauf.length === 0) return;

        let text = `${t.calculations}\n\n`;

        verlauf.forEach((entry, index) => {
            text += `${index + 1}. ${t.netto}: ${formatCurrency(entry.netto)}   + ${t.mwst} (${formatPercent(entry.satz)}): ${formatCurrency(entry.mwst)}   = ${t.brutto}: ${formatCurrency(entry.brutto)}\n`;
            text += `   ${t.date}: ${formatDate(entry.timestamp)}\n`;

            if (entry.notiz && entry.notiz.trim()) {
                text += `   ${t.note}: ${entry.notiz}\n`;
            }

            text += '\n';
        });

        const btn = document.getElementById('kopierenBtn');
        const span = btn.querySelector('.btn-label');
        const oldText = span.textContent;

        function showFeedback(message) {
            span.textContent = message;
            setTimeout(() => {
                span.textContent = oldText;
            }, 2000);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text)
                .then(() => showFeedback(t.copied))
                .catch(() => copyFallback(text) ? showFeedback(t.copied) : showFeedback(t.copyError));
        } else {
            // Fallback für ältere Browser / HTTP-Seiten
            copyFallback(text) ? showFeedback(t.copied) : showFeedback(t.copyError);
        }
    }

    function copyFallback(text) {
        try {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            const ok = document.execCommand('copy');
            document.body.removeChild(textarea);
            return ok;
        } catch (e) {
            return false;
        }
    }

    // ===== EXPORT: CSV =====
    function exportCSV() {
        const t = translations[currentLang];

        if (verlauf.length === 0) return;

        // Felder in Anführungszeichen setzen -> Semikolons und
        // Zeilenumbrüche in Notizen zerstören die CSV nicht mehr
        function csvField(value) {
            return '"' + String(value).replace(/"/g, '""') + '"';
        }

        let csv = [t.netto, t.mwstRate, t.mwstAmount, t.brutto, t.date, t.note]
            .map(csvField).join(';') + '\n';

        verlauf.forEach(entry => {
            const row = [
                entry.netto.toFixed(2).replace('.', ','),
                entry.satz.toFixed(2).replace('.', ','),
                entry.mwst.toFixed(2).replace('.', ','),
                entry.brutto.toFixed(2).replace('.', ','),
                formatDate(entry.timestamp),
                entry.notiz || ''
            ];
            csv += row.map(csvField).join(';') + '\n';
        });

        downloadFile(csv, 'mehrwertsteuer_verlauf.csv', 'text/csv;charset=utf-8;');
    }

    // ===== EXPORT: PDF =====

    // Hilfsfunktion: Script nachladen
    function loadScript(src) {
        return new Promise(function(resolve, reject) {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function exportPDF() {
        const t = translations[currentLang];

        if (verlauf.length === 0) {
            alert(t.noHistory);
            return;
        }

        // Basis-URL der Plugin-Assets (von WordPress via wp_localize_script)
        const assetBase = (typeof mwstConfig !== 'undefined' && mwstConfig.pluginUrl)
            ? mwstConfig.pluginUrl + 'assets/js/vendor/'
            : null;

        const tasks = [];

        // jsPDF laden: bevorzugt lokal (DSGVO), sonst CDN-Fallback
        if (typeof window.jspdf === 'undefined') {
            if (assetBase) {
                tasks.push(
                    loadScript(assetBase + 'jspdf.umd.min.js').catch(function() {
                        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.2/jspdf.umd.min.js');
                    })
                );
            } else {
                tasks.push(loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.2/jspdf.umd.min.js'));
            }
        }

        // Unicode-Schrift laden (fuer tuerkische Zeichen: ı, ş, ğ ...)
        if (typeof window.mwstRegisterPdfFont === 'undefined' && assetBase) {
            tasks.push(
                loadScript(assetBase + 'mwst-pdf-font.js').catch(function() {
                    // Ohne Schrift weitermachen - Standard-Schrift als Fallback
                })
            );
        }

        Promise.all(tasks).then(generatePDF).catch(function() {
            alert('PDF-Export nicht verfügbar');
        });

        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Unicode-Schrift registrieren (falls geladen)
            if (typeof window.mwstRegisterPdfFont === 'function') {
                window.mwstRegisterPdfFont(doc);
            }

            doc.setFontSize(16);
            doc.setTextColor(9, 132, 174);
            doc.text(t.calculations, 105, 15, { align: 'center' });

            let yPos = 30;

            verlauf.forEach((entry, index) => {
                if (yPos > 275) {
                    doc.addPage();
                    yPos = 20;
                }

                doc.setFontSize(11);
                doc.setTextColor(0, 0, 0);
                doc.text(`${index + 1}. ${t.netto}: ${formatCurrency(entry.netto)}`, 15, yPos);

                doc.setTextColor(214, 40, 40);
                doc.text(`+ ${t.mwst} (${formatPercent(entry.satz)}): ${formatCurrency(entry.mwst)}`, 68, yPos);

                doc.setTextColor(40, 167, 69);
                doc.text(`= ${t.brutto}: ${formatCurrency(entry.brutto)}`, 140, yPos);

                yPos += 6;

                doc.setFontSize(9);
                doc.setTextColor(100, 100, 100);
                doc.text(`${t.date}: ${formatDate(entry.timestamp)}`, 15, yPos);

                if (entry.notiz && entry.notiz.trim()) {
                    yPos += 4;
                    doc.setFontSize(9);
                    doc.setTextColor(80, 80, 80);
                    const notizText = entry.notiz.length > 80 ? entry.notiz.substring(0, 77) + '...' : entry.notiz;
                    doc.text(`${t.note}: ${notizText}`, 15, yPos);
                    yPos += 9;
                } else {
                    yPos += 10;
                }
            });

            doc.save('mehrwertsteuer_verlauf.pdf');
        }
    }

    // ===== HILFSFUNKTIONEN =====
    function formatCurrency(value) {
        return new Intl.NumberFormat('de-DE', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }

    function formatPercent(value) {
        return value.toFixed(2).replace('.', ',') + '%';
    }

    function formatDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString(dateLocales[currentLang] || 'de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function saveHistory() {
        try {
            localStorage.setItem('mwst_verlauf', JSON.stringify(verlauf));
        } catch (e) {
            // localStorage kann voll oder blockiert sein - Rechner läuft trotzdem
        }
    }

    function loadHistory() {
        try {
            const saved = localStorage.getItem('mwst_verlauf');
            if (saved) {
                const parsed = JSON.parse(saved);
                verlauf = Array.isArray(parsed) ? parsed : [];
            }
        } catch (e) {
            verlauf = [];
        }
    }

    function downloadFile(content, filename, mimeType) {
        const BOM = '\uFEFF';
        const blob = new Blob([BOM + content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

})();