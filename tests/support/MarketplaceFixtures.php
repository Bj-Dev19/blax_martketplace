<?php

/**
 * Shared self-seeding fixtures for marketplace integration tests.
 *
 * The PHPUnit database group may start with schema but no rows, so every
 * DB-backed test seeds exactly the records it needs with reserved high IDs and
 * cleans them up again. Tests skip (not fail) when the schema is unavailable.
 */
trait MarketplaceFixtures
{
    protected const FX_USER_OWNER    = 9000001;
    protected const FX_USER_CUSTOMER = 9000002;
    protected const FX_USER_ADMIN    = 9000003;
    protected const FX_SHOP          = 9000011;

    /** @var array<string, array<int>> */
    protected array $fxSeeded = [
        'deliveries'         => [],
        'orders'             => [],
        'printing_requests'  => [],
        'shipping_addresses' => [],
        'shops'              => [],
        'users'              => [],
    ];

    protected function fxDb(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }

    /**
     * Skip the test when the marketplace schema has not been installed.
     */
    protected function fxRequireSchema(array $tables = ['shops', 'users', 'orders', 'deliveries']): void
    {
        $db = $this->fxDb();
        foreach ($tables as $table) {
            if (!$db->tableExists($table)) {
                $this->markTestSkipped("Test database table '{$table}' is unavailable; run the migrations against the tests database group.");
            }
        }
    }

    /**
     * @return array{shop: array, owner: array, customer: array}
     */
    protected function fxSeedBase(): array
    {
        $this->fxRequireSchema(['users', 'shops']);
        $db   = $this->fxDb();
        $now  = date('Y-m-d H:i:s');
        $rand = substr((string) uniqid(), -6);

        foreach ([
            [self::FX_USER_OWNER, 'shop_owner', 'Tenant', 'Owner', "tenant.{$rand}@fx.blax.test"],
            [self::FX_USER_CUSTOMER, 'customer', 'Cruz', 'Juan', "customer.{$rand}@fx.blax.test"],
            [self::FX_USER_ADMIN, 'admin', 'Super', 'Admin', "admin.{$rand}@fx.blax.test"],
        ] as [$id, $role, $first, $last, $email]) {
            $db->table('users')->where('id', $id)->delete();
            $db->table('users')->insert([
                'id'            => $id,
                'role'          => $role,
                'first_name'    => $first,
                'last_name'     => $last,
                'email'         => $email,
                'phone'         => '09170000000',
                'password_hash' => password_hash('fx-password', PASSWORD_BCRYPT),
                'status'        => 'active',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $this->fxSeeded['users'][] = $id;
        }

        $db->table('shops')->where('id', self::FX_SHOP)->delete();
        $db->table('shops')->insert([
            'id'              => self::FX_SHOP,
            'owner_id'        => self::FX_USER_OWNER,
            'shop_name'       => 'FX Polomolok Print Hub',
            'slug'            => 'fx-polomolok-print-hub-' . $rand,
            'description'     => 'Fixture shop',
            'address_line'    => 'National Highway, Poblacion',
            'street'          => 'National Highway',
            'barangay'        => 'Poblacion',
            'city'            => 'Polomolok',
            'province'        => 'South Cotabato',
            'latitude'        => 6.2196,
            'longitude'       => 125.0693,
            'plan'            => 'standard',
            'status'          => 'active',
            'offers_printing' => 1,
            'offers_delivery' => 1,
            'offers_pickup'   => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        $this->fxSeeded['shops'][] = self::FX_SHOP;

        $shop     = $db->table('shops')->getWhere(['id' => self::FX_SHOP])->getRowArray();
        $owner    = $db->table('users')->getWhere(['id' => self::FX_USER_OWNER])->getRowArray();
        $customer = $db->table('users')->getWhere(['id' => self::FX_USER_CUSTOMER])->getRowArray();

        return ['shop' => $shop, 'owner' => $owner, 'customer' => $customer];
    }

    protected function fxSeedOrder(int $id, string $status = 'shipped', array $overrides = []): int
    {
        $db = $this->fxDb();
        $db->table('orders')->where('id', $id)->delete();
        $db->table('orders')->insert(array_merge([
            'id'                 => $id,
            'order_number'       => 'ORD-FX-' . $id,
            'customer_id'        => self::FX_USER_CUSTOMER,
            'shop_id'            => self::FX_SHOP,
            'fulfillment_method' => 'delivery',
            'payment_method'     => 'gcash',
            'subtotal'           => 200.00,
            'shipping_fee'       => 50.00,
            'total_amount'       => 250.00,
            'status'             => $status,
            'payment_status'     => 'paid',
            'placed_at'          => date('Y-m-d H:i:s'),
        ], $overrides));
        $this->fxSeeded['orders'][] = $id;

        return $id;
    }

    protected function fxSeedPrintingRequest(int $id, string $status = 'new', array $overrides = []): int
    {
        $db = $this->fxDb();
        $db->table('printing_requests')->where('id', $id)->delete();
        $db->table('printing_requests')->insert(array_merge([
            'id'                 => $id,
            'request_number'     => 'PR-FX-' . $id,
            'customer_id'        => self::FX_USER_CUSTOMER,
            'shop_id'            => self::FX_SHOP,
            'file_name'          => 'fixture_doc.pdf',
            'file_url'           => 'https://example.test/fixture_doc.pdf',
            'document_type'      => 'pdf',
            'page_count'         => 5,
            'paper_size'         => 'A4',
            'color_mode'         => 'bw',
            'copies'             => 1,
            'binding_option'     => 'none',
            'fulfillment_method' => 'delivery',
            'total_price'        => 60.00,
            'down_payment'       => 30.00,
            'status'             => $status,
            'created_at'         => date('Y-m-d H:i:s'),
        ], $overrides));
        $this->fxSeeded['printing_requests'][] = $id;

        return $id;
    }

    protected function fxSeedDelivery(array $data): int
    {
        $db = $this->fxDb();
        $inserted = $db->table('deliveries')->insert(array_merge([
            'deliverable_type'    => 'order',
            'deliverable_id'      => 0,
            'tracking_id'         => 'TRK-FX-' . substr((string) uniqid(), -8),
            'courier_name'        => 'FX Rider',
            'destination_address' => 'Cannery Site, Polomolok',
            'status'              => 'shipped',
            'created_at'          => date('Y-m-d H:i:s'),
        ], $data));
        $id = $inserted ? (int) $db->insertID() : 0;
        $this->fxSeeded['deliveries'][] = $id;

        return $id;
    }

    protected function fxSeedAddress(int $id, array $overrides = []): int
    {
        $db = $this->fxDb();
        $db->table('shipping_addresses')->where('id', $id)->delete();
        $db->table('shipping_addresses')->insert(array_merge([
            'id'             => $id,
            'user_id'        => self::FX_USER_CUSTOMER,
            'label'          => 'Home',
            'recipient_name' => 'Juan Cruz',
            'phone'          => '09170000000',
            'address_line1'  => 'Purok 3, National Highway',
            'address_line2'  => 'Cannery Site',
            'city'           => 'Polomolok',
            'province'       => 'South Cotabato',
            'postal_code'    => '9501',
            'is_default'     => 1,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $overrides));
        $this->fxSeeded['shipping_addresses'][] = $id;

        return $id;
    }

    protected function fxDeliveryRow(int $id): ?array
    {
        return $this->fxDb()->table('deliveries')->getWhere(['id' => $id])->getRowArray();
    }

    protected function fxSeededRow(string $table, int $id): ?array
    {
        return $this->fxDb()->table($table)->getWhere(['id' => $id])->getRowArray();
    }

    /**
     * Delete every record this test inserted (children first, FK-safe).
     */
    protected function fxCleanup(): void
    {
        $db = $this->fxDb();
        foreach (['deliveries', 'orders', 'printing_requests', 'shipping_addresses', 'shops', 'users'] as $table) {
            foreach (array_unique($this->fxSeeded[$table]) as $id) {
                if ($id > 0) {
                    $db->table($table)->where('id', $id)->delete();
                }
            }
            $this->fxSeeded[$table] = [];
        }
    }
}
