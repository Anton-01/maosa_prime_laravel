import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Button, ConfigProvider, theme } from 'antd';
import {
    CheckCircleFilled,
    LoginOutlined,
    SafetyCertificateOutlined,
    StarFilled,
} from '@ant-design/icons';

import HtmlContent from '../../Components/HtmlContent';

function PlatformScene() {
    // Offshore oil & gas platform, night scene (decorative).
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 580 560"
            preserveAspectRatio="xMidYMid slice"
            style={{ position: 'absolute', inset: 0, width: '100%', height: '100%' }}
            aria-hidden="true"
        >
            <defs>
                <linearGradient id="ghSky" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#020818" />
                    <stop offset="55%" stopColor="#0a1440" />
                    <stop offset="100%" stopColor="#111c5c" />
                </linearGradient>
                <linearGradient id="ghOcean" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#0b1748" />
                    <stop offset="45%" stopColor="#071030" />
                    <stop offset="100%" stopColor="#040c20" />
                </linearGradient>
                <radialGradient id="ghMoon" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stopColor="#fce96a" />
                    <stop offset="70%" stopColor="#f5ca18" />
                    <stop offset="100%" stopColor="#e6b000" />
                </radialGradient>
                <radialGradient id="ghMoonHalo" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stopColor="#f5c842" stopOpacity="0.35" />
                    <stop offset="100%" stopColor="#f5c842" stopOpacity="0" />
                </radialGradient>
                <radialGradient id="ghFlare" cx="50%" cy="0%" r="100%">
                    <stop offset="0%" stopColor="#ff8c00" />
                    <stop offset="55%" stopColor="#ff5000" stopOpacity="0.65" />
                    <stop offset="100%" stopColor="#ff3300" stopOpacity="0" />
                </radialGradient>
            </defs>

            <rect width="580" height="560" fill="url(#ghSky)" />

            <g opacity="0.88" fill="#fff">
                <circle cx="52" cy="40" r="1.4" /><circle cx="115" cy="55" r="1" />
                <circle cx="148" cy="22" r="1.2" /><circle cx="208" cy="15" r="1.4" />
                <circle cx="275" cy="9" r="1" /><circle cx="305" cy="58" r="1.2" />
                <circle cx="70" cy="95" r="1" /><circle cx="135" cy="112" r="1" />
                <circle cx="222" cy="98" r="1.2" /><circle cx="288" cy="78" r="1" />
                <circle cx="82" cy="142" r="1.2" /><circle cx="18" cy="36" r="0.8" />
                <circle cx="188" cy="46" r="0.8" /><circle cx="278" cy="56" r="0.8" />
            </g>

            <circle cx="448" cy="72" r="62" fill="url(#ghMoonHalo)" />
            <circle cx="448" cy="72" r="36" fill="url(#ghMoon)" />

            <rect y="355" width="580" height="205" fill="url(#ghOcean)" />
            <path
                d="M0,372 Q50,366 100,372 Q150,378 200,372 Q250,366 300,372 Q350,378 400,372 Q450,366 500,372 Q542,378 580,372 L580,382 L0,382 Z"
                fill="#152268"
                opacity="0.42"
            />

            {/* Legs */}
            <rect x="78" y="210" width="14" height="150" fill="#0e1852" />
            <rect x="194" y="210" width="14" height="150" fill="#0e1852" />
            <rect x="366" y="210" width="14" height="150" fill="#0e1852" />
            <rect x="488" y="210" width="14" height="150" fill="#0e1852" />
            <rect x="78" y="288" width="424" height="5" fill="#152268" />

            {/* Deck */}
            <rect x="56" y="182" width="468" height="8" fill="#1e2e7a" rx="1" />
            <rect x="58" y="193" width="464" height="22" fill="#142264" rx="2" />

            {/* Living quarters */}
            <rect x="130" y="92" width="92" height="100" fill="#0e1852" rx="2" />
            <g fill="#f5c842">
                <rect x="137" y="98" width="11" height="8" opacity="0.55" rx="1" />
                <rect x="169" y="98" width="11" height="8" opacity="0.68" rx="1" />
                <rect x="201" y="98" width="11" height="8" opacity="0.55" rx="1" />
                <rect x="153" y="130" width="11" height="8" opacity="0.65" rx="1" />
                <rect x="185" y="130" width="11" height="8" opacity="0.72" rx="1" />
                <rect x="137" y="162" width="11" height="8" opacity="0.58" rx="1" />
                <rect x="169" y="162" width="11" height="8" opacity="0.5" rx="1" />
            </g>

            {/* Derrick */}
            <line x1="338" y1="212" x2="361" y2="40" stroke="#152268" strokeWidth="4.5" strokeLinecap="round" />
            <line x1="392" y1="212" x2="372" y2="40" stroke="#152268" strokeWidth="4.5" strokeLinecap="round" />
            <line x1="344" y1="153" x2="386" y2="153" stroke="#152268" strokeWidth="2.5" />
            <line x1="350" y1="113" x2="380" y2="113" stroke="#152268" strokeWidth="2.5" />
            <circle cx="366" cy="33" r="3.5" fill="#ff3030" />

            {/* Flare */}
            <line x1="492" y1="208" x2="551" y2="130" stroke="#0e1852" strokeWidth="5.5" strokeLinecap="round" />
            <path d="M547,134 Q549,116 553,102 Q557,116 559,134 Z" fill="#ff7b00" opacity="0.9" />
            <ellipse cx="553" cy="126" rx="12" ry="20" fill="url(#ghFlare)" opacity="0.55" />
        </svg>
    );
}

export default function GuestLanding({ hero }) {
    const { settings, appUrls } = usePage().props;
    const brand = settings?.color || '#6777ef';

    return (
        <ConfigProvider theme={{ algorithm: theme.defaultAlgorithm, token: { colorPrimary: brand } }}>
            <Head title={settings?.siteName} />
            <div className="guest-landing-wrap" style={{ display: 'flex', minHeight: '100vh', overflow: 'hidden' }}>
                {/* Left — decorative scene */}
                <div
                    style={{
                        flex: '1 1 0',
                        position: 'relative',
                        background: '#060d28',
                        minWidth: 0,
                    }}
                    className="guest-landing-scene"
                >
                    <PlatformScene />
                    <div
                        style={{
                            position: 'absolute',
                            bottom: 0,
                            padding: '0 38px 30px',
                            zIndex: 2,
                            color: 'rgba(180,205,255,0.6)',
                            fontSize: 13,
                            lineHeight: 1.7,
                        }}
                    >
                        Plataforma integral para la gestión y monitoreo de precios y proveedores.
                    </div>
                </div>

                {/* Right — welcome */}
                <div
                    style={{
                        flex: '0 0 44%',
                        maxWidth: 560,
                        background: '#ded9f3',
                        display: 'flex',
                        flexDirection: 'column',
                        justifyContent: 'center',
                        padding: '50px 48px',
                        overflowY: 'auto',
                    }}
                    className="guest-landing-panel"
                >
                    <div
                        style={{
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: 7,
                            background: '#fff',
                            color: brand,
                            padding: '8px 16px',
                            borderRadius: 50,
                            fontWeight: 700,
                            fontSize: 12.5,
                            marginBottom: 20,
                            alignSelf: 'flex-start',
                        }}
                    >
                        <StarFilled /> Bienvenido a {settings?.siteName}
                    </div>

                    {hero?.title ? (
                        <HtmlContent
                            html={hero.title}
                            style={{ fontSize: 30, fontWeight: 800, color: '#1a1f5e', lineHeight: 1.2 }}
                        />
                    ) : (
                        <h1 style={{ fontSize: 30, fontWeight: 800, color: '#1a1f5e', lineHeight: 1.2 }}>
                            Gestión de <span style={{ color: brand }}>Hidrocarburos</span>
                        </h1>
                    )}

                    {hero?.sub_title && (
                        <HtmlContent
                            html={hero.sub_title}
                            style={{ color: '#5a5a72', margin: '10px 0 26px' }}
                        />
                    )}

                    <div style={{ marginBottom: 28 }}>
                        <Link href={appUrls.login}>
                            <Button type="primary" size="large" icon={<LoginOutlined />}>
                                Iniciar sesión
                            </Button>
                        </Link>
                    </div>

                    <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                        <span style={{ display: 'flex', alignItems: 'center', gap: 10, color: '#444' }}>
                            <CheckCircleFilled style={{ color: '#22c55e' }} /> Acceso a proveedores
                            exclusivos
                        </span>
                        <span style={{ display: 'flex', alignItems: 'center', gap: 10, color: '#444' }}>
                            <SafetyCertificateOutlined style={{ color: '#22c55e' }} /> Precios de
                            combustibles actualizados día con día
                        </span>
                    </div>
                </div>
            </div>

            <style>{`
                @media (max-width: 860px) {
                    .guest-landing-wrap { flex-direction: column !important; }
                    .guest-landing-scene { flex: 0 0 220px !important; }
                    .guest-landing-panel { flex: 1 1 0 !important; max-width: none !important; padding: 32px 24px !important; }
                }
            `}</style>
        </ConfigProvider>
    );
}

GuestLanding.layout = null;
