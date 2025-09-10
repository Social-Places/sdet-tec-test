import { test as base } from '@playwright/test';

type TestFixtures = {
  loginAsTestUser: () => Promise<void>;
};

export const test = base.extend<TestFixtures>({
  loginAsTestUser: async ({ page }, use) => {
    const login = async () => {
      await page.goto('/login');
      await page.fill('[data-test="email"]', 'test@example.com');
      await page.fill('[data-test="password"]', 'password');
      await page.click('[data-test="login-submit"]');
      await page.waitForSelector('[data-test="user-profile"]');
    };
    await use(login);
  },
});
