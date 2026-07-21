import React from 'react';

/**
 * Render trusted rich-text HTML stored in the database (page bodies, listing
 * descriptions, blog posts). Styled as readable prose via the `.rich-content`
 * class (see the injected style tag), independent of any Blade/Bootstrap CSS.
 */
export default function HtmlContent({ html, style }) {
    if (!html) return null;
    return (
        <div
            className="rich-content"
            style={style}
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
