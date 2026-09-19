import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedDefaultDashboardFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'default-dashboard-feature-tour-settings',
    output: 'feature-tour/default-dashboard-settings.png',
    route: '/admin/default-dashboard/settings',
    viewport: {
        width: 1280,
        height: 960,
        deviceScaleFactor: 2,
    },
    setup: seedDefaultDashboardFixture,
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '#settings-userDashboard-field', state: 'visible' },
        { type: 'text', text: 'User Group Dashboards' },
        { type: 'selector', selector: '#settings-userDashboardByGroup-editors-field', state: 'visible' },
        { type: 'selector', selector: '#settings-userDashboardByGroup-marketing-field', state: 'visible' },
    ],
    preSteps: [
        {
            type: 'evaluate',
            expression: `
                (() => {
                    if (document.activeElement instanceof HTMLElement) {
                        document.activeElement.blur();
                    }

                    window.scrollTo(0, 0);
                })();
            `,
        },
        { type: 'wait', waitFor: { type: 'timeout', ms: 200 } },
    ],
    target: {
        type: 'selector',
        selector: '#content',
        padding: 0,
    },
    caption: 'Default Dashboard settings with global and group-specific source dashboards in Craft 5.',
    intent: 'Show the current plugin settings that replace the obsolete Plugin Store image, including the newer group-specific dashboard controls.',
});
