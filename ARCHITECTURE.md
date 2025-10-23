# Architettura MVC - Modulo Gift Card

## 📐 Panoramica Architetturale

Il modulo è strutturato seguendo i principi **SOLID** e utilizza un'architettura **MVC robusta** con **Dependency Injection**.

---

## 🏛️ Layers dell'Architettura

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
│  ┌────────────────┐  ┌─────────────┐  ┌──────────────┐ │
│  │  Module Class  │  │  Templates  │  │  Controllers │ │
│  │  (Main File)   │  │  (Smarty)   │  │  (Frontend)  │ │
│  └────────────────┘  └─────────────┘  └──────────────┘ │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   SERVICE CONTAINER                      │
│              (Dependency Injection)                      │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   BUSINESS LOGIC LAYER                   │
│  ┌────────────┐  ┌───────────┐  ┌────────────────────┐ │
│  │Controllers │  │  Services │  │  Validators        │ │
│  │(Business)  │  │(Logic)    │  │  (Rules)           │ │
│  └────────────┘  └───────────┘  └────────────────────┘ │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                      DOMAIN LAYER                        │
│  ┌────────────────┐            ┌───────────────────┐   │
│  │   Entities     │            │   Exceptions      │   │
│  │ (Domain Logic) │            │  (Error Handling) │   │
│  └────────────────┘            └───────────────────┘   │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                  DATA ACCESS LAYER                       │
│  ┌───────────────────┐       ┌────────────────────┐    │
│  │   Repositories    │  ←──→ │   Database         │    │
│  │  (Data Access)    │       │   (MySQL)          │    │
│  └───────────────────┘       └────────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

---

## 📦 Componenti Principali

### 1. **Entities** (`src/Entity/`)

Oggetti del dominio con logica business interna.

#### GiftCard.php
```php
class GiftCard
{
    // Properties
    private $id;
    private $code;
    private $amount;
    private $remainingAmount;
    // ...

    // Business methods
    public function isValid(): bool;
    public function isExpired(): bool;
    public function use(float $amount): void;
    
    // Conversion
    public function toArray(): array;
    public static function fromArray(array $data): self;
}
```

**Responsabilità:**
- Rappresentare i dati della gift card
- Contenere logica di validazione interna
- Gestire stato e transizioni
- Conversione da/verso array

#### ProductGiftCard.php
```php
class ProductGiftCard
{
    private $idProduct;
    private $isGiftCard;
    private $customAmounts;
    
    public function hasCustomAmounts(): bool;
    public function getCustomAmountsAsString(): string;
}
```

**Responsabilità:**
- Configurazione prodotti gift card
- Gestione importi personalizzati
- Validazione configurazione

---

### 2. **Repositories** (`src/Repository/`)

Pattern Repository per accesso ai dati.

#### GiftCardRepository.php
```php
class GiftCardRepository
{
    public function find(int $id): ?GiftCard;
    public function findByCode(string $code): ?GiftCard;
    public function findByOrder(int $idOrder): array;
    public function findActive(): array;
    public function save(GiftCard $giftCard): bool;
    public function delete(int $id): bool;
    public function getStatistics(): array;
}
```

**Responsabilità:**
- CRUD operations su gift card
- Query complesse
- Conversione DB ↔ Entity
- Statistiche e aggregazioni

#### ProductGiftCardRepository.php
```php
class ProductGiftCardRepository
{
    public function find(int $idProduct): ?ProductGiftCard;
    public function findAllGiftCards(): array;
    public function save(ProductGiftCard $productGiftCard): bool;
    public function isGiftCard(int $idProduct): bool;
}
```

**Responsabilità:**
- Gestione configurazioni prodotti
- Query su prodotti gift card

---

### 3. **Services** (`src/Service/`)

Logica business riutilizzabile.

#### GiftCardGenerator.php
```php
class GiftCardGenerator
{
    public function generateCode(): string;
    public function generate(array $data): GiftCard;
    public function create(array $data): GiftCard;
}
```

**Responsabilità:**
- Generazione codici univoci
- Creazione gift card
- Calcolo scadenza

#### GiftCardValidator.php
```php
class GiftCardValidator
{
    public function validate(GiftCard $giftCard): void;
    public function canUseAmount(GiftCard $giftCard, float $amount): bool;
    public function validateData(array $data): array;
    public function validateEmail(string $email): bool;
}
```

**Responsabilità:**
- Validazione gift card
- Validazione dati input
- Regole business

#### EmailService.php
```php
class EmailService
{
    public function sendGiftCardEmail(GiftCard $giftCard): bool;
    public function sendExpiryReminder(GiftCard $giftCard, int $days): bool;
    private function prepareTemplateVars(GiftCard $giftCard): array;
}
```

**Responsabilità:**
- Invio email gift card
- Preparazione template
- Email di reminder

---

### 4. **Controllers** (`src/Controller/`)

Orchestrazione operazioni business.

#### GiftCardController.php
```php
class GiftCardController
{
    // Dependencies
    private $giftCardRepository;
    private $productGiftCardRepository;
    private $generator;
    private $validator;
    private $emailService;
    
    // Business operations
    public function createFromOrder(array $orderData, array $productData): GiftCard;
    public function apply(string $code, float $orderAmount): array;
    public function use(string $code, int $idOrder, float $amount): bool;
    public function check(string $code): array;
    public function getStatistics(): array;
}
```

**Responsabilità:**
- Coordinare operazioni complesse
- Utilizzare services e repositories
- Gestire transazioni
- Logging errori

---

### 5. **Service Container** (`src/ServiceContainer.php`)

Dependency Injection Container semplice.

```php
class ServiceContainer
{
    private $services = [];
    private $modulePath;
    
    public function getGiftCardRepository(): GiftCardRepository;
    public function getGiftCardController(): GiftCardController;
    public function getEmailService(): EmailService;
    // ... altri servizi
}
```

**Responsabilità:**
- Creazione e gestione servizi
- Dependency injection
- Singleton pattern per servizi
- Lazy loading

---

### 6. **Configuration** (`src/Config/`)

Configurazione centralizzata.

#### ModuleConfig.php
```php
class ModuleConfig
{
    const CONFIG_PREFIX = 'MLAB_GIFTCARD_';
    
    public static function get(string $key, $default = null);
    public static function set(string $key, $value): bool;
    public static function isEnabled(): bool;
    public static function getDefaultAmounts(): array;
    public static function getValidityDays(): int;
}
```

**Responsabilità:**
- Accesso configurazione
- Valori predefiniti
- Getter tipizzati

---

### 7. **Exceptions** (`src/Exception/`)

Gestione errori tipizzata.

#### GiftCardException.php
```php
class GiftCardException extends \Exception
{
}
```

**Utilizzo:**
```php
try {
    $controller->apply($code, $amount);
} catch (GiftCardException $e) {
    // Handle specific gift card errors
}
```

---

## 🔄 Flusso Operazioni

### Creazione Gift Card

```
┌──────────────┐
│   Order      │ Hook: actionValidateOrder
│  Confirmed   │
└───────┬──────┘
        │
        ▼
┌──────────────────────────────────────┐
│  Module: hookActionValidateOrder()   │
│  - Check if product is gift card     │
│  - Extract form data                 │
└──────────────┬───────────────────────┘
               │
               ▼
┌──────────────────────────────────────┐
│  GiftCardController::createFromOrder │
│  - Validate data                     │
│  - Call generator                    │
│  - Send email                        │
└──────────────┬───────────────────────┘
               │
        ┌──────┴───────┐
        │              │
        ▼              ▼
┌───────────────┐  ┌──────────────┐
│   Generator   │  │  Validator   │
│ - Generate    │  │ - Check data │
│   code        │  │ - Validate   │
│ - Create      │  │   email      │
│   entity      │  └──────────────┘
│ - Save to DB  │
└───────┬───────┘
        │
        ▼
┌───────────────────┐
│  EmailService     │
│ - Prepare vars    │
│ - Send email      │
└───────────────────┘
```

### Applicazione Gift Card

```
┌──────────────┐
│   Customer   │ Enter code in cart
│  Enters Code │
└───────┬──────┘
        │
        ▼
┌────────────────────────────────┐
│  ApplyController::postProcess  │
│  - Get code from form          │
│  - Call GiftCardController     │
└───────┬────────────────────────┘
        │
        ▼
┌────────────────────────────────┐
│  GiftCardController::check()   │
│  - Find by code                │
│  - Validate                    │
│  - Return result               │
└───────┬────────────────────────┘
        │
    ┌───┴───┐
    │       │
    ▼       ▼
┌──────┐  ┌──────────┐
│ Repo │  │Validator │
│ Find │  │ Check    │
│ Code │  │ Valid    │
└──────┘  └──────────┘
```

---

## 🎯 Principi SOLID Applicati

### **S** - Single Responsibility Principle
Ogni classe ha una sola responsabilità:
- `GiftCard` → Gestione dati gift card
- `GiftCardRepository` → Accesso database
- `EmailService` → Invio email
- `GiftCardGenerator` → Generazione codici

### **O** - Open/Closed Principle
Classi aperte all'estensione, chiuse alle modifiche:
- Nuovi services facilmente aggiungibili al container
- Entities estendibili senza modificare codice esistente

### **L** - Liskov Substitution Principle
Interfacce e contracts rispettati:
- Repository implementano pattern uniforme
- Services intercambiabili

### **I** - Interface Segregation Principle
Interfacce specifiche per ogni ruolo:
- Repository vs Service vs Controller
- Nessuna dipendenza da metodi non usati

### **D** - Dependency Inversion Principle
Dipendenze tramite costruttore:
```php
public function __construct(
    GiftCardRepository $repository,
    GiftCardValidator $validator,
    EmailService $emailService
) {
    // Dependencies injected
}
```

---

## 📊 Vantaggi dell'Architettura

### ✅ Testabilità
- Ogni componente testabile isolatamente
- Mock facili tramite DI
- Unit test semplici

### ✅ Manutenibilità
- Codice organizzato e leggibile
- Responsabilità ben definite
- Facile debugging

### ✅ Estendibilità
- Nuovi services aggiunti facilmente
- Override di comportamenti
- Plugin pattern

### ✅ Riusabilità
- Services riutilizzabili
- Logica centralizzata
- No codice duplicato

### ✅ Scalabilità
- Performance ottimizzate
- Cache implementabile
- Query efficienti

---

## 🔧 Pattern Aggiuntivi

### Factory Pattern
```php
$giftCard = GiftCard::fromArray($dbData);
```

### Builder Pattern (implicito)
```php
$giftCard = new GiftCard();
$giftCard
    ->setCode($code)
    ->setAmount($amount)
    ->setRecipientEmail($email);
```

### Repository Pattern
```php
$repository->find($id);
$repository->findByCode($code);
$repository->save($entity);
```

### Service Locator
```php
$container->getGiftCardController();
$container->getEmailService();
```

---

## 📝 Best Practices

1. **Type Hinting** ovunque possibile
2. **DocBlocks** completi
3. **Return types** espliciti
4. **Exceptions** per gestione errori
5. **Immutability** dove possibile
6. **Dependency Injection** sempre
7. **Single Responsibility** per ogni classe
8. **Tests** per logica critica

---

## 🚀 Performance

- **Lazy Loading** dei servizi
- **Repository** con query ottimizzate
- **Caching** implementabile facilmente
- **Indexes** database su colonne chiave
- **Prepared Statements** per sicurezza

---

**Architettura progettata per scalabilità e manutenibilità**

© 2024 mlabfactory
