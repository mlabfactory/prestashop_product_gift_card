<?php
/**
 * Gift Card Apply Controller (Frontend)
 * Uses Service Container and MVC Architecture
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

use Mlab\GiftCard\Exception\GiftCardException;

class Mlab_Product_Gift_CardApplyModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * Initialize controller
     */
    public function init()
    {
        parent::init();
        $this->ajax = true;
    }

    /**
     * Process POST request
     */
    public function postProcess()
    {
        if (!$this->ajax) {
            return;
        }

        $action = Tools::getValue('action');

        try {
            switch ($action) {
                case 'apply':
                    $this->applyGiftCard();
                    break;
                case 'remove':
                    $this->removeGiftCard();
                    break;
                case 'check':
                    $this->checkGiftCard();
                    break;
                default:
                    $this->ajaxError($this->module->l('Invalid action'));
            }
        } catch (Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Apply gift card to cart
     */
    protected function applyGiftCard()
    {
        $code = trim(Tools::getValue('code'));

        if (empty($code)) {
            $this->ajaxError($this->module->l('Please enter a gift card code'));
            return;
        }

        try {
            $controller = $this->module->getContainer()->getGiftCardController();
            $result = $controller->check($code);

            if (!$result['valid']) {
                $this->ajaxError($result['message']);
                return;
            }

            // Store in session
            $this->context->cookie->__set('giftcard_code', $code);
            $this->context->cookie->__set('giftcard_amount', $result['giftcard']['remaining_amount']);
            $this->context->cookie->write();

            $this->ajaxSuccess(
                $this->module->l('Gift card applied successfully!'),
                [
                    'amount' => Tools::displayPrice($result['giftcard']['remaining_amount'])
                ]
            );
        } catch (GiftCardException $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Remove gift card from cart
     */
    protected function removeGiftCard()
    {
        $this->context->cookie->__unset('giftcard_code');
        $this->context->cookie->__unset('giftcard_amount');
        $this->context->cookie->write();

        $this->ajaxSuccess($this->module->l('Gift card removed'));
    }

    /**
     * Check gift card validity
     */
    protected function checkGiftCard()
    {
        $code = trim(Tools::getValue('code'));

        if (empty($code)) {
            $this->ajaxError($this->module->l('Please enter a gift card code'));
            return;
        }

        try {
            $controller = $this->module->getContainer()->getGiftCardController();
            $result = $controller->check($code);

            if (!$result['valid']) {
                $this->ajaxError($result['message']);
                return;
            }

            $this->ajaxSuccess('Gift card is valid', [
                'giftcard' => [
                    'code' => $result['giftcard']['code'],
                    'amount' => Tools::displayPrice($result['giftcard']['amount']),
                    'remaining_amount' => Tools::displayPrice($result['giftcard']['remaining_amount']),
                    'status' => $result['giftcard']['status'],
                    'expiry_date' => $result['giftcard']['expiry_date'],
                ]
            ]);
        } catch (GiftCardException $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Send success JSON response
     */
    protected function ajaxSuccess(string $message, array $data = [])
    {
        $response = array_merge([
            'success' => true,
            'message' => $message,
        ], $data);

        $this->ajaxRender(json_encode($response));
    }

    /**
     * Send error JSON response
     */
    protected function ajaxError(string $message)
    {
        $this->ajaxRender(json_encode([
            'success' => false,
            'message' => $message,
        ]));
    }

     /**
     * @param string|null $value
     * @param string|null $controller
     * @param string|null $method
     *
     * @throws PrestaShopException
     */
    protected function ajaxRender($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        echo $value;
        exit;
    }
}
