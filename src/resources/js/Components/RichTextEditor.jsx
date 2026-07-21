import React, { useEffect, useRef, useState } from 'react';
import { Button, Space, Tooltip, theme } from 'antd';
import {
    BoldOutlined,
    ClearOutlined,
    ItalicOutlined,
    LinkOutlined,
    OrderedListOutlined,
    StrikethroughOutlined,
    UnderlineOutlined,
    UnorderedListOutlined,
} from '@ant-design/icons';

/**
 * Lightweight WYSIWYG rich-text editor built only on Ant Design + a native
 * contentEditable surface (no third-party editor dependency). Emits HTML and
 * is compatible with antd Form.Item (value / onChange).
 */
export default function RichTextEditor({ value = '', onChange, rows = 8, placeholder }) {
    const { token } = theme.useToken();
    const editorRef = useRef(null);
    const focused = useRef(false);
    const [, force] = useState(0);

    // Sync external value into the editable surface only when it is not being
    // edited, to avoid clobbering the caret position while typing.
    useEffect(() => {
        const el = editorRef.current;
        if (el && !focused.current && value !== el.innerHTML) {
            el.innerHTML = value || '';
        }
    }, [value]);

    const emit = () => {
        onChange?.(editorRef.current?.innerHTML ?? '');
    };

    const exec = (command, arg) => {
        editorRef.current?.focus();
        document.execCommand(command, false, arg);
        emit();
        force((n) => n + 1);
    };

    const addLink = () => {
        const url = window.prompt('URL del enlace:', 'https://');
        if (url) exec('createLink', url);
    };

    const tools = [
        { icon: <BoldOutlined />, title: 'Negrita', action: () => exec('bold') },
        { icon: <ItalicOutlined />, title: 'Cursiva', action: () => exec('italic') },
        { icon: <UnderlineOutlined />, title: 'Subrayado', action: () => exec('underline') },
        { icon: <StrikethroughOutlined />, title: 'Tachado', action: () => exec('strikeThrough') },
        { divider: true },
        { label: 'H1', title: 'Título 1', action: () => exec('formatBlock', 'H2') },
        { label: 'H2', title: 'Título 2', action: () => exec('formatBlock', 'H3') },
        { label: 'P', title: 'Párrafo', action: () => exec('formatBlock', 'P') },
        { divider: true },
        { icon: <UnorderedListOutlined />, title: 'Lista', action: () => exec('insertUnorderedList') },
        { icon: <OrderedListOutlined />, title: 'Lista numerada', action: () => exec('insertOrderedList') },
        { icon: <LinkOutlined />, title: 'Enlace', action: addLink },
        { divider: true },
        { icon: <ClearOutlined />, title: 'Quitar formato', action: () => exec('removeFormat') },
    ];

    const minHeight = Math.max(rows, 3) * 24 + 16;

    return (
        <div
            style={{
                border: `1px solid ${token.colorBorder}`,
                borderRadius: token.borderRadius,
                overflow: 'hidden',
            }}
        >
            <div
                style={{
                    display: 'flex',
                    flexWrap: 'wrap',
                    gap: 2,
                    padding: 6,
                    borderBottom: `1px solid ${token.colorBorderSecondary}`,
                    background: token.colorFillQuaternary,
                }}
            >
                {tools.map((tool, index) =>
                    tool.divider ? (
                        <span
                            key={`d-${index}`}
                            style={{
                                width: 1,
                                margin: '2px 4px',
                                background: token.colorBorderSecondary,
                            }}
                        />
                    ) : (
                        <Tooltip key={tool.title} title={tool.title}>
                            <Button
                                size="small"
                                type="text"
                                icon={tool.icon}
                                onMouseDown={(e) => e.preventDefault()}
                                onClick={tool.action}
                                style={{ minWidth: 28, fontWeight: tool.label ? 700 : undefined }}
                            >
                                {tool.label}
                            </Button>
                        </Tooltip>
                    ),
                )}
            </div>

            <div
                ref={editorRef}
                contentEditable
                suppressContentEditableWarning
                dir="ltr"
                onInput={emit}
                onFocus={() => {
                    focused.current = true;
                }}
                onBlur={() => {
                    focused.current = false;
                    emit();
                }}
                data-placeholder={placeholder || 'Escribe aquí...'}
                className="rte-surface rich-content"
                style={{
                    minHeight,
                    maxHeight: 460,
                    overflowY: 'auto',
                    padding: '10px 14px',
                    outline: 'none',
                    lineHeight: 1.6,
                }}
            />
        </div>
    );
}
