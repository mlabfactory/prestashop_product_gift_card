<?php
/**
 * Product Gift Card Repository
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Repository;

use Mlab\GiftCard\Entity\ProductGiftCard;
use Db;
use DbQuery;

class ProductGiftCardRepository
{
    const TABLE = 'product_giftcard';

    /** @var Db */
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Find product gift card configuration
     */
    public function find(int $idProduct): ?ProductGiftCard
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('id_product = ' . (int)$idProduct);

        $result = $this->db->getRow($query);

        return $result ? ProductGiftCard::fromArray($result) : null;
    }

    /**
     * Find all gift card products
     */
    public function findAllGiftCards(): array
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('is_giftcard = 1');

        $results = $this->db->executeS($query);

        if (!$results) {
            return [];
        }

        return array_map(function($data) {
            return ProductGiftCard::fromArray($data);
        }, $results);
    }

    /**
     * Check if product is gift card
     */
    public function isGiftCard(int $idProduct): bool
    {
        $query = new DbQuery();
        $query->select('is_giftcard')
              ->from(self::TABLE)
              ->where('id_product = ' . (int)$idProduct);

        return (bool)$this->db->getValue($query);
    }

    /**
     * Save product gift card configuration
     */
    public function save(ProductGiftCard $productGiftCard): bool
    {
        $data = $productGiftCard->toArray();
        $idProduct = $data['id_product'];
        unset($data['id_product']);

        if ($this->exists($idProduct)) {
            return $this->db->update(
                self::TABLE,
                $data,
                'id_product = ' . (int)$idProduct
            );
        }

        $data['id_product'] = $idProduct;
        return $this->db->insert(self::TABLE, $data);
    }

    /**
     * Delete product gift card configuration
     */
    public function delete(int $idProduct): bool
    {
        return $this->db->delete(
            self::TABLE,
            'id_product = ' . (int)$idProduct
        );
    }

    /**
     * Check if product configuration exists
     */
    public function exists(int $idProduct): bool
    {
        $query = new DbQuery();
        $query->select('COUNT(*)')
              ->from(self::TABLE)
              ->where('id_product = ' . (int)$idProduct);

        return (bool)$this->db->getValue($query);
    }

    /**
     * Get count of gift card products
     */
    public function countGiftCardProducts(): int
    {
        $query = new DbQuery();
        $query->select('COUNT(*)')
              ->from(self::TABLE)
              ->where('is_giftcard = 1');

        return (int)$this->db->getValue($query);
    }
}
