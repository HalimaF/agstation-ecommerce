<?php
/**
 * Product Image Helper Functions
 * Handles local product images with fallback to default
 */

/**
 * Get the URL for a product image
 * @param string $image_url The image filename from database
 * @return string Full URL to product image
 */
function getProductImageUrl($image_url) {
    if (empty($image_url)) {
        return '/assets/images/products/default-product.svg';
    }
    
    // Check if it's already a full URL (for backward compatibility)
    if (strpos($image_url, 'http') === 0) {
        return $image_url;
    }
    
    // For local files, use assets directory
    return '/assets/images/products/' . $image_url;
}

/**
 * Check if a product image file exists
 * @param string $image_url The image filename
 * @return bool True if file exists
 */
function productImageExists($image_url) {
    if (empty($image_url)) {
        return false;
    }
    
    $imagePath = __DIR__ . '/../assets/images/products/' . $image_url;
    return file_exists($imagePath);
}

/**
 * Get product image with fallback
 * @param string $image_url The image filename from database
 * @return string URL to image (with fallback if needed)
 */
function getProductImageWithFallback($image_url) {
    if (empty($image_url) || !productImageExists($image_url)) {
        return '/assets/images/products/default-product.svg';
    }
    
    return '/assets/images/products/' . $image_url;
}

/**
 * Generate image tag for product
 * @param string $image_url The image filename
 * @param string $alt Alt text for image
 * @param string $class CSS classes
 * @param string $style Inline styles
 * @return string HTML img tag
 */
function getProductImageTag($image_url, $alt = 'Product Image', $class = '', $style = '') {
    $src = getProductImageWithFallback($image_url);
    $classAttr = $class ? " class=\"$class\"" : '';
    $styleAttr = $style ? " style=\"$style\"" : '';
    
    return "<img src=\"$src\" alt=\"" . htmlspecialchars($alt) . "\"$classAttr$styleAttr>";
}

/**
 * Get list of available product images in assets directory
 * @return array Array of image filenames
 */
function getAvailableProductImages() {
    $imageDir = __DIR__ . '/../assets/images/products/';
    $images = [];
    
    if (is_dir($imageDir)) {
        $files = scandir($imageDir);
        foreach ($files as $file) {
            if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $images[] = $file;
            }
        }
    }
    
    return $images;
}
?>
