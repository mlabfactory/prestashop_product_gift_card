# Gift Card Module for PrestaShop 9

**Version:** 2.0.0  
**Architecture:** MVC with Service Container  
**Author:** mlabfactory

---

## 🏗️ Architettura MVC Robusta

Questo modulo implementa un'architettura MVC professionale con:

- **Entities** - Oggetti di dominio con logica business
- **Repositories** - Accesso ai dati con pattern Repository
- **Services** - Logica business riutilizzabile
- **Controllers** - Orchestrazione delle operazioni
- **Service Container** - Dependency Injection
- **Config** - Configurazione centralizzata
- **Exceptions** - Gestione errori tipizzata

---

## 📁 Struttura del Progetto

```
mlab_product_gift_card/
├── src/
│   ├── Entity/
│   │   ├── GiftCard.php                    # Entity gift card
│   │   └── ProductGiftCard.php             # Entity product config
│   ├── Repository/
│   │   ├── GiftCardRepository.php          # Repository gift card
│   │   └── ProductGiftCardRepository.php   # Repository product
│   ├── Service/
│   │   ├── GiftCardGenerator.php           # Generazione gift card
│   │   ├── GiftCardValidator.php           # Validazione
│   │   └── EmailService.php                # Invio email
│   ├── Controller/
│   │   └── GiftCardController.php          # Business logic controller
│   ├── Config/
│   │   └── ModuleConfig.php                # Configurazione centralizzata
│   ├── Exception/
│   │   └── GiftCardException.php           # Custom exceptions
│   └── ServiceContainer.php                # DI Container
├── controllers/front/
│   └── apply.php                           # Frontend controller
├── views/
│   ├── templates/                          # Template Smarty
│   ├── css/                                # Stili
│   └── js/                                 # JavaScript
├── mails/                                  # Template email
├── translations/                           # Traduzioni
├── composer.json                           # Composer config
└── mlab_product_gift_card.php             # Main module file
```

---

## 🎯 Design Patterns Implementati

### 1. **Entity Pattern**
```php
use Mlab\GiftCard\\Entity\GiftCard;

$giftCard = new GiftCard();
$giftCard
    ->setCode('GC-123456789012')
    ->setAmount(50.00)
    ->setRecipientEmail('user@example.com');

if ($giftCard->isValid()) {
    // Gift card è valida
}
```

### 2. **Repository Pattern**
```php
use Mlab\GiftCard\\Repository\GiftCardRepository;

$repository = new GiftCardRepository();
$giftCard = $repository->findByCode('GC-123456789012');
$repository->save($giftCard);
```

### 3. **Service Layer**
```php
use Mlab\GiftCard\\Service\GiftCardGenerator;

$generator = new GiftCardGenerator($repository, 365);
$giftCard = $generator->create([
    'id_order' => 1,
    'id_product' => 10,
    'amount' => 100.00,
    'recipient_email' => 'user@example.com'
]);
```

### 4. **Dependency Injection**
```php
// Nel modulo principale
$container = $this->getContainer();
$controller = $container->getGiftCardController();
$giftCard = $controller->createFromOrder($data, $product);
```

---

## 🚀 Utilizzo

### Accesso al Service Container

```php
// Nel modulo
$container = $this->module->getContainer();

// Ottenere servizi
$giftCardController = $container->getGiftCardController();
$giftCardRepository = $container->getGiftCardRepository();
$emailService = $container->getEmailService();
$validator = $container->getGiftCardValidator();
$generator = $container->getGiftCardGenerator();
```

### Creare una Gift Card

```php
$controller = $this->module->getContainer()->getGiftCardController();

$data = [
    'id_order' => $orderId,
    'id_product' => $productId,
    'amount' => 100.00,
    'recipient_email' => 'recipient@example.com',
    'recipient_name' => 'John Doe',
    'sender_name' => 'Jane Smith',
    'message' => 'Happy Birthday!'
];

try {
    $giftCard = $controller->createFromOrder($data, $productData);
    // Gift card creata e email inviata
} catch (GiftCardException $e) {
    // Gestione errore
    echo $e->getMessage();
}
```

### Validare una Gift Card

```php
$controller = $this->module->getContainer()->getGiftCardController();
$result = $controller->check('GC-123456789012');

if ($result['valid']) {
    $amount = $result['giftcard']['remaining_amount'];
    // Gift card valida
} else {
    echo $result['message'];
}
```

### Applicare una Gift Card

```php
$controller = $this->module->getContainer()->getGiftCardController();

try {
    $result = $controller->apply('GC-123456789012', $orderAmount);
    $discountAmount = $result['discount_amount'];
    $remainingAfter = $result['remaining_after'];
} catch (GiftCardException $e) {
    echo $e->getMessage();
}
```

---

## 🔧 Configurazione

### Via ModuleConfig Class

```php
use Mlab\GiftCard\\Config\ModuleConfig;

// Get configuration
$isEnabled = ModuleConfig::isEnabled();
$amounts = ModuleConfig::getDefaultAmounts(); // [25, 50, 100, 150, 200]
$validityDays = ModuleConfig::getValidityDays(); // 365

// Set configuration
ModuleConfig::set(ModuleConfig::KEY_ENABLED, true);
ModuleConfig::set(ModuleConfig::KEY_AMOUNTS, '30,60,90,120');
ModuleConfig::set(ModuleConfig::KEY_VALIDITY_DAYS, 730);

// Get custom key
$value = ModuleConfig::get('CUSTOM_KEY', 'default_value');
```

---

## 📦 Installazione

### 1. Installare Dipendenze

```bash
cd modules/mlab_product_gift_card
composer install --no-dev --optimize-autoloader
```

### 2. Installare Modulo

1. Andare in **Moduli > Module Manager**
2. Cercare "Gift Card Product"
3. Cliccare **Installa**

### 3. Configurare

1. **Moduli > Gift Card Product > Configura**
2. Impostare importi predefiniti
3. Impostare periodo validità
4. Salvare

---

## 🧪 Testing

### Unit Testing Setup

```bash
composer require --dev phpunit/phpunit
```

### Esempio Test

```php
use PHPUnit\Framework\TestCase;
use Mlab\GiftCard\\Entity\GiftCard;

class GiftCardTest extends TestCase
{
    public function testCreateGiftCard()
    {
        $giftCard = new GiftCard();
        $giftCard->setAmount(100.00);
        
        $this->assertEquals(100.00, $giftCard->getAmount());
        $this->assertEquals(100.00, $giftCard->getRemainingAmount());
        $this->assertTrue($giftCard->hasRemainingAmount());
    }
    
    public function testUseGiftCard()
    {
        $giftCard = new GiftCard();
        $giftCard->setAmount(100.00);
        $giftCard->use(30.00);
        
        $this->assertEquals(70.00, $giftCard->getRemainingAmount());
    }
}
```

---

## 🎨 Estendibilità

### Creare un Nuovo Service

```php
namespace Mlab\GiftCard\\Service;

class GiftCardAnalytics
{
    private $repository;
    
    public function __construct(GiftCardRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function getTotalSold(): float
    {
        $stats = $this->repository->getStatistics();
        return (float)$stats['total_sold'];
    }
}
```

### Aggiungere al Service Container

```php
// In ServiceContainer.php
public function getGiftCardAnalytics(): GiftCardAnalytics
{
    if (!isset($this->services['giftcard_analytics'])) {
        $this->services['giftcard_analytics'] = new GiftCardAnalytics(
            $this->getGiftCardRepository()
        );
    }
    return $this->services['giftcard_analytics'];
}
```

---

## 🔍 Debug

### Enable Debug Mode

```php
// In mlab_product_gift_card.php
public function __construct()
{
    // ...
    if (_PS_MODE_DEV_) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }
}
```

### Log Errori

```php
use PrestaShopLogger;

try {
    $controller->createFromOrder($data, $product);
} catch (GiftCardException $e) {
    PrestaShopLogger::addLog(
        'Gift Card Error: ' . $e->getMessage(),
        3,
        null,
        'GiftCard',
        $giftCard->getId()
    );
}
```

---

## 📚 Documentazione Completa

- **INSTALLATION.md** - Guida installazione dettagliata
- **FEATURES.md** - Lista completa funzionalità
- **USAGE_EXAMPLES.md** - Esempi pratici
- **QUICK_REFERENCE.md** - Riferimento rapido
- **CHANGELOG.md** - Storico versioni

---

## 🛠️ Requisiti

- PrestaShop 9.0.0+
- PHP 7.4+
- Composer (per autoloading)
- MySQL 5.6+

---

## 🤝 Contribuire

### Coding Standards

- PSR-4 Autoloading
- PSR-12 Coding Style
- Type hinting obbligatorio
- DocBlocks completi
- SOLID principles

### Git Workflow

```bash
git checkout -b feature/my-feature
# Make changes
git commit -m "feat: add new feature"
git push origin feature/my-feature
```

---

## 📄 Licenza

Proprietary - © 2024 mlabfactory

---

## 📞 Supporto

**Email:** tech@mlabfactory.com  
**Documentazione:** Consulta i file .md nella root  
**Issues:** Contattare via email

---

## ✨ Caratteristiche Principali

- ✅ Architettura MVC professionale
- ✅ Dependency Injection Container
- ✅ Repository Pattern per database
- ✅ Service Layer per business logic
- ✅ Entity con logica di dominio
- ✅ Type hinting completo
- ✅ Exception handling robusto
- ✅ PSR-4 Autoloading
- ✅ Facilmente testabile
- ✅ Altamente estendibile
- ✅ SOLID principles
- ✅ Clean Code

---

**Developed with ❤️ by mlabfactory**
