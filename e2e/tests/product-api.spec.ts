import { test, expect } from '@playwright/test';

test.describe('Product API', () => {
  const apiBaseUrl = 'http://localhost:8000/api';

  test('GET /api/products returns product list', async ({ request }) => {
    const response = await request.get(`${apiBaseUrl}/products`);
    expect(response.ok()).toBeTruthy();
    
    const products = await response.json();
    expect(Array.isArray(products)).toBeTruthy();
    expect(products.length).toBeGreaterThan(0);
    
    // Validate product structure
    const product = products[0];
    expect(product).toHaveProperty('id');
    expect(product).toHaveProperty('name');
    expect(product).toHaveProperty('price');
    expect(product).toHaveProperty('description');
  });

  test('GET /api/products/{id} returns specific product', async ({ request }) => {
    // First get all products to get a valid ID
    const productsResponse = await request.get(`${apiBaseUrl}/products`);
    const products = await productsResponse.json();
    const firstProductId = products[0].id;
    
    // Get specific product
    const response = await request.get(`${apiBaseUrl}/products/${firstProductId}`);
    expect(response.ok()).toBeTruthy();
    
    const product = await response.json();
    expect(product.id).toBe(firstProductId);
  });

  test('GET /api/products with invalid ID returns 404', async ({ request }) => {
    const response = await request.get(`${apiBaseUrl}/products/999999`);
    expect(response.status()).toBe(404);
  });
});
