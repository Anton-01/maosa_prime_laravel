import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { App as AntApp, ConfigProvider } from 'antd';
import esES from 'antd/locale/es_ES';
import dayjs from 'dayjs';
import 'dayjs/locale/es';

import AdminLayout from './Layouts/AdminLayout';

dayjs.locale('es');

const appName = document.querySelector('title')?.innerText || 'Maosa Prime';

const themeConfig = {
    token: {
        colorPrimary: '#6777ef',
        borderRadius: 6,
        fontFamily:
            "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
    },
    components: {
        Layout: {
            headerBg: '#ffffff',
            siderBg: '#001529',
        },
    },
};

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        );

        // Every page under Pages/Admin inherits the shared admin layout
        // unless the page component explicitly defines its own.
        if (page.default.layout === undefined && name.startsWith('Admin/')) {
            page.default.layout = (children) => <AdminLayout>{children}</AdminLayout>;
        }

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <ConfigProvider locale={esES} theme={themeConfig}>
                <AntApp>
                    <App {...props} />
                </AntApp>
            </ConfigProvider>,
        );
    },
    progress: {
        color: '#6777ef',
    },
});
