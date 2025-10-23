# 🚀 Quick Reference - Modulo Gift Card

Riferimento rapido per sviluppatori e amministratori.

---

## 🎯 Installazione Rapida (30 secondi)

```bash
# 1. Copia modulo
cp -r mlab_product_gift_card /path/to/prestashop/modules/

# 2. Installa da backoffice
Moduli → Module Manager → Cerca "Gift Card" → Installa

# 3. Configura
Importi: 25,50,100,150,200
Validità: 365 giorni
```

---

## 📁 Struttura File Essenziali

```
mlab_product_gift_card/
├── mlab_product_gift_card.php     ← File principale
├── controllers/front/apply.php    ← AJAX gift card
├── views/
│   ├── templates/front/
│   │   ├── giftcard_form.tpl     ← Form prodotto
│   │   └── cart_giftcard.tpl     ← Form carrello
│   ├── templates/admin/
│   │   └── product_giftcard.tpl  ← Config admin
│   ├── css/giftcard.css           ← Stili
│   └── js/giftcard.js             ← JavaScript
└── mails/it/
    ├── giftcard.html              ← Email HTML
    └── giftcard.txt               ← Email testo
```

---

## 🗄️ Query Database Utili

```sql
-- Gift card attive
SELECT * FROM ps_giftcard WHERE status = 'active';

-- Gift card scadute
SELECT * FROM ps_giftcard WHERE date_expiry < NOW();

-- Utilizzi gift card
SELECT * FROM ps_giftcard_usage WHERE id_giftcard = X;

-- Prodotti gift card
SELECT * FROM ps_product_giftcard WHERE is_giftcard = 1;

-- Totale vendite
SELECT SUM(amount) FROM ps_giftcard;
```

---

## 🔧 Configurazione Rapida

### Via Backoffice
```
Moduli → Gift Card Product → Configura
```

### Via Database
```sql
-- Abilita modulo
UPDATE ps_configuration SET value = '1' 
WHERE name = 'MLAB_GIFTCARD_ENABLED';

-- Imposta importi
UPDATE ps_configuration SET value = '25,50,100,150,200' 
WHERE name = 'MLAB_GIFTCARD_AMOUNTS';

-- Imposta validità
UPDATE ps_configuration SET value = '365' 
WHERE name = 'MLAB_GIFTCARD_VALIDITY_DAYS';
```

---

## 🎨 Personalizzazione Rapida

### Cambia Colori Email
```css
/* mails/it/giftcard.html - linea ~20 */
background: linear-gradient(135deg, #TUO_COLORE1, #TUO_COLORE2);
```

### Cambia Importi Predefiniti
```php
// Via config o database
Configuration::updateValue('MLAB_GIFTCARD_AMOUNTS', '30,60,90,120');
```

### Cambia Periodo Validità
```php
Configuration::updateValue('MLAB_GIFTCARD_VALIDITY_DAYS', 730); // 2 anni
```

---

## 🐛 Debug Rapido

### Verifica Installazione
```php
// In qualsiasi file PHP PrestaShop
$module = Module::getInstanceByName('mlab_product_gift_card');
var_dump($module->active); // true = installato
```

### Log Email
```bash
tail -f /var/log/mail.log
```

### Verifica Gift Card Generata
```sql
SELECT * FROM ps_giftcard ORDER BY id_giftcard DESC LIMIT 1;
```

### Test Codice
```javascript
// Console browser - pagina carrello
fetch('/module/mlab_product_gift_card/apply', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=check&code=GC-XXXXXXXXXXXX'
}).then(r => r.json()).then(console.log);
```

---

## ⚡ Comandi Utili

### Svuota Cache
```bash
rm -rf /path/to/prestashop/var/cache/*
```

### Reinstalla Modulo
```php
// Backoffice → Moduli
Disinstalla → Installa
```

### Reset Configurazione
```sql
DELETE FROM ps_configuration 
WHERE name LIKE 'MLAB_GIFTCARD_%';
```

---

## 📋 Checklist Veloce

### Pre-Deploy
- [ ] Backup database
- [ ] Test email ricevuta
- [ ] Test codice applicato
- [ ] Verifica responsive mobile
- [ ] Cache svuotata

### Troubleshooting
- [ ] Modulo attivo?
- [ ] Tabelle create?
- [ ] Prodotto configurato?
- [ ] Email configurata?
- [ ] JavaScript caricato?

---

## 🔑 Variabili Template Email

```smarty
{giftcard_code}        → Codice gift card
{giftcard_amount}      → Importo formattato
{recipient_name}       → Nome destinatario
{sender_name}          → Nome mittente
{message}              → Messaggio personalizzato
{expiry_date}          → Data scadenza
{shop_name}            → Nome negozio
{shop_url}             → URL negozio
```

---

## 🎯 Hook Points

```php
displayProductAdditionalInfo  → Form su prodotto
displayAdminProductsExtra     → Config backoffice
actionProductUpdate           → Salva config
actionValidateOrder           → Genera gift card
displayShoppingCartFooter     → Form carrello
displayHeader                 → Carica assets
actionCartSave                → Gestisce carrello
```

---

## 📞 Supporto Veloce

**Errore:** Email non ricevuta  
**Fix:** Controlla `/var/log/mail.log` e config SMTP

**Errore:** Form non visibile  
**Fix:** Svuota cache + verifica hook

**Errore:** Codice non valido  
**Fix:** Controlla spazi e maiuscole/minuscole

**Errore:** Gift card non applicata  
**Fix:** Verifica cookie e JavaScript console

---

## 💡 Tips & Tricks

```php
// Genera codice manualmente
$code = 'GC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));

// Verifica validità gift card
$giftcard = Db::getInstance()->getRow('
    SELECT * FROM ps_giftcard 
    WHERE code = "' . pSQL($code) . '"
    AND status = "active"
    AND date_expiry > NOW()
    AND remaining_amount > 0
');

// Applica gift card a ordine
Db::getInstance()->insert('giftcard_usage', [
    'id_giftcard' => $id_giftcard,
    'id_order' => $id_order,
    'amount_used' => $amount,
    'date_add' => date('Y-m-d H:i:s')
]);
```

---

## 🔗 Link Utili

- **Configurazione:** `/admin/index.php?controller=AdminModules&configure=mlab_product_gift_card`
- **Controller AJAX:** `/module/mlab_product_gift_card/apply`
- **Template Email:** `/modules/mlab_product_gift_card/mails/`
- **Assets:** `/modules/mlab_product_gift_card/views/`

---

## 📊 Stati Gift Card

| Stato | Descrizione | Utilizzabile |
|-------|-------------|--------------|
| active | Attiva e valida | ✅ Sì |
| used | Completamente utilizzata | ❌ No |
| expired | Scaduta | ❌ No |

---

## 🎨 Codici Colore Default

```css
Primary:    #667eea
Secondary:  #764ba2
Success:    #d4edda
Error:      #f8d7da
Warning:    #ffc107
```

---

## 📱 Test Rapidi

### Test 1: Generazione (2 min)
```
1. Crea ordine con gift card
2. Controlla: SELECT * FROM ps_giftcard ORDER BY id_giftcard DESC LIMIT 1;
3. Verifica email ricevuta
```

### Test 2: Applicazione (1 min)
```
1. Copia codice da email
2. Vai al carrello
3. Applica codice
4. Verifica sconto applicato
```

### Test 3: Validazione (30 sec)
```
1. Prova codice invalido → Errore
2. Prova codice scaduto → Errore
3. Prova senza credito → Errore
```

---

## 🚨 Errori Comuni

| Errore | Causa | Soluzione |
|--------|-------|-----------|
| Tabelle non create | Installazione fallita | Reinstalla modulo |
| Email non inviata | Config SMTP errata | Verifica email PS |
| Form non visibile | Cache attiva | Svuota cache |
| JS non caricato | 404 assets | Verifica permessi |
| Codice non valido | Maiuscole/spazi | Trim e uppercase |

---

## 📈 Performance

```php
// Ottimizza query
// ❌ Lento
foreach ($orders as $order) {
    $giftcard = Db::getInstance()->getRow('SELECT...');
}

// ✅ Veloce
$ids = implode(',', array_map('intval', $order_ids));
$giftcards = Db::getInstance()->executeS('
    SELECT * FROM ps_giftcard WHERE id_order IN (' . $ids . ')
');
```

---

## 🔒 Sicurezza Checklist

- [x] SQL Injection → pSQL()
- [x] XSS → escape:'htmlall'
- [x] CSRF → PrestaShop tokens
- [x] Email validation → regex
- [x] Code uniqueness → check DB
- [x] Directory listing → index.php

---

**⚡ Quick Reference v1.0**  
**Updated:** 2024-10-10  
**By:** mlabfactory
