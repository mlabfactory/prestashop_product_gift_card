<?php
/**
 * Gift Card Generator Service
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Service;

use Mlab\GiftCard\Entity\GiftCard;
use Mlab\GiftCard\Repository\GiftCardRepository;
use DateTime;

class GiftCardGenerator
{
    const CODE_PREFIX = 'GC-';
    const CODE_LENGTH = 12;

    /** @var GiftCardRepository */
    private $repository;

    /** @var int */
    private $validityDays;

    public function __construct(GiftCardRepository $repository, int $validityDays = 365)
    {
        $this->repository = $repository;
        $this->validityDays = $validityDays;
    }

    /**
     * Generate unique gift card code
     */
    public function generateCode(): string
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $code = $this->createCode();
            $exists = $this->repository->codeExists($code);
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Failed to generate unique gift card code');
            }
        } while ($exists);

        return $code;
    }

    /**
     * Create gift card code
     */
    private function createCode(): string
    {
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, self::CODE_LENGTH));
        return self::CODE_PREFIX . $random;
    }

    /**
     * Generate gift card from order data
     */
    public function generate(array $data): GiftCard
    {
        $giftCard = new GiftCard();
        
        $giftCard
            ->setCode($this->generateCode())
            ->setIdOrder($data['id_order'])
            ->setIdProduct($data['id_product'])
            ->setAmount($data['amount'])
            ->setRecipientEmail($data['recipient_email'])
            ->setRecipientName($data['recipient_name'] ?? null)
            ->setSenderName($data['sender_name'] ?? null)
            ->setMessage($data['message'] ?? null)
            ->setStatus(GiftCard::STATUS_ACTIVE);

        // Calculate expiry date
        $expiryDate = new DateTime();
        $expiryDate->modify('+' . $this->validityDays . ' days');
        $giftCard->setDateExpiry($expiryDate);

        return $giftCard;
    }

    /**
     * Create and save gift card
     */
    public function create(array $data): GiftCard
    {
        $giftCard = $this->generate($data);
        
        if (!$this->repository->save($giftCard)) {
            throw new \RuntimeException('Failed to save gift card');
        }

        return $giftCard;
    }
}
