import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-default-dashboard.php'), 'utf8');

/** Seed representative source users, user groups and current plugin settings. */
export async function seedDefaultDashboardFixture(context: ScreenshotSetupContext): Promise<void> {
    const output = await context.runCraftScript(seedScript, { label: 'seed-default-dashboard-settings' });
    const fixture = JSON.parse(output.trim()) as { users?: number; groups?: number; settingsSaved?: boolean };

    if (Number(fixture.users) !== 3 || Number(fixture.groups) !== 2 || fixture.settingsSaved !== true) {
        throw new Error(`Invalid Default Dashboard fixture payload: ${output}`);
    }
}
