<?php
/**
 * Gift Card Repository
 * 
 * @author mlabfactory <tech@mlabfactory.com>
 */

namespace Mlab\GiftCard\Repository;

use Mlab\GiftCard\Entity\GiftCard;
use Db;
use DbQuery;

class GiftCardRepository
{
    const TABLE = 'giftcard';

    /** @var Db */
    private $db;

    /** @var string */
    private $prefix;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->prefix = _DB_PREFIX_;
    }

    /**
     * Find gift card by ID
     */
    public function find(int $id): ?GiftCard
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('id_giftcard = ' . (int)$id);

        $result = $this->db->getRow($query);

        return $result ? GiftCard::fromArray($result) : null;
    }

    /**
     * Find gift card by code
     */
    public function findByCode(string $code): ?GiftCard
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('code = "' . pSQL($code) . '"');

        $result = $this->db->getRow($query);

        return $result ? GiftCard::fromArray($result) : null;
    }

    /**
     * Find gift cards by order
     */
    public function findByOrder(int $idOrder): array
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('id_order = ' . (int)$idOrder);

        $results = $this->db->executeS($query);

        if (!$results) {
            return [];
        }

        return array_map(function($data) {
            return GiftCard::fromArray($data);
        }, $results);
    }

    /**
     * Find gift cards by recipient email
     */
    public function findByRecipientEmail(string $email): array
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('recipient_email = "' . pSQL($email) . '"')
              ->orderBy('date_add DESC');

        $results = $this->db->executeS($query);

        if (!$results) {
            return [];
        }

        return array_map(function($data) {
            return GiftCard::fromArray($data);
        }, $results);
    }

    /**
     * Find active gift cards
     */
    public function findActive(): array
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('status = "' . GiftCard::STATUS_ACTIVE . '"')
              ->where('(date_expiry IS NULL OR date_expiry > NOW())')
              ->where('remaining_amount > 0');

        $results = $this->db->executeS($query);

        if (!$results) {
            return [];
        }

        return array_map(function($data) {
            return GiftCard::fromArray($data);
        }, $results);
    }

    /**
     * Find expiring gift cards
     */
    public function findExpiringSoon(int $days = 30): array
    {
        $query = new DbQuery();
        $query->select('*')
              ->from(self::TABLE)
              ->where('status = "' . GiftCard::STATUS_ACTIVE . '"')
              ->where('date_expiry BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ' . (int)$days . ' DAY)')
              ->where('remaining_amount > 0');

        $results = $this->db->executeS($query);

        if (!$results) {
            return [];
        }

        return array_map(function($data) {
            return GiftCard::fromArray($data);
        }, $results);
    }

    /**
     * Save gift card
     */
    public function save(GiftCard $giftCard): bool
    {
        $data = $giftCard->toArray();
        unset($data['id_giftcard']); // Remove ID for insert/update

        if ($giftCard->getId()) {
            return $this->db->update(
                self::TABLE,
                $data,
                'id_giftcard = ' . (int)$giftCard->getId()
            );
        }

        $result = $this->db->insert(self::TABLE, $data);
        
        if ($result) {
            $giftCard->setId((int)$this->db->Insert_ID());
        }

        return $result;
    }

    /**
     * Delete gift card
     */
    public function delete(int $id): bool
    {
        return $this->db->delete(
            self::TABLE,
            'id_giftcard = ' . (int)$id
        );
    }

    /**
     * Check if code exists
     */
    public function codeExists(string $code): bool
    {
        $query = new DbQuery();
        $query->select('COUNT(*)')
              ->from(self::TABLE)
              ->where('code = "' . pSQL($code) . '"');

        return (bool)$this->db->getValue($query);
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $query = new DbQuery();
        $query->select('
            COUNT(*) as total_cards,
            SUM(amount) as total_sold,
            SUM(remaining_amount) as total_remaining,
            COUNT(CASE WHEN status = "' . GiftCard::STATUS_ACTIVE . '" THEN 1 END) as active_cards,
            COUNT(CASE WHEN status = "' . GiftCard::STATUS_USED . '" THEN 1 END) as used_cards,
            COUNT(CASE WHEN status = "' . GiftCard::STATUS_EXPIRED . '" THEN 1 END) as expired_cards
        ')->from(self::TABLE);

        return $this->db->getRow($query) ?: [];
    }

    /**
     * Update expired gift cards
     */
    public function updateExpiredCards(): int
    {
        return $this->db->execute('
            UPDATE ' . $this->prefix . self::TABLE . '
            SET status = "' . GiftCard::STATUS_EXPIRED . '"
            WHERE status = "' . GiftCard::STATUS_ACTIVE . '"
            AND date_expiry < NOW()
        ');
    }
}
