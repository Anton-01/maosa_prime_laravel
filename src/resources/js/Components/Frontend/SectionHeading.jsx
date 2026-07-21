import React from 'react';
import { Typography } from 'antd';

const { Title } = Typography;

/**
 * Centered section heading + subtitle used throughout the public site.
 * The subtitle may contain rich HTML authored in the admin.
 */
export default function SectionHeading({ title, subtitle, style }) {
    if (!title && !subtitle) return null;
    return (
        <div style={{ textAlign: 'center', maxWidth: 620, margin: '0 auto 36px', ...style }}>
            {title && (
                <Title level={2} style={{ marginBottom: 8 }}>
                    {title}
                </Title>
            )}
            {subtitle && (
                <div
                    className="rich-content"
                    style={{ color: 'rgba(0,0,0,0.45)', fontSize: 15 }}
                    dangerouslySetInnerHTML={{ __html: subtitle }}
                />
            )}
        </div>
    );
}
