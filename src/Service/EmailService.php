<?php
/**
 * Email Service
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Service;

use Mlab\GiftCard\Entity\GiftCard;
use Configuration;
use Mail;
use Tools;
use Context;

class EmailService
{
    /** @var string */
    private $modulePath;

    /** @var Context */
    private $context;

    public function __construct(string $modulePath)
    {
        $this->modulePath = $modulePath;
        $this->context = Context::getContext();
    }

    /**
     * Send gift card email to recipient
     */
    public function sendGiftCardEmail(GiftCard $giftCard): bool
    {
        $templateVars = $this->prepareTemplateVars($giftCard);
        
        return Mail::Send(
            (int)$this->context->language->id,
            'giftcard',
            $this->getEmailSubject(),
            $templateVars,
            $giftCard->getRecipientEmail(),
            $giftCard->getRecipientName(),
            null,
            null,
            null,
            null,
            $this->modulePath . '/mails/',
            false,
            (int)$this->context->shop->id
        );
    }

    /**
     * Prepare template variables for email
     */
    private function prepareTemplateVars(GiftCard $giftCard): array
    {
        $shopUrl = $this->context->link->getPageLink('index', true);
        
        return [
            '{giftcard_code}' => $giftCard->getCode(),
            '{giftcard_amount}' => Tools::convertPrice($giftCard->getAmount()),
            '{recipient_name}' => $giftCard->getRecipientName() ?: 'Dear Customer',
            '{sender_name}' => $giftCard->getSenderName() ?: '',
            '{message}' => $giftCard->getMessage() ?: '',
            '{expiry_date}' => $giftCard->getDateExpiry() 
                ? $giftCard->getDateExpiry()->format('d/m/Y') 
                : '',
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => $shopUrl,
        ];
    }

    /**
     * Get email subject
     */
    private function getEmailSubject(): string
    {
        $shopName = Configuration::get('PS_SHOP_NAME');
        return sprintf('Your Gift Card from %s', $shopName);
    }

    /**
     * Send expiry reminder email
     */
    public function sendExpiryReminder(GiftCard $giftCard, int $daysRemaining): bool
    {
        $templateVars = $this->prepareTemplateVars($giftCard);
        $templateVars['{days_remaining}'] = $daysRemaining;
        
        $subject = sprintf(
            'Your Gift Card expires in %d days',
            $daysRemaining
        );
        
        return Mail::Send(
            (int)$this->context->language->id,
            'giftcard_expiry_reminder',
            $subject,
            $templateVars,
            $giftCard->getRecipientEmail(),
            $giftCard->getRecipientName(),
            null,
            null,
            null,
            null,
            $this->modulePath . '/mails/',
            false,
            (int)$this->context->shop->id
        );
    }

    /**
     * Test email configuration
     */
    public function testEmail(string $email): bool
    {
        $templateVars = [
            '{test_message}' => 'This is a test email from Gift Card module',
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
        ];
        
        return Mail::Send(
            (int)$this->context->language->id,
            'test',
            'Gift Card Module - Test Email',
            $templateVars,
            $email,
            null,
            null,
            null,
            null,
            null,
            $this->modulePath . '/mails/',
            false,
            (int)$this->context->shop->id
        );
    }
}
