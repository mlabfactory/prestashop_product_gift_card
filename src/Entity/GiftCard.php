<?php
/**
 * Gift Card Entity
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Entity;

use DateTime;

class GiftCard
{
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    /** @var int|null */
    private $id;

    /** @var int */
    private $idOrder;

    /** @var int */
    private $idProduct;

    /** @var string */
    private $code;

    /** @var float */
    private $amount;

    /** @var float */
    private $remainingAmount;

    /** @var string */
    private $recipientEmail;

    /** @var string|null */
    private $recipientName;

    /** @var string|null */
    private $senderName;

    /** @var string|null */
    private $message;

    /** @var string */
    private $status;

    /** @var DateTime */
    private $dateAdd;

    /** @var DateTime|null */
    private $dateExpiry;

    /** @var DateTime */
    private $dateUpd;

    public function __construct()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->dateAdd = new DateTime();
        $this->dateUpd = new DateTime();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdOrder(): int
    {
        return $this->idOrder;
    }

    public function getIdProduct(): int
    {
        return $this->idProduct;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getRemainingAmount(): float
    {
        return $this->remainingAmount;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDateAdd(): DateTime
    {
        return $this->dateAdd;
    }

    public function getDateExpiry(): ?DateTime
    {
        return $this->dateExpiry;
    }

    public function getDateUpd(): DateTime
    {
        return $this->dateUpd;
    }

    // Setters
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setIdOrder(int $idOrder): self
    {
        $this->idOrder = $idOrder;
        return $this;
    }

    public function setIdProduct(int $idProduct): self
    {
        $this->idProduct = $idProduct;
        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        $this->remainingAmount = $amount;
        return $this;
    }

    public function setRemainingAmount(float $remainingAmount): self
    {
        $this->remainingAmount = $remainingAmount;
        return $this;
    }

    public function setRecipientEmail(string $recipientEmail): self
    {
        $this->recipientEmail = $recipientEmail;
        return $this;
    }

    public function setRecipientName(?string $recipientName): self
    {
        $this->recipientName = $recipientName;
        return $this;
    }

    public function setSenderName(?string $senderName): self
    {
        $this->senderName = $senderName;
        return $this;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_USED, self::STATUS_EXPIRED])) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->status = $status;
        return $this;
    }

    public function setDateAdd(DateTime $dateAdd): self
    {
        $this->dateAdd = $dateAdd;
        return $this;
    }

    public function setDateExpiry(?DateTime $dateExpiry): self
    {
        $this->dateExpiry = $dateExpiry;
        return $this;
    }

    public function setDateUpd(DateTime $dateUpd): self
    {
        $this->dateUpd = $dateUpd;
        return $this;
    }

    // Business methods
    public function isValid(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->remainingAmount > 0
            && ($this->dateExpiry === null || $this->dateExpiry > new DateTime());
    }

    public function isExpired(): bool
    {
        return $this->dateExpiry !== null && $this->dateExpiry < new DateTime();
    }

    public function hasRemainingAmount(): bool
    {
        return $this->remainingAmount > 0;
    }

    public function use(float $amount): void
    {
        if ($amount > $this->remainingAmount) {
            throw new \InvalidArgumentException('Amount exceeds remaining amount');
        }

        $this->remainingAmount -= $amount;
        
        if ($this->remainingAmount <= 0) {
            $this->status = self::STATUS_USED;
        }

        $this->dateUpd = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id_giftcard' => $this->id,
            'id_order' => $this->idOrder,
            'id_product' => $this->idProduct,
            'code' => $this->code,
            'amount' => $this->amount,
            'remaining_amount' => $this->remainingAmount,
            'recipient_email' => $this->recipientEmail,
            'recipient_name' => $this->recipientName,
            'sender_name' => $this->senderName,
            'message' => $this->message,
            'status' => $this->status,
            'date_add' => $this->dateAdd->format('Y-m-d H:i:s'),
            'date_expiry' => $this->dateExpiry ? $this->dateExpiry->format('Y-m-d H:i:s') : null,
            'date_upd' => $this->dateUpd->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        $giftCard = new self();
        
        if (isset($data['id_giftcard'])) {
            $giftCard->setId((int)$data['id_giftcard']);
        }
        
        $giftCard
            ->setIdOrder((int)$data['id_order'])
            ->setIdProduct((int)$data['id_product'])
            ->setCode($data['code'])
            ->setAmount((float)$data['amount'])
            ->setRemainingAmount((float)$data['remaining_amount'])
            ->setRecipientEmail($data['recipient_email'])
            ->setRecipientName($data['recipient_name'] ?? null)
            ->setSenderName($data['sender_name'] ?? null)
            ->setMessage($data['message'] ?? null)
            ->setStatus($data['status']);

        if (isset($data['date_add'])) {
            $giftCard->setDateAdd(new DateTime($data['date_add']));
        }

        if (isset($data['date_expiry']) && $data['date_expiry']) {
            $giftCard->setDateExpiry(new DateTime($data['date_expiry']));
        }

        if (isset($data['date_upd'])) {
            $giftCard->setDateUpd(new DateTime($data['date_upd']));
        }

        return $giftCard;
    }
}
