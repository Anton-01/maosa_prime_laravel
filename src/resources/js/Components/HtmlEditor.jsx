import React from 'react';
import RichTextEditor from './RichTextEditor';

/**
 * Rich-text field for HTML content stored by the admin (page bodies, listing
 * and blog descriptions, section subtitles…). Kept as a thin alias over
 * RichTextEditor so every existing `<HtmlEditor />` usage becomes a real
 * WYSIWYG editor. Compatible with antd Form.Item (value / onChange).
 */
export default function HtmlEditor(props) {
    return <RichTextEditor {...props} />;
}
