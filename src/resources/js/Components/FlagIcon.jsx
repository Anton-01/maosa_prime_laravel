import React from 'react';

/**
 * Banderas dibujadas en SVG en lugar de emoji: Windows no renderiza los
 * emoji de bandera y mostraría las letras "US" / "MX" en su lugar.
 */
export default function FlagIcon({ country, size = 16 }) {
    const common = {
        width: size,
        height: Math.round((size * 2) / 3),
        viewBox: '0 0 30 20',
        'aria-hidden': true,
        style: { borderRadius: 2, display: 'block', boxShadow: '0 0 0 1px rgba(0,0,0,0.08)' },
    };

    if (country === 'US') {
        return (
            <svg {...common}>
                <rect width="30" height="20" fill="#fff" />
                {[0, 2, 4, 6, 8, 10, 12].map((i) => (
                    <rect key={i} y={i * 1.538} width="30" height="1.538" fill="#b22234" />
                ))}
                <rect width="13" height="10.77" fill="#3c3b6e" />
                {[1.8, 5.4, 9].map((y) =>
                    [1.6, 4.4, 7.2, 10].map((x) => (
                        <circle key={`${x}-${y}`} cx={x} cy={y} r="0.7" fill="#fff" />
                    )),
                )}
            </svg>
        );
    }

    return (
        <svg {...common}>
            <rect width="10" height="20" fill="#006847" />
            <rect x="10" width="10" height="20" fill="#fff" />
            <rect x="20" width="10" height="20" fill="#ce1126" />
            <circle cx="15" cy="10" r="2.6" fill="none" stroke="#9a7b3f" strokeWidth="1.1" />
        </svg>
    );
}
