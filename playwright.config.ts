import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright E2E configuration for Rumah Makan Padang Raso Mandeh
 */
export default defineConfig({
  testDir: './e2e',
  /* Maximum time one test can run for. */
  timeout: 45 * 1000,
  expect: {
    timeout: 8000,
  },
  /* Run tests sequentially to preserve predictable database and order state */
  fullyParallel: false,
  workers: 1,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on failure */
  retries: process.env.CI ? 2 : 0,
  /* Reporter to use. */
  reporter: [
    ['list'],
    ['html', { open: 'never' }]
  ],
  use: {
    baseURL: 'http://127.0.0.1:8000',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },

  projects: [
    // Setup project for admin authentication
    {
      name: 'setup',
      testMatch: /.*\.setup\.ts/,
    },
    {
      name: 'chromium',
      use: { 
        ...devices['Desktop Chrome'],
        viewport: { width: 1280, height: 720 },
      },
      dependencies: ['setup'],
    },
  ],

  /* Run your local dev server before starting the tests */
  webServer: {
    command: 'php artisan serve --port=8000 --no-reload',
    url: 'http://127.0.0.1:8000/up',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  },
});
