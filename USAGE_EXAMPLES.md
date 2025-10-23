# Esempi di Utilizzo - Modulo Gift Card

Questa guida fornisce esempi pratici di utilizzo del modulo Gift Card.

---

## Esempi per Amministratori

### 1. Configurazione Base del Modulo

```
1. Vai su: Moduli > Module Manager
2. Cerca: "Gift Card Product"
3. Clicca: Configura
4. Imposta:
   - Abilita Gift Card: SÌ
   - Importi Disponibili: 25,50,100,150,200
   - Periodo di Validità: 365 giorni
5. Salva
```

### 2. Creare un Prodotto Gift Card Standard

```
1. Catalogo > Prodotti > Nuovo Prodotto
2. Nome: "Gift Card Dolce & Zampa"
3. Descrizione: "Regala un'esperienza culinaria..."
4. Prezzo: 0 (verrà sovrascritto dalla selezione utente)
5. Scorri a: "Gift Card Settings"
6. Abilita: "È una Gift Card"
7. Lascia vuoto: "Importi Personalizzati" (usa quelli predefiniti)
8. Aggiungi immagine rappresentativa
9. Salva
```

### 3. Creare una Gift Card con Importi Personalizzati

```
1. Catalogo > Prodotti > Nuovo Prodotto
2. Nome: "Gift Card Premium"
3. Scorri a: "Gift Card Settings"
4. Abilita: "È una Gift Card"
5. Importi Personalizzati: "50,100,200,500"
6. Salva
```

### 4. Modificare gli Importi Predefiniti

```
1. Moduli > Module Manager > Gift Card Product > Configura
2. Importi Disponibili: "20,40,60,80,100,150"
3. Salva
4. Tutti i prodotti gift card senza importi personalizzati useranno questi
```

---

## Esempi per Clienti

### Scenario 1: Regalo di Compleanno

**Maria vuole regalare una gift card alla sua amica Lucia per il compleanno**

```
1. Maria naviga nel sito e trova "Gift Card Dolce & Zampa"
2. Seleziona importo: €50
3. Compila il form:
   - Email Destinatario: lucia@example.com
   - Nome Destinatario: Lucia Rossi
   - Il Tuo Nome: Maria Bianchi
   - Messaggio: "Buon compleanno Lucia! Spero ti piaccia :)"
4. Aggiunge al carrello
5. Completa l'ordine con pagamento
6. Lucia riceve l'email con:
   - Codice: GC-A1B2C3D4E5F6
   - Importo: €50,00
   - Messaggio di Maria
   - Istruzioni per l'uso
```

### Scenario 2: Regalo Aziendale

**Un'azienda vuole regalare gift card ai dipendenti**

```
Per ogni dipendente:

1. Seleziona Gift Card
2. Importo: €100
3. Email Destinatario: dipendente@azienda.com
4. Nome Destinatario: Nome Dipendente
5. Il Tuo Nome: Azienda XYZ
6. Messaggio: "Grazie per il tuo impegno! Buone feste!"
7. Procede con l'ordine

Ogni dipendente riceve la sua gift card personale via email.
```

### Scenario 3: Utilizzo Gift Card

**Lucia vuole utilizzare la gift card ricevuta**

```
1. Lucia apre l'email ricevuta
2. Annota il codice: GC-A1B2C3D4E5F6
3. Visita il sito Dolce & Zampa
4. Aggiunge prodotti al carrello per €35
5. Nel carrello, trova "Hai una Gift Card?"
6. Inserisce: GC-A1B2C3D4E5F6
7. Clicca "Applica"
8. Vede: "Gift Card Applicata! Importo: €50,00"
9. Completa l'ordine
10. Credito residuo: €15,00 (disponibile per acquisti futuri)
```

### Scenario 4: Utilizzo Multiplo

**Lucia usa il credito residuo**

```
Primo ordine: €35 (credito residuo: €15)

Secondo ordine:
1. Aggiunge prodotti per €12
2. Applica lo stesso codice: GC-A1B2C3D4E5F6
3. Sconto applicato: €12
4. Credito residuo: €3
5. Completa ordine a €0

Terzo ordine:
1. Aggiunge prodotti per €25
2. Applica codice: GC-A1B2C3D4E5F6
3. Sconto applicato: €3
4. Paga la differenza: €22
5. Gift card completamente utilizzata
```

---

## Esempi Query Database

### Verificare Gift Card Generate

```sql
-- Tutte le gift card attive
SELECT * FROM ps_giftcard 
WHERE status = 'active' 
ORDER BY date_add DESC;

-- Gift card in scadenza nei prossimi 30 giorni
SELECT code, recipient_email, remaining_amount, date_expiry 
FROM ps_giftcard 
WHERE status = 'active' 
AND date_expiry BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY);

-- Totale vendite gift card
SELECT SUM(amount) as total_sold, COUNT(*) as total_cards
FROM ps_giftcard;

-- Gift card per prodotto
SELECT p.name, COUNT(g.id_giftcard) as cards_sold, SUM(g.amount) as total_amount
FROM ps_giftcard g
JOIN ps_product_lang p ON g.id_product = p.id_product
WHERE p.id_lang = 1
GROUP BY g.id_product;
```

### Verificare Utilizzi

```sql
-- Storico utilizzi di una gift card
SELECT u.*, o.reference, o.date_add
FROM ps_giftcard_usage u
JOIN ps_orders o ON u.id_order = o.id_order
WHERE u.id_giftcard = 1;

-- Gift card più utilizzate
SELECT g.code, COUNT(u.id_giftcard_usage) as times_used, 
       g.amount, g.remaining_amount
FROM ps_giftcard g
LEFT JOIN ps_giftcard_usage u ON g.id_giftcard = u.id_giftcard
GROUP BY g.id_giftcard
ORDER BY times_used DESC;
```

---

## Esempi Personalizzazione

### Modificare Template Email

**Cambiare colore gradiente header email**

File: `/modules/mlab_product_gift_card/mails/it/giftcard.html`

```css
/* Cerca questa riga (circa linea 20): */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Cambia con i tuoi colori: */
background: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
```

### Aggiungere Nuovo Importo Predefinito

```
1. Moduli > Module Manager > Gift Card Product > Configura
2. Importi Disponibili: aggiungi ",250" alla fine
3. Risultato: "25,50,100,150,200,250"
4. Salva
```

### Modificare Periodo Validità

```
1. Moduli > Module Manager > Gift Card Product > Configura
2. Periodo di Validità: cambia da 365 a 730 (2 anni)
3. Salva
4. Le nuove gift card avranno validità 2 anni
```

---

## Esempi Testing

### Test 1: Verifica Generazione Gift Card

```
1. Crea ordine test con prodotto gift card
2. Completa il pagamento
3. Verifica nel database:
   
   SELECT * FROM ps_giftcard ORDER BY id_giftcard DESC LIMIT 1;
   
4. Controlla:
   - Codice generato
   - Importo corretto
   - Email destinatario
   - Data scadenza = data_add + validità giorni
   - Status = 'active'
```

### Test 2: Verifica Invio Email

```
1. Crea gift card con tua email come destinatario
2. Controlla inbox
3. Verifica:
   - Email ricevuta
   - Codice presente
   - Importo formattato correttamente
   - Messaggio personalizzato visualizzato
   - Link negozio funzionante
   - Design responsive su mobile
```

### Test 3: Verifica Applicazione Codice

```
1. Prendi codice da email test
2. Aggiungi prodotti al carrello
3. Applica codice gift card
4. Verifica:
   - Sconto applicato
   - Importo corretto
   - Messaggio successo
   - Pulsante rimozione visibile
```

### Test 4: Verifica Validazioni

```
Test codice invalido:
- Inserisci: GC-INVALIDCODE
- Atteso: "Codice gift card non valido"

Test codice scaduto:
- Modifica database: UPDATE ps_giftcard SET date_expiry = '2020-01-01'
- Applica codice
- Atteso: "Questa gift card è scaduta"

Test senza credito:
- Modifica database: UPDATE ps_giftcard SET remaining_amount = 0
- Applica codice
- Atteso: "Questa gift card non ha credito residuo"
```

---

## Troubleshooting Esempi

### Problema: Email non ricevuta

**Soluzione:**

```
1. Verifica configurazione email PrestaShop
2. Controlla spam/junk folder
3. Verifica log mail:
   tail -f /var/log/mail.log

4. Test manuale invio:
   - Backoffice > Avanzate > Email
   - Invia email test
```

### Problema: Gift card non applicata

**Soluzione:**

```
1. Verifica codice copiato correttamente (no spazi)
2. Controlla stato gift card:
   SELECT * FROM ps_giftcard WHERE code = 'GC-XXXX';
3. Verifica JavaScript console per errori
4. Controlla cookie abilitati nel browser
5. Prova in modalità incognito
```

### Problema: Form non visualizzato su prodotto

**Soluzione:**

```
1. Verifica modulo installato e attivo
2. Controlla configurazione prodotto:
   SELECT * FROM ps_product_giftcard WHERE id_product = X;
3. Verifica hook registrato:
   SELECT * FROM ps_hook_module WHERE id_module = Y;
4. Svuota cache PrestaShop
5. Verifica tema supporta hook displayProductAdditionalInfo
```

---

## Best Practices

### Per Amministratori

✅ **DO:**
- Imposta importi coerenti con i tuoi prodotti
- Usa validità ragionevole (12-24 mesi)
- Testa gift card prima del lancio
- Monitora utilizzi e scadenze
- Mantieni template email aggiornati

❌ **DON'T:**
- Non impostare importi troppo bassi (< €10)
- Non usare validità troppo breve (< 6 mesi)
- Non dimenticare di testare email
- Non ignorare gift card in scadenza

### Per Sviluppatori

✅ **DO:**
- Esegui backup prima delle modifiche
- Testa modifiche in ambiente staging
- Documenta personalizzazioni
- Usa versioning per template
- Monitora performance query

❌ **DON'T:**
- Non modificare file core direttamente
- Non rimuovere validazioni sicurezza
- Non esporre codici in URL/log
- Non bypassare controlli scadenza

---

**Hai bisogno di aiuto?**  
Contatta: tech@mlabfactory.com
