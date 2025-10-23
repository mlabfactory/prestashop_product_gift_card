<?php
/**
 * Gift Card Module for PrestaShop 9
 * MVC Architecture with Service Container
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 * @version 1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

// Carica l'autoloader di Composer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Autoloader alternativo manuale
    spl_autoload_register(function ($className) {
        $prefix = 'Dolcezampa\\SubscriptionCouponModule\\';
        $base_dir = __DIR__ . '/src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            return;
        }

        $relative_class = substr($className, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Mlab\GiftCard\ServiceContainer;
use Mlab\GiftCard\Config\ModuleConfig;
use Mlab\GiftCard\Entity\ProductGiftCard;
use Mlab\GiftCard\Exception\GiftCardException;

class Mlab_Product_Gift_Card extends Module implements WidgetInterface
{
    /** @var ServiceContainer */
    private $container;

    public function __construct()
    {
        $this->name = 'mlab_product_gift_card';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'mlabfactory';
        $this->need_instance = 0;
        
        $this->ps_versions_compliancy = [
            'min' => '9.0.0',
            'max' => '9.99.99'
        ];
        
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('Gift Card Product');
        $this->description = $this->l('Enables gift card products with custom amounts and email delivery');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        // Initialize service container
        $this->container = new ServiceContainer(__DIR__);
    }

    /**
     * Install module
     */
    public function install(): bool
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHooks()
            && ModuleConfig::setDefaults();
    }

    /**
     * Uninstall module
     */
    public function uninstall(): bool
    {
        return parent::uninstall()
            && $this->uninstallDb()
            && ModuleConfig::deleteAll();
    }

    /**
     * Register all hooks
     */
    private function registerHooks(): bool
    {
        $hooks = [
            'displayProductAdditionalInfo',
            'actionValidateOrder',
            'displayAdminProductsExtra',
            'actionProductUpdate',
            'displayShoppingCartFooter',
            'actionCartSave',
            'displayHeader',
            'displayCustomerAccount',
        ];

        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Install database tables
     */
    private function installDb(): bool
    {
        $sql = [];
        
        // Gift cards table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'giftcard` (
            `id_giftcard` int(11) NOT NULL AUTO_INCREMENT,
            `id_order` int(11) NOT NULL,
            `id_product` int(11) NOT NULL,
            `code` varchar(50) NOT NULL,
            `amount` decimal(20,6) NOT NULL,
            `remaining_amount` decimal(20,6) NOT NULL,
            `recipient_email` varchar(255) NOT NULL,
            `recipient_name` varchar(255) DEFAULT NULL,
            `sender_name` varchar(255) DEFAULT NULL,
            `message` text DEFAULT NULL,
            `status` enum(\'active\',\'used\',\'expired\') DEFAULT \'active\',
            `date_add` datetime NOT NULL,
            `date_expiry` datetime DEFAULT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_giftcard`),
            UNIQUE KEY `code` (`code`),
            KEY `id_order` (`id_order`),
            KEY `status` (`status`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';
        
        // Product gift card configuration table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_giftcard` (
            `id_product` int(11) NOT NULL,
            `is_giftcard` tinyint(1) NOT NULL DEFAULT 0,
            `custom_amounts` text DEFAULT NULL,
            `default_image` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';
        
        // Gift card usage history table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'giftcard_usage` (
            `id_giftcard_usage` int(11) NOT NULL AUTO_INCREMENT,
            `id_giftcard` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `amount_used` decimal(20,6) NOT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_giftcard_usage`),
            KEY `id_giftcard` (`id_giftcard`),
            KEY `id_order` (`id_order`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Uninstall database tables
     */
    private function uninstallDb(): bool
    {
        $sql = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'giftcard_usage`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_giftcard`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'giftcard`'
        ];
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Module configuration page
     */
    public function getContent(): string
    {
        $output = '';
        
        if (Tools::isSubmit('submitGiftCardConfig')) {
            ModuleConfig::set(ModuleConfig::KEY_ENABLED, Tools::getValue('MLAB_GIFTCARD_ENABLED'));
            ModuleConfig::set(ModuleConfig::KEY_AMOUNTS, Tools::getValue('MLAB_GIFTCARD_AMOUNTS'));
            ModuleConfig::set(ModuleConfig::KEY_VALIDITY_DAYS, Tools::getValue('MLAB_GIFTCARD_VALIDITY_DAYS'));
            
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        
        return $output . $this->renderForm();
    }

    /**
     * Render configuration form
     */
    protected function renderForm(): string
    {
        $helper = new HelperForm();
        
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGiftCardConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->tpl_vars = [
            'fields_value' => [
                'MLAB_GIFTCARD_ENABLED' => ModuleConfig::get(ModuleConfig::KEY_ENABLED, 1),
                'MLAB_GIFTCARD_AMOUNTS' => ModuleConfig::get(ModuleConfig::KEY_AMOUNTS, '25,50,100,150,200'),
                'MLAB_GIFTCARD_VALIDITY_DAYS' => ModuleConfig::get(ModuleConfig::KEY_VALIDITY_DAYS, 365),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        
        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Get configuration form structure
     */
    protected function getConfigForm(): array
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Gift Card Settings'),
                    'icon' => 'icon-gift'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Gift Cards'),
                        'name' => 'MLAB_GIFTCARD_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Available Amounts'),
                        'name' => 'MLAB_GIFTCARD_AMOUNTS',
                        'desc' => $this->l('Comma-separated values (e.g., 25,50,100,150,200)'),
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Validity Period (days)'),
                        'name' => 'MLAB_GIFTCARD_VALIDITY_DAYS',
                        'desc' => $this->l('Number of days the gift card is valid'),
                        'required' => true
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ]
            ],
        ];
    }

    /**
     * Hook: Display product additional info (gift card form)
     */
    public function hookDisplayProductAdditionalInfo($params): string
    {
        if (!ModuleConfig::isEnabled()) {
            return '';
        }
        
        $idProduct = (int)Tools::getValue('id_product');
        
        if (!$idProduct && isset($params['product'])) {
            $idProduct = is_object($params['product']) 
                ? (int)$params['product']->id 
                : (int)$params['product']['id_product'];
        }
        
        if (!$idProduct) {
            return '';
        }
        
        $productGiftCard = $this->container
            ->getProductGiftCardRepository()
            ->find($idProduct);
        
        if (!$productGiftCard || !$productGiftCard->isGiftCard()) {
            return '';
        }
        
        $amounts = $productGiftCard->hasCustomAmounts() 
            ? $productGiftCard->getCustomAmounts()
            : ModuleConfig::getDefaultAmounts();
        
        $this->context->smarty->assign([
            'amounts' => $amounts,
            'currency' => $this->context->currency,
            'id_product' => $idProduct
        ]);
        
        return $this->display(__FILE__, 'views/templates/front/giftcard_form.tpl');
    }

    /**
     * Hook: Admin products extra (product configuration)
     */
    public function hookDisplayAdminProductsExtra($params): string
    {
        $idProduct = (int)Tools::getValue('id_product');
        
        $productGiftCard = $this->container
            ->getProductGiftCardRepository()
            ->find($idProduct);
        
        $this->context->smarty->assign([
            'is_giftcard' => $productGiftCard ? $productGiftCard->isGiftCard() : false,
            'custom_amounts' => $productGiftCard ? $productGiftCard->getCustomAmountsAsString() : '',
            'id_product' => $idProduct
        ]);
        
        return $this->display(__FILE__, 'views/templates/admin/product_giftcard.tpl');
    }

    /**
     * Hook: Product update (save configuration)
     */
    public function hookActionProductUpdate($params): void
    {
        $idProduct = (int)Tools::getValue('id_product');
        $isGiftcard = (bool)Tools::getValue('is_giftcard');
        $customAmounts = Tools::getValue('giftcard_custom_amounts');
        
        if (!$idProduct) {
            return;
        }
        
        $repository = $this->container->getProductGiftCardRepository();
        $productGiftCard = $repository->find($idProduct);
        
        if (!$productGiftCard) {
            $productGiftCard = new ProductGiftCard($idProduct);
        }
        
        $productGiftCard
            ->setIsGiftCard($isGiftcard)
            ->setCustomAmountsFromString($customAmounts);
        
        $repository->save($productGiftCard);
    }

    /**
     * Hook: Order validation (create gift card)
     */
    public function hookActionValidateOrder($params): void
    {
        if (!ModuleConfig::isEnabled()) {
            return;
        }
        
        $order = $params['order'];
        $cart = $params['cart'];
        $controller = $this->container->getGiftCardController();
        
        foreach ($cart->getProducts() as $product) {
            $productGiftCard = $this->container
                ->getProductGiftCardRepository()
                ->find((int)$product['id_product']);
            
            if ($productGiftCard && $productGiftCard->isGiftCard()) {
                try {
                    $this->createGiftCardFromProduct($controller, $order, $product);
                } catch (GiftCardException $e) {
                    PrestaShopLogger::addLog(
                        'Gift Card Error: ' . $e->getMessage(),
                        3,
                        null,
                        'Order',
                        $order->id
                    );
                }
            }
        }
    }

    /**
     * Create gift card from product in order
     */
    private function createGiftCardFromProduct($controller, $order, $product): void
    {
        $amount = (float)$product['price_wt'];
        $recipientEmail = Tools::getValue('giftcard_recipient_email_' . $product['id_product'], $order->getCustomer()->email);
        $recipientName = Tools::getValue('giftcard_recipient_name_' . $product['id_product'], '');
        $senderName = Tools::getValue('giftcard_sender_name_' . $product['id_product'], 
            $order->getCustomer()->firstname . ' ' . $order->getCustomer()->lastname);
        $message = Tools::getValue('giftcard_message_' . $product['id_product'], '');
        
        $data = [
            'id_order' => (int)$order->id,
            'id_product' => (int)$product['id_product'],
            'amount' => $amount,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName ?: null,
            'sender_name' => $senderName ?: null,
            'message' => $message ?: null,
        ];
        
        $controller->createFromOrder($data, $product);
    }

    /**
     * Hook: Display shopping cart footer (apply gift card)
     */
    public function hookDisplayShoppingCartFooter($params): string
    {
        $giftcardCode = $this->context->cookie->__get('giftcard_code');
        $giftcardAmount = $this->context->cookie->__get('giftcard_amount');

        $this->context->smarty->assign([
            'giftcard_applied' => !empty($giftcardCode),
            'giftcard_code' => $giftcardCode,
            'giftcard_amount' => $giftcardAmount,
            'apply_url' => $this->context->link->getModuleLink($this->name, 'apply')
        ]);

        return $this->display(__FILE__, 'views/templates/front/cart_giftcard.tpl');
    }

    /**
     * Hook: Cart save (maintain gift card in session)
     */
    public function hookActionCartSave($params): void
    {
        // Placeholder for cart save logic if needed
    }

    /**
     * Hook: Display header (load assets)
     */
    public function hookDisplayHeader($params): void
    {
        $this->context->controller->addCSS($this->_path . 'views/css/giftcard.css');
        $this->context->controller->addJS($this->_path . 'views/js/giftcard.js');
    }

    /**
     * Hook: Display customer account (add link to my account)
     */
    public function hookDisplayCustomerAccount($params): string
    {
        return $this->display(__FILE__, 'views/templates/front/my-account-link.tpl');
    }

    /**
     * Widget implementation
     */
    public function renderWidget($hookName, array $configuration): string
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId());
    }

    /**
     * Get widget variables
     */
    public function getWidgetVariables($hookName, array $configuration): array
    {
        return [];
    }
}
