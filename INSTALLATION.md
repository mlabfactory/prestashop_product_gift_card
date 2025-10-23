# Guida Installazione - Modulo Gift Card PrestaShop 9

## Prerequisiti

- PrestaShop 9.0.0 o superiore
- PHP 7.4 o superiore
- Accesso al backoffice PrestaShop
- Permessi di scrittura nella cartella modules

## Installazione

### 1. Caricamento File

Caricare l'intera cartella `mlab_product_gift_card` nella directory:
```
/modules/mlab_product_gift_card/
```

### 2. Installazione dal Backoffice

1. Accedere al backoffice PrestaShop
2. Andare in **Moduli > Module Manager**
3. Cercare "Gift Card Product"
4. Cliccare su **Installa**

Il modulo creerà automaticamente le seguenti tabelle nel database:
- `ps_giftcard` - Gestione gift card generate
- `ps_product_giftcard` - Configurazione prodotti gift card
- `ps_giftcard_usage` - Storico utilizzi

### 3. Configurazione Iniziale

Dopo l'installazione:

1. Andare in **Moduli > Module Manager**
2. Cercare "Gift Card Product"
3. Cliccare su **Configura**

#### Impostazioni Disponibili:

- **Abilita Gift Card**: ON/OFF per attivare il modulo
- **Importi Disponibili**: Lista di tagli predefiniti (es: 25,50,100,150,200)
- **Periodo di Validità**: Giorni di validità della gift card (default: 365)

Cliccare su **Salva** per applicare le modifiche.

## Configurazione Prodotti

### Creare un Prodotto Gift Card

1. **Creare o Modificare un Prodotto**
   - Andare in **Catalogo > Prodotti**
   - Creare un nuovo prodotto o modificarne uno esistente

2. **Configurare come Gift Card**
   - Scorrere fino alla sezione **Gift Card Settings**
   - Attivare l'opzione **È una Gift Card**
   - (Opzionale) Inserire importi personalizzati nel campo **Importi Personalizzati**
   - Se lasciato vuoto, verranno usati gli importi predefiniti del modulo

3. **Configurazione Prezzo**
   - Il prezzo del prodotto può essere impostato a 0 o un valore base
   - Il prezzo effettivo sarà quello selezionato dall'utente nel form

4. **Immagini e Descrizione**
   - Aggiungere immagini rappresentative della gift card
   - Descrivere il funzionamento e i vantaggi

5. **Salvare il Prodotto**

## Utilizzo Frontend

### Processo di Acquisto Gift Card

1. **L'utente accede alla pagina prodotto**
   - Visualizza il form "Opzioni Gift Card"

2. **Compila il form**
   - Seleziona l'importo desiderato
   - Inserisce l'email del destinatario (obbligatorio)
   - Inserisce il nome del destinatario (opzionale)
   - Inserisce il proprio nome come mittente (opzionale)
   - Aggiunge un messaggio personalizzato (opzionale, max 500 caratteri)

3. **Aggiunge al carrello e completa l'ordine**

4. **Dopo il pagamento**
   - Viene generata automaticamente una gift card con codice univoco
   - Viene inviata un'email al destinatario contenente:
     * Codice gift card
     * Importo
     * Messaggio personalizzato
     * Data di scadenza
     * Istruzioni per l'utilizzo

### Utilizzo Gift Card

1. **Il destinatario riceve l'email**
   - Email con design professionale
   - Codice gift card ben visibile
   - Istruzioni chiare

2. **Utilizzo nel carrello**
   - Aggiungere prodotti al carrello
   - Nel carrello, trovare la sezione "Hai una Gift Card?"
   - Inserire il codice gift card
   - Cliccare su "Applica"
   - Lo sconto viene applicato automaticamente

3. **Gestione Credito Residuo**
   - Se l'ordine è inferiore al valore della gift card, il credito residuo rimane disponibile
   - Il cliente può utilizzare la stessa gift card per acquisti futuri fino a esaurimento credito

## Template Email

### Personalizzazione Email

I template email si trovano in:
```
/modules/mlab_product_gift_card/mails/it/
/modules/mlab_product_gift_card/mails/en/
```

File disponibili:
- `giftcard.html` - Template HTML
- `giftcard.txt` - Template testo

### Variabili Disponibili

Nei template sono disponibili le seguenti variabili:

- `{giftcard_code}` - Codice univoco gift card
- `{giftcard_amount}` - Importo formattato
- `{recipient_name}` - Nome destinatario
- `{sender_name}` - Nome mittente
- `{message}` - Messaggio personalizzato
- `{expiry_date}` - Data di scadenza
- `{shop_name}` - Nome negozio
- `{shop_url}` - URL negozio

### Lingue Supportate

- Italiano (it)
- Inglese (en)

Per aggiungere altre lingue, creare le cartelle corrispondenti in `/mails/` e copiare i template.

## Gestione Database

### Tabelle Create

#### ps_giftcard
```sql
- id_giftcard: ID univoco
- id_order: ID ordine di acquisto
- id_product: ID prodotto gift card
- code: Codice univoco (es: GC-A1B2C3D4E5F6)
- amount: Importo iniziale
- remaining_amount: Importo residuo
- recipient_email: Email destinatario
- recipient_name: Nome destinatario
- sender_name: Nome mittente
- message: Messaggio personalizzato
- status: Stato (active/used/expired)
- date_add: Data creazione
- date_expiry: Data scadenza
- date_upd: Data ultimo aggiornamento
```

#### ps_product_giftcard
```sql
- id_product: ID prodotto
- is_giftcard: Flag gift card (0/1)
- custom_amounts: Importi personalizzati
- default_image: Immagine predefinita
```

#### ps_giftcard_usage
```sql
- id_giftcard_usage: ID utilizzo
- id_giftcard: ID gift card
- id_order: ID ordine di utilizzo
- amount_used: Importo utilizzato
- date_add: Data utilizzo
```

## Risoluzione Problemi

### Email Non Inviate

1. Verificare la configurazione email di PrestaShop
2. Controllare i log in `/var/logs/`
3. Verificare che i template email esistano nelle lingue corrette

### Gift Card Non Applicata

1. Verificare che la gift card sia attiva
2. Controllare la data di scadenza
3. Verificare che ci sia credito residuo
4. Controllare i cookie del browser

### Form Non Visualizzato

1. Verificare che il modulo sia installato e attivo
2. Verificare che il prodotto sia configurato come gift card
3. Controllare che il tema supporti gli hook utilizzati
4. Svuotare la cache PrestaShop

## Supporto Tecnico

Per assistenza tecnica:
- Email: tech@mlabfactory.com
- Verificare sempre la versione di PrestaShop e PHP
- Allegare eventuali log di errore

## Disinstallazione

Per disinstallare il modulo:

1. Andare in **Moduli > Module Manager**
2. Cercare "Gift Card Product"
3. Cliccare su **Disinstalla**

**ATTENZIONE**: La disinstallazione rimuoverà tutte le tabelle e i dati delle gift card. Fare un backup prima di procedere.

## Note di Sicurezza

- I codici gift card sono generati con hash MD5 univoci
- Le email dei destinatari sono protette da escape SQL
- I template prevengono XSS injection
- Le gift card hanno scadenza automatica

## Changelog

### Versione 1.0.0
- Release iniziale
- Supporto PrestaShop 9
- Funzionalità base gift card
- Email multilingua
- Form personalizzazione completo
