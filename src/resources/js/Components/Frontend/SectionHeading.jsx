import React from 'react';
import { Typography } from 'antd';

const { Title, Paragraph } = Typography;

/** Centered section heading + subtitle used throughout the public site. */
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
                <Paragraph type="secondary" style={{ margin: 0, fontSize: 15 }}>
                    {subtitle}
                </Paragraph>
            )}
        </div>
    );
}
