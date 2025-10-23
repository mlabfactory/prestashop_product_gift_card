# Changelog

Tutte le modifiche significative a questo modulo saranno documentate in questo file.

## [1.0.0] - 2024-10-10

### Aggiunto
- Release iniziale del modulo Gift Card per PrestaShop 9
- Gestione prodotti di tipo Gift Card con configurazione da backoffice
- Form frontend per selezione importo e personalizzazione gift card
- Generazione automatica codici univoci gift card (formato GC-XXXXXXXXXXXX)
- Sistema di invio email automatico al destinatario con template HTML responsive
- Template email multilingua (Italiano e Inglese)
- Form di applicazione gift card nel carrello
- Controller AJAX per validazione e applicazione codici
- Gestione credito residuo e utilizzi multipli
- Sistema di scadenza automatica configurabile
- Tracking completo utilizzi gift card
- Validazione email real-time nel form
- Counter caratteri per messaggi personalizzati
- Interfaccia admin per configurazione importi predefiniti
- Configurazione importi personalizzati per singolo prodotto
- 3 tabelle database per gestione completa gift card
- CSS responsive con design moderno
- JavaScript per validazione e UX migliorata
- File di traduzione italiana completo
- Documentazione completa (README, INSTALLATION, FEATURES)
- File di configurazione composer.json e config.xml
- Index files per sicurezza directory
- Hook integration completa con PrestaShop 9

### Caratteristiche Tecniche
- Compatibilità PrestaShop 9.0.0+
- PHP 7.4+ support
- Best practices PrestaShop
- Security-first approach (SQL injection prevention, XSS protection)
- Mobile responsive design
- AJAX implementation
- WidgetInterface implementation
- Multilingua ready

### Database
- Tabella `ps_giftcard` per gestione gift card
- Tabella `ps_product_giftcard` per configurazione prodotti
- Tabella `ps_giftcard_usage` per storico utilizzi

### Hooks Implementati
- `displayProductAdditionalInfo` - Form prodotto
- `displayAdminProductsExtra` - Configurazione admin
- `actionProductUpdate` - Salvataggio configurazione
- `actionValidateOrder` - Generazione gift card
- `displayShoppingCartFooter` - Form applicazione carrello
- `displayHeader` - Caricamento CSS/JS
- `actionCartSave` - Gestione carrello

### Files
- 13 file PHP
- 3 template Smarty
- 1 file JavaScript
- 1 file CSS
- 4 template email (2 lingue x HTML/TXT)
- 3 file documentazione markdown
- ~900 righe di codice PHP

---

## [Unreleased]

### Pianificato per versioni future
- Dashboard admin per gestione gift card
- Report vendite e statistiche
- Export gift card in CSV/Excel
- Editor template email nel backoffice
- Notifiche email scadenza gift card
- Generazione QR code per gift card
- Stampa gift card fisica PDF
- Generazione bulk gift card
- API REST per integrazioni esterne
- Widget verifica saldo gift card
- Regali gift card multipli in un ordine
- Gift card con condizioni speciali (categorie, importi minimi)
- Sistema punti fedeltà con gift card
- Integrazione con programmi affiliazione

---

## Note di Versione

### Formato
Questo changelog segue le linee guida di [Keep a Changelog](https://keepachangelog.com/it/1.0.0/)
e questo progetto aderisce al [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### Categorie Modifiche
- **Aggiunto** per nuove funzionalità
- **Modificato** per cambiamenti a funzionalità esistenti
- **Deprecato** per funzionalità che saranno rimosse
- **Rimosso** per funzionalità rimosse
- **Corretto** per bug fix
- **Sicurezza** per vulnerabilità corrette

---

**Sviluppato da mlabfactory**  
Contatto: tech@mlabfactory.com
