<?php
/**
 * Product Gift Card Entity
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Entity;

class ProductGiftCard
{
    /** @var int */
    private $idProduct;

    /** @var bool */
    private $isGiftCard;

    /** @var array */
    private $customAmounts;

    /** @var string|null */
    private $defaultImage;

    public function __construct(int $idProduct)
    {
        $this->idProduct = $idProduct;
        $this->isGiftCard = false;
        $this->customAmounts = [];
    }

    public function getIdProduct(): int
    {
        return $this->idProduct;
    }

    public function isGiftCard(): bool
    {
        return $this->isGiftCard;
    }

    public function setIsGiftCard(bool $isGiftCard): self
    {
        $this->isGiftCard = $isGiftCard;
        return $this;
    }

    public function getCustomAmounts(): array
    {
        return $this->customAmounts;
    }

    public function setCustomAmounts(array $customAmounts): self
    {
        $this->customAmounts = array_map('floatval', $customAmounts);
        return $this;
    }

    public function setCustomAmountsFromString(string $amounts): self
    {
        if (empty($amounts)) {
            $this->customAmounts = [];
            return $this;
        }

        $this->customAmounts = array_map(
            'floatval',
            array_filter(
                array_map('trim', explode(',', $amounts)),
                function($v) { return $v !== ''; }
            )
        );

        return $this;
    }

    public function getCustomAmountsAsString(): string
    {
        return implode(',', $this->customAmounts);
    }

    public function getDefaultImage(): ?string
    {
        return $this->defaultImage;
    }

    public function setDefaultImage(?string $defaultImage): self
    {
        $this->defaultImage = $defaultImage;
        return $this;
    }

    public function hasCustomAmounts(): bool
    {
        return !empty($this->customAmounts);
    }

    public function toArray(): array
    {
        return [
            'id_product' => $this->idProduct,
            'is_giftcard' => $this->isGiftCard ? 1 : 0,
            'custom_amounts' => $this->getCustomAmountsAsString(),
            'default_image' => $this->defaultImage,
        ];
    }

    public static function fromArray(array $data): self
    {
        $productGiftCard = new self((int)$data['id_product']);
        
        $productGiftCard
            ->setIsGiftCard((bool)$data['is_giftcard'])
            ->setDefaultImage($data['default_image'] ?? null);

        if (!empty($data['custom_amounts'])) {
            $productGiftCard->setCustomAmountsFromString($data['custom_amounts']);
        }

        return $productGiftCard;
    }
}
