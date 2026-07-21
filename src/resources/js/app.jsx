import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { App as AntApp, ConfigProvider } from 'antd';
import esES from 'antd/locale/es_ES';
import dayjs from 'dayjs';
import 'dayjs/locale/es';

import './styles/app.css';

import AdminLayout from './Layouts/AdminLayout';
import FrontendLayout from './Layouts/FrontendLayout';
import UserLayout from './Layouts/UserLayout';

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

        // Each area inherits its shared layout unless the page component
        // explicitly defines its own (e.g. the guest landing / auth screens).
        if (page.default.layout === undefined) {
            if (name.startsWith('Admin/')) {
                page.default.layout = (children) => <AdminLayout>{children}</AdminLayout>;
            } else if (name.startsWith('User/')) {
                page.default.layout = (children) => <UserLayout>{children}</UserLayout>;
            } else if (name.startsWith('Public/')) {
                page.default.layout = (children) => <FrontendLayout>{children}</FrontendLayout>;
            }
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
