import React from 'react';
import { usePage } from '@inertiajs/react';
import { Segmented, Space, Tooltip } from 'antd';

import FlagIcon from './FlagIcon';
import classicFormPost from '../Utils/classicFormPost';
import useTranslation from '../Hooks/useTranslation';

const OPTIONS = [
    { value: 'en', label: 'EN', country: 'US' },
    { value: 'es', label: 'ES', country: 'MX' },
];

/**
 * Selector de idioma de la plataforma. El cambio se envía como POST clásico
 * (recarga completa) a propósito: así se reinician con el nuevo idioma tanto
 * el diccionario como los textos internos de Ant Design y los formatos de
 * fecha de dayjs, que se resuelven al arrancar la aplicación.
 */
export default function LocaleSwitcher({ size = 'small' }) {
    const { appUrls } = usePage().props;
    const { t, locale } = useTranslation();

    const handleChange = (value) => {
        if (value === locale) return;
        classicFormPost(`${appUrls.localeBase}/${value}`);
    };

    return (
        <Tooltip title={t('header.language')}>
            <Segmented
                size={size}
                value={locale}
                onChange={handleChange}
                aria-label={t('header.language')}
                options={OPTIONS.map((option) => ({
                    value: option.value,
                    label: (
                        <Space size={6} align="center" style={{ padding: '0 2px' }}>
                            <FlagIcon country={option.country} />
                            <span style={{ fontSize: 12, fontWeight: 600 }}>{option.label}</span>
                        </Space>
                    ),
                }))}
            />
        </Tooltip>
    );
}
