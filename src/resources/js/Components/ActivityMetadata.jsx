import React from 'react';
import { Space, Tag } from 'antd';

/**
 * Detalle de una actividad registrada (UserActivity.metadata): la estación
 * consultada, el formato descargado, la fecha de vigencia, etc. Se renderiza
 * como etiquetas para que la traza se lea de un vistazo.
 */
export default function ActivityMetadata({ metadata }) {
    if (!metadata || typeof metadata !== 'object') return '—';

    const entries = Object.entries(metadata).filter(
        ([, value]) => value !== null && value !== undefined && value !== '',
    );

    if (!entries.length) return '—';

    const format = (value) => (Array.isArray(value) ? value.join(', ') : String(value));

    return (
        <Space size={[4, 4]} wrap>
            {entries.map(([key, value]) => (
                <Tag key={key}>
                    {key.replace(/_/g, ' ')}: {format(value)}
                </Tag>
            ))}
        </Space>
    );
}
