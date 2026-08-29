import React from 'react';
import { usePage } from '@inertiajs/react';
import { Select, Space, Tooltip } from 'antd';

import FlagIcon from './FlagIcon';
import classicFormPost from '../Utils/classicFormPost';
import useTranslation from '../Hooks/useTranslation';

const OPTIONS = [
    { value: 'en', label: 'EN', country: 'US' },
    { value: 'es', label: 'ES', country: 'MX' },
];

/**
 * Selector de idioma de la plataforma, como lista desplegable con la bandera
 * y el código del idioma. El cambio se envía como POST clásico
 * (recarga completa) a propósito: así se reinician con el nuevo idioma tanto
 * el diccionario como los textos internos de Ant Design y los formatos de
 * fecha de dayjs, que se resuelven al arrancar la aplicación.
 */
export default function LocaleSwitcher({ size = 'small', width = 104 }) {
    const { appUrls } = usePage().props;
    const { t, locale } = useTranslation();

    const handleChange = (value) => {
        if (value === locale) return;
        classicFormPost(`${appUrls.localeBase}/${value}`);
    };

    return (
        <Tooltip title={t('header.language')}>
            <Select
                size={size}
                value={locale}
                onChange={handleChange}
                aria-label={t('header.language')}
                style={{ width }}
                popupMatchSelectWidth={false}
                options={OPTIONS.map((option) => ({
                    value: option.value,
                    label: (
                        <Space size={8} align="center">
                            <FlagIcon country={option.country} />
                            <span style={{ fontWeight: 600 }}>{option.label}</span>
                        </Space>
                    ),
                }))}
            />
        </Tooltip>
    );
}
