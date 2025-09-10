import { test, expect } from '@playwright/test';

test.describe('Shopping Flow', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('complete shopping flow', async ({ page }) => {
    // Login
    await page.click('text=Login');
    await page.fill('[data-test="email"]', 'test@example.com');
    await page.fill('[data-test="password"]', 'password');
    await page.click('[data-test="login-submit"]');
    
    // Verify login success
    await expect(page.locator('[data-test="user-profile"]')).toBeVisible();

    // Browse products
    await page.click('text=Products');
    await expect(page.locator('.product-card')).toHaveCount(8);

    // Add item to cart
    await page.click('.product-card:first-child button');
    await expect(page.locator('[data-test="cart-count"]')).toHaveText('1');

    // Go to cart
    await page.click('[data-test="cart-icon"]');
    await expect(page.locator('.cart-item')).toHaveCount(1);

    // Proceed to checkout
    await page.click('[data-test="checkout-button"]');
    
    // Fill shipping info
    await page.fill('[data-test="shipping-name"]', 'Test User');
    await page.fill('[data-test="shipping-address"]', '123 Test St');
    await page.fill('[data-test="shipping-city"]', 'Test City');
    await page.fill('[data-test="shipping-zip"]', '12345');
    
    // Complete order
    await page.click('[data-test="place-order"]');
    
    // Verify order success
    await expect(page.locator('[data-test="order-success"]')).toBeVisible();
    
    // Check order history
    await page.click('text=Orders');
    await expect(page.locator('.order-item')).toHaveCount(1);
  });
});
