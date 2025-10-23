<?php
/**
 * Gift Card Controller
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Controller;

use Mlab\GiftCard\Entity\GiftCard;
use Mlab\GiftCard\Repository\GiftCardRepository;
use Mlab\GiftCard\Repository\ProductGiftCardRepository;
use Mlab\GiftCard\Service\GiftCardGenerator;
use Mlab\GiftCard\Service\GiftCardValidator;
use Mlab\GiftCard\Service\EmailService;
use Mlab\GiftCard\Exception\GiftCardException;

class GiftCardController
{
    /** @var GiftCardRepository */
    private $giftCardRepository;

    /** @var ProductGiftCardRepository */
    private $productGiftCardRepository;

    /** @var GiftCardGenerator */
    private $generator;

    /** @var GiftCardValidator */
    private $validator;

    /** @var EmailService */
    private $emailService;

    public function __construct(
        GiftCardRepository $giftCardRepository,
        ProductGiftCardRepository $productGiftCardRepository,
        GiftCardGenerator $generator,
        GiftCardValidator $validator,
        EmailService $emailService
    ) {
        $this->giftCardRepository = $giftCardRepository;
        $this->productGiftCardRepository = $productGiftCardRepository;
        $this->generator = $generator;
        $this->validator = $validator;
        $this->emailService = $emailService;
    }

    /**
     * Create gift card from order
     */
    public function createFromOrder(array $orderData, array $productData): GiftCard
    {
        // Validate data
        $errors = $this->validator->validateData($orderData);
        if (!empty($errors)) {
            throw new GiftCardException(implode(', ', $errors));
        }

        // Generate gift card
        $giftCard = $this->generator->create($orderData);

        // Send email
        $this->emailService->sendGiftCardEmail($giftCard);

        return $giftCard;
    }

    /**
     * Apply gift card to order
     */
    public function apply(string $code, float $orderAmount): array
    {
        $giftCard = $this->giftCardRepository->findByCode($code);

        if (!$giftCard) {
            throw new GiftCardException('Gift card not found');
        }

        $this->validator->validate($giftCard);

        $discountAmount = min($orderAmount, $giftCard->getRemainingAmount());

        return [
            'giftcard' => $giftCard,
            'discount_amount' => $discountAmount,
            'remaining_after' => $giftCard->getRemainingAmount() - $discountAmount,
        ];
    }

    /**
     * Use gift card for order
     */
    public function use(string $code, int $idOrder, float $amount): bool
    {
        $giftCard = $this->giftCardRepository->findByCode($code);

        if (!$giftCard) {
            throw new GiftCardException('Gift card not found');
        }

        $this->validator->validate($giftCard);

        if ($amount > $giftCard->getRemainingAmount()) {
            throw new GiftCardException('Amount exceeds remaining balance');
        }

        // Use the gift card
        $giftCard->use($amount);

        // Save updated gift card
        if (!$this->giftCardRepository->save($giftCard)) {
            throw new GiftCardException('Failed to update gift card');
        }

        // Record usage (could be done in a separate UsageRepository)
        $this->recordUsage($giftCard->getId(), $idOrder, $amount);

        return true;
    }

    /**
     * Check gift card validity
     */
    public function check(string $code): array
    {
        $giftCard = $this->giftCardRepository->findByCode($code);

        if (!$giftCard) {
            return [
                'valid' => false,
                'message' => 'Gift card not found',
            ];
        }

        try {
            $this->validator->validate($giftCard);
            
            return [
                'valid' => true,
                'giftcard' => [
                    'code' => $giftCard->getCode(),
                    'amount' => $giftCard->getAmount(),
                    'remaining_amount' => $giftCard->getRemainingAmount(),
                    'status' => $giftCard->getStatus(),
                    'expiry_date' => $giftCard->getDateExpiry() 
                        ? $giftCard->getDateExpiry()->format('Y-m-d') 
                        : null,
                ],
            ];
        } catch (GiftCardException $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get gift card by code
     */
    public function getByCode(string $code): ?GiftCard
    {
        return $this->giftCardRepository->findByCode($code);
    }

    /**
     * Get gift cards by order
     */
    public function getByOrder(int $idOrder): array
    {
        return $this->giftCardRepository->findByOrder($idOrder);
    }

    /**
     * Get gift cards by recipient email
     */
    public function getByRecipientEmail(string $email): array
    {
        return $this->giftCardRepository->findByRecipientEmail($email);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return $this->giftCardRepository->getStatistics();
    }

    /**
     * Update expired gift cards
     */
    public function updateExpired(): int
    {
        return $this->giftCardRepository->updateExpiredCards();
    }

    /**
     * Record gift card usage (simplified - should use dedicated repository)
     */
    private function recordUsage(int $idGiftCard, int $idOrder, float $amount): bool
    {
        return \Db::getInstance()->insert('giftcard_usage', [
            'id_giftcard' => (int)$idGiftCard,
            'id_order' => (int)$idOrder,
            'amount_used' => (float)$amount,
            'date_add' => date('Y-m-d H:i:s'),
        ]);
    }
}
