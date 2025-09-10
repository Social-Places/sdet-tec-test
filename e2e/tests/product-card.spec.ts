import { test, expect } from '@playwright/test';

test.describe('ProductCard Component', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/products');
  });

  test('displays product information correctly', async ({ page }) => {
    const productCard = page.locator('.product-card').first();
    
    // Check basic product info is displayed
    await expect(productCard.locator('.product-title')).toBeVisible();
    await expect(productCard.locator('.product-price')).toBeVisible();
    await expect(productCard.locator('button')).toBeVisible();
  });

  test('add to cart functionality works', async ({ page }) => {
    const productCard = page.locator('.product-card').first();
    
    // Get initial cart count
    const cartCount = await page.locator('[data-test="cart-count"]').textContent();
    const initialCount = cartCount ? parseInt(cartCount) : 0;
    
    // Click add to cart
    await productCard.locator('button').click();
    
    // Verify cart count increased
    await expect(page.locator('[data-test="cart-count"]')).toHaveText(`${initialCount + 1}`);
  });
});
