<?php
/**
 * Shared helper functions used across the storefront and admin panel.
 */

/**
 * Is a product's discount currently active (today falls within its date range)?
 */
function isDiscountActive(array $product): bool
{
    if (empty($product['discount_percent']) || (float)$product['discount_percent'] <= 0) {
        return false;
    }

    $today = date('Y-m-d');

    if (!empty($product['discount_start']) && $today < $product['discount_start']) {
        return false;
    }
    if (!empty($product['discount_end']) && $today > $product['discount_end']) {
        return false;
    }

    return true;
}

/**
 * Returns [original, final, percentOff, isActive] for a product.
 */
function getPricing(array $product): array
{
    $original = (float)$product['base_price'];
    $active = isDiscountActive($product);
    $percent = $active ? (float)$product['discount_percent'] : 0;
    $final = $active ? round($original - ($original * $percent / 100), 2) : $original;

    return [
        'original' => $original,
        'final' => $final,
        'percent' => $percent,
        'active' => $active,
    ];
}

function formatPrice(float $amount): string
{
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 0);
}

/**
 * Build a wa.me link pre-filled with a message about a specific product.
 */
function whatsAppProductLink(array $product): string
{
    $pricing = getPricing($product);
    $priceLine = $pricing['active']
        ? formatPrice($pricing['final']) . " (was " . formatPrice($pricing['original']) . ")"
        : formatPrice($pricing['original']);

    $message = "Hi Nuru Fashion! I'd like to order:\n"
        . "*" . $product['name'] . "*\n"
        . "Price: " . $priceLine . "\n"
        . "Link: " . SITE_URL . "/product.php?slug=" . $product['slug'];

    return "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . rawurlencode($message);
}

/**
 * Generic WhatsApp link (no specific product) for the floating button / contact page.
 */
function whatsAppGeneralLink(): string
{
    $message = "Hi Nuru Fashion! I have a question about your products.";
    return "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . rawurlencode($message);
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text ?: 'n-a';
}

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}