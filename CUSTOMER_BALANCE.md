# 💳 Funzionalità: Verifica Saldo Gift Card

## Panoramica

Questa funzionalità permette ai clienti di visualizzare e gestire le proprie gift card direttamente dall'area riservata.

---

## 🎯 Caratteristiche

### 1. **Area Clienti - Le Mie Gift Card**

I clienti possono accedere a una pagina dedicata dove visualizzare:

- ✅ Tutte le gift card ricevute
- ✅ Saldo residuo per ogni card
- ✅ Stato (Attiva/Utilizzata/Scaduta)
- ✅ Data di ricezione
- ✅ Data di scadenza
- ✅ Mittente della gift card
- ✅ Messaggio personalizzato

### 2. **Verifica Saldo per Codice**

Funzionalità per verificare qualsiasi gift card inserendo il codice:

- ✅ Form di ricerca per codice
- ✅ Visualizzazione dettagli completi
- ✅ Controllo validità in tempo reale

### 3. **Utilizzo Rapido**

- ✅ Copia codice con un click
- ✅ Bottone "Usa" per andare direttamente al checkout
- ✅ Visualizzazione messaggio del mittente

### 4. **Dashboard Riepilogativa**

- ✅ Totale gift card ricevute
- ✅ Gift card attive
- ✅ Saldo totale disponibile

---

## 📁 File Implementati

### Controller Frontend
```
controllers/front/balance.php
```
- Gestione pagina gift card
- Autenticazione richiesta
- Query database per recupero gift card

### Template Smarty
```
views/templates/front/balance.tpl
```
- Lista gift card
- Form verifica saldo
- Dashboard riepilogativa
- Modal messaggio personalizzato

### JavaScript
```
views/js/balance.js
```
- Copia codice clipboard
- Visualizzazione messaggi
- Interazioni UI

### Link Area Clienti
```
views/templates/front/my-account-link.tpl
```
- Link visibile in "Il mio account"
- Icona gift card

---

## 🔗 URL Accesso

```
https://tuosito.com/module/mlab_product_gift_card/balance
```

Oppure tramite link nell'area clienti "My Account".

---

## 🎨 Interfaccia Utente

### Sezione 1: Verifica Saldo

```
┌─────────────────────────────────────────────┐
│ 🔍 Controlla Saldo Gift Card                │
├─────────────────────────────────────────────┤
│                                             │
│ [Input: GC-XXXXXXXXXXXX]  [Controlla]      │
│                                             │
│ ✓ Gift Card Trovata!                       │
│   Codice: GC-A1B2C3D4E5F6                  │
│   Saldo Residuo: €50,00                    │
│   Scade il: 31/12/2024                     │
└─────────────────────────────────────────────┘
```

### Sezione 2: Lista Gift Card

```
┌─────────────────────────────────────────────────────────────────┐
│ 🎁 Le Mie Gift Card Ricevute                                   │
├──────┬────────┬─────────┬────────┬──────────┬─────────┬────────┤
│ Code │ Stato  │ Importo │ Residuo│ Ricevuta │ Scade   │ Azioni │
├──────┼────────┼─────────┼────────┼──────────┼─────────┼────────┤
│ GC-  │ Attiva │ €100    │ €50    │ 01/10/24 │ 01/10/25│ [Usa]  │
│ ...  │        │         │        │          │         │ [Msg]  │
└──────┴────────┴─────────┴────────┴──────────┴─────────┴────────┘
```

### Sezione 3: Dashboard

```
┌────────────────┬────────────────┬────────────────┐
│ Totale Cards   │ Cards Attive   │ Saldo Totale   │
│     5          │      3         │   €150,00      │
└────────────────┴────────────────┴────────────────┘
```

---

## 💻 Utilizzo da Codice

### Ottenere Gift Card di un Cliente

```php
$controller = $this->module->getContainer()->getGiftCardController();
$giftCards = $controller->getByRecipientEmail('customer@email.com');

foreach ($giftCards as $giftCard) {
    echo $giftCard->getCode() . ': ';
    echo Tools::displayPrice($giftCard->getRemainingAmount());
}
```

### Query Diretta Database

```php
// Nel controller
$sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'giftcard`
        WHERE `recipient_email` = "' . pSQL($email) . '"
        ORDER BY `date_add` DESC';

$results = Db::getInstance()->executeS($sql);
```

---

## 🔐 Sicurezza

### Autenticazione
- ✅ Solo clienti autenticati possono accedere
- ✅ Redirect automatico al login se non autenticato
- ✅ Visualizzazione solo delle proprie gift card

### Privacy
- ✅ Ogni cliente vede solo le gift card ricevute al proprio indirizzo email
- ✅ I codici sono visibili solo al destinatario

---

## 🎯 User Flow

```
Cliente → Login
    ↓
Area Clienti → Click "Le Mie Gift Card"
    ↓
Pagina Balance
    ↓
    ├─→ Visualizza Lista Gift Card
    │   ├─→ Vede saldo residuo
    │   ├─→ Legge messaggio mittente
    │   └─→ Copia codice / Usa card
    │
    └─→ Verifica Gift Card per Codice
        ├─→ Inserisce codice
        ├─→ Vede dettagli
        └─→ Copia per usare
```

---

## 📱 Responsive Design

La pagina è completamente responsive e si adatta a:

- 📱 **Mobile** - Liste compatte, cards sovrapposte
- 💻 **Tablet** - Layout a 2 colonne
- 🖥️ **Desktop** - Tabella completa con tutti i dettagli

---

## 🔄 Aggiornamento Automatico

- ✅ Lo stato viene aggiornato automaticamente quando la gift card viene usata
- ✅ Le card scadute vengono evidenziate visivamente
- ✅ Il saldo residuo è sempre aggiornato

---

## 🎨 CSS Classes Disponibili

```css
.giftcard-balance-page      /* Container principale */
.giftcard-check-form        /* Form verifica saldo */
.giftcard-table             /* Tabella gift cards */
.giftcard-row               /* Riga tabella */
.giftcard-row.expired       /* Riga scaduta (opacità ridotta) */
.giftcard-code              /* Codice gift card (monospace) */
.summary-card               /* Card riepilogo */
.use-giftcard               /* Bottone "Usa" */
.view-message               /* Bottone "Visualizza messaggio" */
```

---

## 🌐 Traduzioni

Tutte le stringhe sono traducibili tramite file:
```
translations/it.php
```

Chiavi principali:
- `balance_my_gift_cards`
- `balance_check_balance`
- `balance_remaining_balance`
- `balance_no_cards`
- etc.

---

## ✨ Funzionalità Avanzate

### Copia Automatica Codice

```javascript
// Click su codice → copia in clipboard
document.querySelector('.giftcard-code').click();
// → Codice copiato!
```

### Notifiche Toast

```javascript
// Notifiche automatiche per azioni
showNotification('Codice copiato!', 'success');
```

### Modal Messaggio

```javascript
// Visualizza messaggio personalizzato in modal Bootstrap
$('.view-message').click();
```

---

## 📊 Statistiche Visualizzate

1. **Totale Gift Card** - Tutte le card ricevute
2. **Gift Card Attive** - Card utilizzabili (non scadute, con saldo)
3. **Saldo Totale** - Somma di tutti i saldi residui

---

## 🔧 Estensioni Future

Possibili miglioramenti:

- [ ] Notifiche email pre-scadenza
- [ ] Storico utilizzi dettagliato
- [ ] Ricarica gift card
- [ ] Transfer gift card ad altro utente
- [ ] QR code per redemption
- [ ] App mobile

---

## 📞 Supporto

Per problemi o domande sulla funzionalità Balance:
- Email: tech@mlabfactory.com
- Documentazione: README.md

---

**Funzionalità implementata e testata** ✅

© 2024 mlabfactory
