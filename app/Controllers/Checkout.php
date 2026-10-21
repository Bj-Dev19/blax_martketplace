<?php

namespace App\Controllers;

use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\ShippingAddressModel;
use App\Models\ShopModel;
use App\Traits\CheckoutProcessorTrait;

class Checkout extends BaseController
{
    use CheckoutProcessorTrait;

    /**
     * Direct Buy Now page: Renders dedicated single-item checkout without touching cart_items.
     */
    public function direct()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $productId = (int) $this->request->getGet('product_id');
        $quantity  = max(1, (int) ($this->request->getGet('quantity') ?? 1));
        $variantId = (int) ($this->request->getGet('variant_id') ?? 0);

        if ($productId <= 0) {
            return redirect()->to('/')->with('error', 'Invalid product requested.');
        }

        $productModel = new ProductModel();
        $product      = $productModel->find($productId);

        if (!$product || ($product['status'] ?? 'active') !== 'active') {
            return redirect()->to('/')->with('error', 'Product is no longer available.');
        }

        // Validate variants if product has them
        $variantModel = new ProductVariantModel();
        $variants     = $variantModel->where('product_id', $productId)->findAll();

        $selectedVariant = null;
        $variantLabel    = null;
        $unitPrice       = (float) $product['price'];

        if (!empty($variants)) {
            if ($variantId <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'Please select a product option before checking out.');
            }

            foreach ($variants as $v) {
                if ((int) $v['id'] === $variantId) {
                    $selectedVariant = $v;
                    break;
                }
            }

            if (!$selectedVariant) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'The selected product option does not exist.');
            }

            if ((int) $selectedVariant['stock_quantity'] <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'The selected product option is currently out of stock.');
            }

            $unitPrice    = !empty($selectedVariant['price_override']) ? (float) $selectedVariant['price_override'] : $unitPrice;
            $variantLabel = ($selectedVariant['name'] ?? 'Option') . ': ' . ($selectedVariant['value'] ?? '');
            $quantity     = min($quantity, (int) $selectedVariant['stock_quantity']);
        } else {
            if ((int) $product['stock_quantity'] <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'This product is currently out of stock.');
            }
            $quantity = min($quantity, (int) $product['stock_quantity']);
        }

        // Resolve product thumbnail
        $imageUrl = null;
        if ($selectedVariant && !empty($selectedVariant['image_url'])) {
            $imageUrl = $selectedVariant['image_url'];
        } else {
            $img = (new ProductImageModel())
                ->where('product_id', $productId)
                ->orderBy('is_primary', 'DESC')
                ->first();
            if ($img && !empty($img['image_url'])) {
                $imageUrl = $img['image_url'];
            }
        }
        $imageUrl = product_image_url($imageUrl, 'thumbnail');

        // Load shop and addresses
        $shop      = (new ShopModel())->find($product['shop_id']);
        $addresses = (new ShippingAddressModel())
            ->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->findAll();

        if (empty($addresses)) {
            return redirect()->to(base_url('customer/addresses?open_add=1'))->with('error', 'Please add and set a shipping address before checking out.');
        }

        $subtotal = $unitPrice * $quantity;

        // Calculate distance-based shipping quote using Admin's configured rates + Google Maps Routes API
        $shippingSettings = (new \App\Models\SiteContentModel())->getShippingFeeSettings();
        $mapsService      = new \App\Services\GoogleMapsService();
        $defaultAddr      = $addresses[0];
        foreach ($addresses as $a) {
            if (!empty($a['is_default'])) {
                $defaultAddr = $a;
                break;
            }
        }

        $shippingQuote = $mapsService->calculateDeliveryQuote($shop ?? [], $defaultAddr, $shippingSettings);
        $shipping      = (float) ($shippingQuote['shipping_fee'] ?? $shippingSettings['base_shipping_fee'] ?? 50.00);

        return view('customer/buy_now', [
            'product'          => $product,
            'shop'             => $shop,
            'selectedVariant'  => $selectedVariant,
            'variantLabel'     => $variantLabel,
            'quantity'         => $quantity,
            'unitPrice'        => $unitPrice,
            'subtotal'         => $subtotal,
            'shipping'         => $shipping,
            'shippingQuote'    => $shippingQuote,
            'shippingSettings' => $shippingSettings,
            'addresses'        => $addresses,
            'imageUrl'         => $imageUrl,
            'csrfName'         => csrf_token(),
            'csrfHash'         => csrf_hash(),
        ]);
    }

    /**
     * AJAX endpoint: Compute distance-based shipping fee quote for a selected customer address and one or more shops.
     * Route: POST customer/shipping/quote
     */
    public function shippingQuote()
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'error'   => 'Unauthorized.',
            ]);
        }

        $addressId  = (int) $this->request->getPost('address_id');
        $rawShopIds = $this->request->getPost('shop_ids');

        $shopIds = [];
        if (is_array($rawShopIds)) {
            $shopIds = array_map('intval', $rawShopIds);
        } elseif (is_string($rawShopIds) && trim($rawShopIds) !== '') {
            $shopIds = array_map('intval', explode(',', $rawShopIds));
        }
        $shopIds = array_values(array_unique(array_filter($shopIds, fn($id) => $id > 0)));

        $addr = (new ShippingAddressModel())->find($addressId);
        if (!$addr || (int) ($addr['user_id'] ?? 0) !== $userId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success'   => false,
                'error'     => 'Invalid delivery address selected.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $settings    = (new \App\Models\SiteContentModel())->getShippingFeeSettings();
        $mapsService = new \App\Services\GoogleMapsService();
        $shopModel   = new ShopModel();

        $quotes        = [];
        $totalShipping = 0.0;
        $allDeliverable = true;
        $firstError    = null;

        foreach ($shopIds as $sId) {
            $shop = $shopModel->find($sId);
            if (!$shop) {
                continue;
            }
            $quote = $mapsService->calculateDeliveryQuote($shop, $addr, $settings);
            $quotes[$sId] = $quote;
            if (!$quote['deliverable']) {
                $allDeliverable = false;
                if ($firstError === null) {
                    $firstError = $quote['error'];
                }
            }
            $totalShipping += (float) $quote['shipping_fee'];
        }

        return $this->response->setJSON([
            'success'            => true,
            'deliverable'        => $allDeliverable,
            'total_shipping_fee' => round($totalShipping, 2),
            'quotes'             => $quotes,
            'settings'           => $settings,
            'error'              => $firstError,
            'csrf_hash'          => csrf_hash(),
        ]);
    }

    /**
     * Place order from Direct Buy Now page.
     * Re-derives all pricing and stock server-side and uses CheckoutProcessorTrait.
     */
    public function placeOrder()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $productId = (int) $this->request->getPost('product_id');
        $quantity  = max(1, (int) ($this->request->getPost('quantity') ?? 1));
        $variantId = (int) ($this->request->getPost('variant_id') ?? 0);

        if ($productId <= 0) {
            return redirect()->to('/')->with('error', 'Invalid product.');
        }

        $productModel = new ProductModel();
        $product      = $productModel->find($productId);

        if (!$product || ($product['status'] ?? 'active') !== 'active') {
            return redirect()->to('/')->with('error', 'This product is no longer available.');
        }

        $variantModel    = new ProductVariantModel();
        $variants        = $variantModel->where('product_id', $productId)->findAll();
        $selectedVariant = null;
        $variantLabel    = null;
        $unitPrice       = (float) $product['price'];

        if (!empty($variants)) {
            if ($variantId <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'Please select a product option to continue.');
            }

            foreach ($variants as $v) {
                if ((int) $v['id'] === $variantId) {
                    $selectedVariant = $v;
                    break;
                }
            }

            if (!$selectedVariant || (int) $selectedVariant['stock_quantity'] <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'The selected option is out of stock.');
            }

            $unitPrice    = !empty($selectedVariant['price_override']) ? (float) $selectedVariant['price_override'] : $unitPrice;
            $variantLabel = ($selectedVariant['name'] ?? 'Option') . ': ' . ($selectedVariant['value'] ?? '');
            $quantity     = min($quantity, (int) $selectedVariant['stock_quantity']);
        } else {
            if ((int) $product['stock_quantity'] <= 0) {
                return redirect()->to(base_url('product/' . $productId))->with('error', 'Product is out of stock.');
            }
            $quantity = min($quantity, (int) $product['stock_quantity']);
        }

        $paymentMethod     = (string) ($this->request->getPost('payment_method') ?? 'gcash');
        $fulfillmentMethod = (string) ($this->request->getPost('fulfillment_method') ?? ($paymentMethod === 'pickup' ? 'pickup' : 'delivery'));
        $addressId         = $this->request->getPost('shipping_address_id') ? (int) $this->request->getPost('shipping_address_id') : null;

        $itemsByShop = [
            $product['shop_id'] => [[
                'product_id'    => $product['id'],
                'variant_id'    => $selectedVariant['id'] ?? null,
                'variant_label' => $variantLabel,
                'product_name'  => $product['name'],
                'quantity'      => $quantity,
                'unit_price'    => $unitPrice,
            ]],
        ];

        $buyNowUrl = base_url("buy-now?product_id={$productId}&quantity={$quantity}" . ($variantId > 0 ? "&variant_id={$variantId}" : ''));

        return $this->processItemsByShop(
            $itemsByShop,
            $userId,
            $paymentMethod,
            $fulfillmentMethod,
            $addressId,
            [], // Empty cart items list — Direct Buy never touches cart_items!
            $buyNowUrl,
            $buyNowUrl
        );
    }
}

