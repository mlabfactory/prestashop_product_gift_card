<?php
namespace Mlab\GiftCard;

use Mlab\GiftCard\Repository\GiftCardRepository;
use Mlab\GiftCard\Repository\ProductGiftCardRepository;
use Mlab\GiftCard\Service\GiftCardGenerator;
use Mlab\GiftCard\Service\GiftCardValidator;
use Mlab\GiftCard\Service\EmailService;
use Mlab\GiftCard\Controller\GiftCardController;
use Mlab\GiftCard\Config\ModuleConfig;

/**
 * Service Container
 * Simple Dependency Injection Container
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */
class ServiceContainer
{
    /** @var array */
    private $services = [];

    /** @var string */
    private $modulePath;

    public function __construct(string $modulePath)
    {
        $this->modulePath = $modulePath;
    }

    /**
     * Get gift card repository
     */
    public function getGiftCardRepository(): GiftCardRepository
    {
        if (!isset($this->services['giftcard_repository'])) {
            $this->services['giftcard_repository'] = new GiftCardRepository();
        }

        return $this->services['giftcard_repository'];
    }

    /**
     * Get product gift card repository
     */
    public function getProductGiftCardRepository(): ProductGiftCardRepository
    {
        if (!isset($this->services['product_giftcard_repository'])) {
            $this->services['product_giftcard_repository'] = new ProductGiftCardRepository();
        }

        return $this->services['product_giftcard_repository'];
    }

    /**
     * Get gift card generator
     */
    public function getGiftCardGenerator(): GiftCardGenerator
    {
        if (!isset($this->services['giftcard_generator'])) {
            $this->services['giftcard_generator'] = new GiftCardGenerator(
                $this->getGiftCardRepository(),
                ModuleConfig::getValidityDays()
            );
        }

        return $this->services['giftcard_generator'];
    }

    /**
     * Get gift card validator
     */
    public function getGiftCardValidator(): GiftCardValidator
    {
        if (!isset($this->services['giftcard_validator'])) {
            $this->services['giftcard_validator'] = new GiftCardValidator();
        }

        return $this->services['giftcard_validator'];
    }

    /**
     * Get email service
     */
    public function getEmailService(): EmailService
    {
        if (!isset($this->services['email_service'])) {
            $this->services['email_service'] = new EmailService($this->modulePath);
        }

        return $this->services['email_service'];
    }

    /**
     * Get gift card controller
     */
    public function getGiftCardController(): GiftCardController
    {
        if (!isset($this->services['giftcard_controller'])) {
            $this->services['giftcard_controller'] = new GiftCardController(
                $this->getGiftCardRepository(),
                $this->getProductGiftCardRepository(),
                $this->getGiftCardGenerator(),
                $this->getGiftCardValidator(),
                $this->getEmailService()
            );
        }

        return $this->services['giftcard_controller'];
    }

    /**
     * Clear all services (useful for testing)
     */
    public function clear(): void
    {
        $this->services = [];
    }
}
