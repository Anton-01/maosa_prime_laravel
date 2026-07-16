import React from 'react';
import { Input, Tabs, theme } from 'antd';
import { CodeOutlined, EyeOutlined } from '@ant-design/icons';

/**
 * Editor for HTML content stored by the legacy rich-text fields.
 * Keeps the stack Ant Design-only: raw HTML editing plus a live
 * preview tab. Compatible with antd Form.Item (value/onChange).
 */
export default function HtmlEditor({ value = '', onChange, rows = 12, placeholder }) {
    const { token } = theme.useToken();

    const items = [
        {
            key: 'edit',
            label: 'Editar (HTML)',
            icon: <CodeOutlined />,
            children: (
                <Input.TextArea
                    value={value}
                    onChange={(event) => onChange?.(event.target.value)}
                    rows={rows}
                    placeholder={placeholder}
                    style={{ fontFamily: token.fontFamilyCode }}
                />
            ),
        },
        {
            key: 'preview',
            label: 'Vista previa',
            icon: <EyeOutlined />,
            children: (
                <div
                    style={{
                        border: `1px solid ${token.colorBorder}`,
                        borderRadius: token.borderRadius,
                        padding: 16,
                        minHeight: 120,
                        maxHeight: 420,
                        overflow: 'auto',
                    }}
                    // Content comes from the site's own admins and is already
                    // rendered as raw HTML on the public site.
                    dangerouslySetInnerHTML={{ __html: value || '<em>Sin contenido</em>' }}
                />
            ),
        },
    ];

    return <Tabs size="small" items={items} />;
}
