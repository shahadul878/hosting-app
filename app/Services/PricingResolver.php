<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ResellerProductPrice;
use App\Models\User;

/**
 * Resolves the effective sell price for a seller and catalog product.
 *
 * Sub-resellers inherit their parent reseller's custom_price when they have no
 * dedicated override row (MVP: only reseller-level overrides exist).
 */
class PricingResolver
{
    public function resolvePrice(User $seller, Product $product): string
    {
        $base = $product->sale_price !== null ? (string) $product->sale_price : (string) $product->base_price;
        $amount = $base;

        if ($seller->isReseller()) {
            $row = ResellerProductPrice::withoutGlobalScopes()
                ->where('reseller_user_id', $seller->id)
                ->where('product_id', $product->id)
                ->first();
            if ($row !== null) {
                $amount = (string) $row->custom_price;
            }
        } elseif ($seller->isSubReseller()) {
            $parent = $seller->parent;
            if ($parent !== null && $parent->isReseller()) {
                $row = ResellerProductPrice::withoutGlobalScopes()
                    ->where('reseller_user_id', $parent->id)
                    ->where('product_id', $product->id)
                    ->first();
                if ($row !== null) {
                    $amount = (string) $row->custom_price;
                }
            }
        }

        return number_format((float) $amount, 2, '.', '');
    }
}
