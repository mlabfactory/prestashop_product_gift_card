# Gift Card Module for PrestaShop 9

Module per la gestione di prodotti Gift Card in PrestaShop 9.

## Caratteristiche

- ✅ Creazione di prodotti di tipo Gift Card
- ✅ Selezione taglio personalizzabile dall'utente
- ✅ Invio email automatico con codice Gift Card
- ✅ Messaggio personalizzato opzionale
- ✅ Gestione scadenza Gift Card
- ✅ Tracking utilizzo Gift Card
- ✅ Template email personalizzabili (IT/EN)
- ✅ Interfaccia admin per configurazione prodotti

## Installazione

1. Caricare il modulo nella cartella `modules/mlab_product_gift_card`
2. Installare il modulo dal backoffice PrestaShop
3. Configurare i tagli predefiniti nelle impostazioni del modulo
4. Abilitare la funzionalità Gift Card sui prodotti desiderati

## Configurazione

### Impostazioni Modulo

Accedere a: **Moduli > Gift Card Product > Configura**

- **Abilita Gift Card**: Attiva/disattiva la funzionalità
- **Tagli Disponibili**: Importi predefiniti separati da virgola (es: 25,50,100,150,200)
- **Periodo di Validità**: Numero di giorni di validità della Gift Card

### Configurazione Prodotto

1. Andare nella scheda prodotto
2. Nella sezione "Gift Card Settings":
   - Abilitare "Is Gift Card"
   - Inserire tagli personalizzati (opzionale)
3. Salvare il prodotto

## Utilizzo

### Frontend

Quando un prodotto è configurato come Gift Card, verrà visualizzato un form aggiuntivo con:

- **Selezione taglio**: Dropdown con i tagli disponibili
- **Email destinatario**: Campo obbligatorio per l'invio
- **Nome destinatario**: Campo opzionale
- **Nome mittente**: Campo opzionale
- **Messaggio personalizzato**: Textarea opzionale (max 500 caratteri)

### Processo di Acquisto

1. L'utente seleziona il taglio desiderato
2. Compila i dati del destinatario
3. Aggiunge al carrello e completa l'ordine
4. Viene generata automaticamente una Gift Card con codice univoco
5. Viene inviata una email al destinatario con:
   - Codice Gift Card
   - Importo
   - Messaggio personalizzato
   - Istruzioni per l'utilizzo
   - Data di scadenza

## Database

Il modulo crea 3 tabelle:

### ps_giftcard
Contiene le Gift Card generate con codice, importi, stato, scadenza.

### ps_product_giftcard
Associazione prodotti configurati come Gift Card.

### ps_giftcard_usage
Storico degli utilizzi delle Gift Card.

## Template Email

I template email sono disponibili in:
- `mails/it/giftcard.html` (HTML italiano)
- `mails/it/giftcard.txt` (Testo italiano)
- `mails/en/giftcard.html` (HTML inglese)
- `mails/en/giftcard.txt` (Testo inglese)

## Requisiti

- PrestaShop 9.0.0 o superiore
- PHP 7.4 o superiore
- MySQL 5.6 o superiore

## Supporto

Per supporto tecnico, contattare: tech@mlabfactory.com

## Licenza

© 2024 mlabfactory - Tutti i diritti riservati

## Autore

Sviluppato da mlabfactory
