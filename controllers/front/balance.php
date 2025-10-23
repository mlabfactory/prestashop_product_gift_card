<?php
/**
 * Gift Card Balance Controller
 * Check gift card balance from customer account
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

use Mlab\GiftCard\Exception\GiftCardException;

class Mlab_Product_Gift_CardBalanceModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $auth = true;
    public $authRedirect = 'my-account';

    /**
     * Initialize controller
     */
    public function init()
    {
        parent::init();
        
        // Set page meta
        $this->context->smarty->assign([
            'meta_title' => $this->module->l('My Gift Cards'),
            'meta_description' => $this->module->l('Check your gift card balance'),
        ]);
    }

    /**
     * Set media (CSS/JS)
     */
    public function setMedia()
    {
        parent::setMedia();
        
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/giftcard.css');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/balance.js');
    }

    /**
     * Process page content
     */
    public function initContent()
    {
        parent::initContent();

        $customer = $this->context->customer;
        
        // Get customer's gift cards
        $giftCards = $this->getCustomerGiftCards($customer->email);
        
        // Get gift card by code if checking specific card
        $checkedCard = null;
        $checkError = null;
        
        if (Tools::isSubmit('check_code')) {
            $code = trim(Tools::getValue('giftcard_code'));
            
            if (!empty($code)) {
                try {
                    $controller = $this->module->getContainer()->getGiftCardController();
                    $result = $controller->check($code);
                    
                    if ($result['valid']) {
                        $checkedCard = $result['giftcard'];
                    } else {
                        $checkError = $result['message'];
                    }
                } catch (Exception $e) {
                    $checkError = $e->getMessage();
                }
            } else {
                $checkError = $this->module->l('Please enter a gift card code');
            }
        }

        $this->context->smarty->assign([
            'giftcards' => $giftCards,
            'checked_card' => $checkedCard,
            'check_error' => $checkError,
            'check_url' => $this->context->link->getModuleLink($this->module->name, 'balance'),
            'apply_url' => $this->context->link->getModuleLink($this->module->name, 'apply'),
        ]);

        $this->setTemplate('module:mlab_product_gift_card/views/templates/front/balance.tpl');
    }

    /**
     * Get gift cards received by customer email
     */
    private function getCustomerGiftCards(string $email): array
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'giftcard`
                WHERE `recipient_email` = "' . pSQL($email) . '"
                ORDER BY `date_add` DESC';
        
        $results = Db::getInstance()->executeS($sql);
        
        if (!$results) {
            return [];
        }

        // Format data
        return array_map(function($row) {
            $row['amount_formatted'] = Tools::displayPrice($row['amount']);
            $row['remaining_amount_formatted'] = Tools::displayPrice($row['remaining_amount']);
            $row['is_expired'] = strtotime($row['date_expiry']) < time();
            $row['expiry_date_formatted'] = date('d/m/Y', strtotime($row['date_expiry']));
            $row['date_add_formatted'] = date('d/m/Y', strtotime($row['date_add']));
            $row['status_label'] = $this->getStatusLabel($row['status']);
            $row['status_class'] = $this->getStatusClass($row['status']);
            $row['can_use'] = $row['status'] === 'active' 
                && !$row['is_expired'] 
                && $row['remaining_amount'] > 0;
            
            return $row;
        }, $results);
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        switch ($status) {
            case 'active':
                return $this->module->l('Active');
            case 'used':
                return $this->module->l('Used');
            case 'expired':
                return $this->module->l('Expired');
            default:
                return $status;
        }
    }

    /**
     * Get status CSS class
     */
    private function getStatusClass(string $status): string
    {
        switch ($status) {
            case 'active':
                return 'success';
            case 'used':
                return 'secondary';
            case 'expired':
                return 'danger';
            default:
                return 'info';
        }
    }

    /**
     * Get breadcrumb links
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->module->l('My account'),
            'url' => $this->context->link->getPageLink('my-account'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->module->l('My Gift Cards'),
            'url' => $this->context->link->getModuleLink($this->module->name, 'balance'),
        ];

        return $breadcrumb;
    }
}
