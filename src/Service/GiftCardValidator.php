<?php
/**
 * Gift Card Validator Service
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Service;

use Mlab\GiftCard\Entity\GiftCard;
use Mlab\GiftCard\Exception\GiftCardException;
use DateTime;

class GiftCardValidator
{
    /**
     * Validate gift card for use
     * 
     * @throws GiftCardException
     */
    public function validate(GiftCard $giftCard): void
    {
        if ($giftCard->getStatus() !== GiftCard::STATUS_ACTIVE) {
            throw new GiftCardException('Gift card is not active');
        }

        if ($giftCard->isExpired()) {
            throw new GiftCardException('Gift card has expired');
        }

        if (!$giftCard->hasRemainingAmount()) {
            throw new GiftCardException('Gift card has no remaining balance');
        }
    }

    /**
     * Check if gift card can be used for amount
     */
    public function canUseAmount(GiftCard $giftCard, float $amount): bool
    {
        try {
            $this->validate($giftCard);
            return $amount <= $giftCard->getRemainingAmount();
        } catch (GiftCardException $e) {
            return false;
        }
    }

    /**
     * Validate gift card data before creation
     */
    public function validateData(array $data): array
    {
        $errors = [];

        if (empty($data['amount']) || $data['amount'] <= 0) {
            $errors[] = 'Amount must be greater than zero';
        }

        if (empty($data['recipient_email'])) {
            $errors[] = 'Recipient email is required';
        } elseif (!filter_var($data['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid recipient email address';
        }

        if (empty($data['id_order']) || $data['id_order'] <= 0) {
            $errors[] = 'Valid order ID is required';
        }

        if (empty($data['id_product']) || $data['id_product'] <= 0) {
            $errors[] = 'Valid product ID is required';
        }

        if (!empty($data['message']) && strlen($data['message']) > 500) {
            $errors[] = 'Message cannot exceed 500 characters';
        }

        return $errors;
    }

    /**
     * Validate email address
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate gift card code format
     */
    public function validateCodeFormat(string $code): bool
    {
        return preg_match('/^GC-[A-Z0-9]{12}$/', $code) === 1;
    }
}
