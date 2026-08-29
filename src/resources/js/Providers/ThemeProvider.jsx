import React, { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { ConfigProvider, theme as antdTheme } from 'antd';
import enUS from 'antd/locale/en_US';
import esES from 'antd/locale/es_ES';

const STORAGE_KEY = 'maosa-theme-mode';

/**
 * Idioma con el que arrancó la página (lo escribe Blade en <html lang>).
 * El selector de idioma recarga la aplicación, así que leerlo una vez al
 * montar es suficiente y evita pasar la prop por todo el árbol.
 */
function documentLocale() {
    if (typeof document === 'undefined') return 'es';

    return document.documentElement.lang?.startsWith('en') ? 'en' : 'es';
}

const ThemeModeContext = createContext({ mode: 'light', toggle: () => {}, setMode: () => {} });

export const useThemeMode = () => useContext(ThemeModeContext);

const BASE_TOKEN = {
    colorPrimary: '#6777ef',
    borderRadius: 6,
    fontFamily:
        "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
};

/**
 * Root theme provider that exposes a light/dark mode toggle (persisted in
 * localStorage) and applies the matching Ant Design algorithm. Public and
 * user areas force the light algorithm via their own nested ConfigProvider,
 * so dark mode is effectively scoped to the admin panel.
 */
export default function AppThemeProvider({ children }) {
    const [mode, setMode] = useState(() => {
        if (typeof window === 'undefined') return 'light';
        return window.localStorage.getItem(STORAGE_KEY) === 'dark' ? 'dark' : 'light';
    });

    useEffect(() => {
        window.localStorage.setItem(STORAGE_KEY, mode);
        document.documentElement.setAttribute('data-theme', mode);
    }, [mode]);

    const value = useMemo(
        () => ({
            mode,
            setMode,
            toggle: () => setMode((current) => (current === 'dark' ? 'light' : 'dark')),
        }),
        [mode],
    );

    const themeConfig = {
        algorithm: mode === 'dark' ? antdTheme.darkAlgorithm : antdTheme.defaultAlgorithm,
        token: BASE_TOKEN,
        components: {
            Layout: {
                headerBg: mode === 'dark' ? '#141414' : '#ffffff',
                siderBg: '#001529',
            },
        },
    };

    return (
        <ThemeModeContext.Provider value={value}>
            <ConfigProvider locale={documentLocale() === 'en' ? enUS : esES} theme={themeConfig}>
                {children}
            </ConfigProvider>
        </ThemeModeContext.Provider>
    );
}
