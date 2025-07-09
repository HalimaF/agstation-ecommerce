# Product Images Directory

This directory contains pre-loaded product images that are part of the project.

## Benefits:
- ✅ Images persist across deployments
- ✅ No ephemeral storage issues
- ✅ Faster loading (served as static assets)
- ✅ Version controlled with code

## Usage:
- Place product images in this directory
- Use filename format: `{asin}.jpg` or `{asin}.png`
- Update database `image_url` field to reference these files
- Images accessible via `/assets/images/products/{filename}`

## Current Images:
- vivilux-led-strip.jpg (for ASIN: B07S28X9KZ)
- vivilux-smart-bulb.jpg (for ASIN: B0DB9Z88RG)  
- ge-design-tool.jpg (for ASIN: B075H3MLR5)
- default-product.png (fallback image)
