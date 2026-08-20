=== Mehrwertsteuer Rechner ===
Contributors: samadkhakpour
Donate link: https://github.com/sponsors/samkh244
Tags: mehrwertsteuer, vat, calculator, rechner, steuer, tax, mwst, umsatzsteuer, schweiz, österreich
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 1.2.7
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professioneller Mehrwertsteuer-Rechner mit Unterstützung für Deutsch, Englisch, Französisch und Türkisch.

== Description ==

Ein benutzerfreundlicher Mehrwertsteuer-Rechner mit umfangreichen Features für Ihre WordPress-Website.

= Hauptfunktionen =

* ✅ **Bidirektionale Berechnung**: Netto → Brutto und Brutto → Netto
* 🌍 **Mehrsprachig**: Deutsch, Englisch, Französisch, Türkisch
* 🇩🇪 **Vordefinierte Steuersätze**: DE (19%, 7%), CH (7.7%, 8.1%, 2.6%, 3.8%), AT (20%)
* ⚙️ **Individuelle Steuersätze**: Eigene Prozentsätze eingeben
* 📝 **Verlauf mit Notizen**: Speichert letzte 10 Berechnungen mit Notizfunktion
* 📄 **PDF-Export**: Druckbares PDF mit allen Berechnungen
* 📊 **CSV-Export**: Excel-kompatible CSV-Datei
* 📋 **Kopieren-Funktion**: Alle Daten mit einem Klick kopieren
* 📱 **Vollständig responsive**: Optimiert für Desktop, Tablet und Smartphone
* 💾 **Lokale Speicherung**: Daten bleiben im Browser des Nutzers
* ♿ **Barrierefrei**: ARIA-Labels und Tastaturnavigation

= Einfache Verwendung =

Nach der Installation einfach einen der Shortcodes auf Ihrer Seite einfügen:

* `[mwst_rechner_de]` - Startet mit deutscher Sprache
* `[mwst_rechner_en]` - Startet mit englischer Sprache
* `[mwst_rechner_fr]` - Startet mit französischer Sprache
* `[mwst_rechner_tr]` - Startet mit türkischer Sprache

Die Sprache kann auch während der Nutzung über die Flaggen-Buttons gewechselt werden.

= Perfekt für =

* Freiberufler und Selbstständige
* Online-Shops und E-Commerce
* Buchhaltungs- und Steuerberater
* Unternehmens-Websites
* Informations-Portale

= Datenschutzfreundlich =

Alle Berechnungen und der Verlauf werden nur im Browser des Nutzers gespeichert (localStorage). Das Plugin zählt lediglich anonyme Nutzungsstatistiken (ohne personenbezogene Daten) in der eigenen WordPress-Datenbank. Es werden keine Daten an Dritte übermittelt.

= Kostenlos & Spendenbasis =

Dieses Plugin ist kostenlos und wird ohne offiziellen Support bereitgestellt ("as is"). Es besteht kein Anspruch auf Hilfe, Fehlerbehebungen oder neue Funktionen. Wenn Ihnen das Plugin gefällt, freuen wir uns über eine freiwillige Spende: [GitHub Sponsors](https://github.com/sponsors/samkh244)

== Installation ==

= Automatische Installation =

1. Gehen Sie zu **Plugins → Installieren**
2. Suchen Sie nach "Mehrwertsteuer Rechner"
3. Klicken Sie auf **Jetzt installieren**
4. Aktivieren Sie das Plugin

= Manuelle Installation =

1. Laden Sie die Plugin-ZIP-Datei herunter
2. Gehen Sie zu **Plugins → Installieren → Plugin hochladen**
3. Wählen Sie die ZIP-Datei aus und klicken Sie auf **Jetzt installieren**
4. Aktivieren Sie das Plugin

= Nach der Installation =

1. Erstellen Sie eine neue Seite oder bearbeiten Sie eine bestehende
2. Fügen Sie einen der Shortcodes ein:
   * `[mwst_rechner_de]` für Deutsch
   * `[mwst_rechner_en]` für Englisch
   * `[mwst_rechner_fr]` für Französisch
   * `[mwst_rechner_tr]` für Türkisch
3. Veröffentlichen Sie die Seite
4. Der Rechner ist sofort einsatzbereit!

= Ordnerstruktur =

Das Plugin sollte folgende Struktur haben:

`
mehrwertsteuer-rechner/
├── mehrwertsteuer-rechner.php
├── includes/
│   └── class-updater.php
├── assets/
│   ├── css/style.css
│   ├── js/script.js
│   └── img/
│       ├── flagsde.svg
│       ├── flagsen.svg
│       ├── flagsfr.svg
│       └── flagstr.svg
└── readme.txt
`

== Frequently Asked Questions ==

= Welche Shortcodes gibt es? =

Es gibt vier Shortcodes für verschiedene Standardsprachen:

* `[mwst_rechner_de]` - Deutsche Standardsprache
* `[mwst_rechner_en]` - Englische Standardsprache
* `[mwst_rechner_fr]` - Französische Standardsprache
* `[mwst_rechner_tr]` - Türkische Standardsprache

Die Sprache kann auch während der Nutzung über die Flaggen-Buttons gewechselt werden.

= Werden die Berechnungen gespeichert? =

Ja, die letzten 10 Berechnungen werden im localStorage des Browsers gespeichert. Die Daten bleiben beim Nutzer lokal und werden nicht auf dem Server gespeichert. Dies gewährleistet maximale Privatsphäre.

= Kann ich eigene Steuersätze eingeben? =

Ja! Wählen Sie im Dropdown-Menü "Individuell" aus und geben Sie Ihren eigenen Steuersatz ein (z.B. 7,5 oder 15,3).

= Wie exportiere ich die Berechnungen? =

Sie haben drei Export-Optionen:

* **Kopieren**: Kopiert alle Daten in die Zwischenablage
* **CSV Export**: Erstellt eine Excel-kompatible CSV-Datei
* **PDF Export**: Erstellt ein druckbares PDF-Dokument

= Funktioniert das Plugin auf Smartphones? =

Ja! Das Plugin ist vollständig responsive und für alle Geräte optimiert - Desktop, Tablet und Smartphone.

= Kann ich Notizen zu Berechnungen hinzufügen? =

Ja, jede Berechnung im Verlauf hat ein Notizfeld. Sie können dort z.B. Kundennamen oder Projektnamen eintragen.

= Werden externe Dienste verwendet? =

Nein. Die jsPDF-Bibliothek für den PDF-Export ist seit Version 1.2.7 lokal im Plugin enthalten - es wird kein CDN mehr kontaktiert. Alle Berechnungen erfolgen lokal im Browser.

= Ist das Plugin DSGVO-konform? =

Ja. Die Berechnungen und der Verlauf werden ausschließlich lokal im Browser gespeichert. Zusätzlich zählt das Plugin anonyme Nutzungsstatistiken (Anzahl der Berechnungen, verwendete Sprache und Steuersätze) in der WordPress-Datenbank der eigenen Website - ohne personenbezogene Daten, ohne Cookies und ohne Übermittlung an Dritte. Die Statistik ist im Admin-Bereich unter "MwSt Statistik" einsehbar.

= Welche Browser werden unterstützt? =

Alle modernen Browser werden unterstützt:
* Chrome/Edge (neueste 2 Versionen)
* Firefox (neueste 2 Versionen)
* Safari (neueste 2 Versionen)
* Opera (neueste 2 Versionen)

= Kann ich das Plugin anpassen? =

Ja, das Plugin ist Open Source (GPL v2). Sie können den Code nach Belieben selbst anpassen.

== Screenshots ==

1. Mehrwertsteuer Rechner - Hauptansicht mit Eingabefeldern
2. Ergebnis-Anzeige mit farblicher Kennzeichnung
3. Verlauf mit Notizen und Export-Funktionen
4. Sprachumschaltung zwischen DE, EN, FR, TR
5. Responsive Design auf Smartphone
6. PDF-Export Vorschau
7. CSV-Export in Excel

== Changelog ==

= 1.2.7 - 2026-08-20 =
* **Behoben**: Eigener Steuersatz mit Komma (z.B. "7,5") wurde falsch berechnet
* **Behoben**: Sicherheitslücke im Notizfeld des Verlaufs (XSS)
* **Behoben**: Türkische Zeichen (ı, ş, ğ) werden im PDF-Export korrekt dargestellt
* **Neu**: jsPDF ist jetzt lokal im Plugin enthalten (kein CDN, DSGVO-freundlich)
* **Neu**: Visuelles Feedback bei ungültigen Eingaben
* **Neu**: 0% als eigener Steuersatz möglich (z.B. für innergemeinschaftliche Lieferungen)
* **Verbessert**: Rundung jetzt garantiert konsistent (Netto + MwSt = Brutto)
* **Verbessert**: CSS wirkt nur noch auf den Rechner und verändert das Theme der Website nicht mehr
* **Verbessert**: CSV-Export verkraftet jetzt Semikolons und Zeilenumbrüche in Notizen
* **Verbessert**: Kopieren-Funktion mit Fallback für ältere Browser
* **Verbessert**: Korrektes Datumsformat für Französisch und Türkisch

= 1.2.5 - 2026-08-19 =
* **Behoben**: Kritischer Fehler im Update-System, der Updates anderer Plugins stören konnte
* **Verbessert**: Update-Prüfung mit Caching (weniger Anfragen an den Update-Server)
* **Verbessert**: Automatische Hintergrund-Updates (WP-Cron) funktionieren jetzt zuverlässig
* **Verbessert**: GitHub-Release-ZIPs werden beim Update korrekt entpackt
* **Geändert**: Kontakt und Autor: Samad Khakpour / mointools.com

= 1.2.1 - 2025-11-27 =
* **Neu**: Türkische Sprachunterstützung hinzugefügt
* **Verbessert**: Responsive Design für Smartphones optimiert
* **Verbessert**: Hover-Effekte für alle Buttons
* **Verbessert**: PDF-Export mit kompakterem Layout
* **Verbessert**: CSV-Export mit korrekter Spaltenformatierung
* **Verbessert**: Kopieren-Funktion im PDF-Format
* **Behoben**: Excel erkennt jetzt alle Zahlen korrekt
* **Behoben**: Floating Labels funktionieren einwandfrei
* **Behoben**: Berechnen-Button wieder zentriert
* **Technisch**: Update-System optimiert mit Caching
* **Technisch**: Migration zu Bindestrich-Konvention (mehrwertsteuer-rechner.php)

= 1.2.0 - 2025-01-15 =
* **Neu**: PDF-Export mit jsPDF
* **Neu**: CSV-Export für Excel
* **Neu**: Kopieren-Funktion für Verlauf
* **Neu**: Notizfunktion für Berechnungen
* **Neu**: Einzelne Einträge löschen
* **Neu**: Automatisches Update-System
* **Verbessert**: Verlauf-Design übersichtlicher
* **Verbessert**: Mobile Optimierung

= 1.1.0 - 2025-01-10 =
* **Neu**: Französische Sprache
* **Neu**: Englische Sprache
* **Neu**: Sprach-Umschalter mit Flaggen
* **Neu**: Verlauf-Funktion (letzte 10 Berechnungen)
* **Neu**: Individuelle Steuersätze
* **Verbessert**: Design-Update mit Farbverläufen
* **Verbessert**: Responsive Layout für Tablet

= 1.0.0 - 2025-01-05 =
* Erste öffentliche Version
* Netto ↔ Brutto Berechnung
* Deutsche, Schweizer, Österreichische Steuersätze
* Responsive Design
* Barrierefreie Umsetzung
* Floating Labels für moderne Input-Felder

== Upgrade Notice ==

= 1.2.7 =
Wichtiges Update: behebt einen Rechenfehler bei Komma-Eingaben und eine Sicherheitslücke. jsPDF jetzt lokal enthalten (DSGVO). Update dringend empfohlen.

= 1.2.5 =
Wichtiges Update: behebt einen kritischen Fehler im Update-System. Update dringend empfohlen.

= 1.2.1 =
Wichtiges Update mit türkischer Sprache, verbessertem Smartphone-Design und optimierten Export-Funktionen. Update wird empfohlen.

= 1.2.0 =
Großes Update mit PDF/CSV-Export, Notizfunktion und automatischen Updates. Bitte vor dem Update ein Backup erstellen.

= 1.1.0 =
Mehrsprachigkeit hinzugefügt! Jetzt mit Englisch und Französisch.

= 1.0.0 =
Erste Version - Installieren und loslegen!

== Arbitrary section ==

= Systemanforderungen =

* WordPress 5.0 oder höher
* PHP 7.2 oder höher
* Moderne Browser (Chrome, Firefox, Safari, Edge)

= Credits =

* Entwickelt von [Samad Khakpour](https://mointools.com)
* jsPDF für PDF-Export
* Flaggen-Icons von [Flagpack](https://flagpack.xyz)

= Lizenz =

Dieses Plugin ist Open Source Software, lizenziert unter GPL v2 oder höher.
Sie können es frei verwenden, modifizieren und verteilen.

Vollständige Lizenz: https://www.gnu.org/licenses/gpl-2.0.html

= Mitwirken =

Das Plugin ist Open Source! Verbesserungsvorschläge und Pull Requests sind willkommen.

Repository: https://github.com/DEIN-GITHUB-BENUTZERNAME/mehrwertsteuer-rechner

= Hinweis =

Dieses Plugin wird kostenlos und ohne offiziellen Support bereitgestellt.
* Website: https://mointools.com

= Spenden =

Wenn Ihnen das Plugin gefällt, freuen wir uns über eine Spende:
https://github.com/sponsors/samkh244
