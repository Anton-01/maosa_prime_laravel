import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Breadcrumb, Typography } from 'antd';
import { HomeOutlined } from '@ant-design/icons';

const { Title } = Typography;

/**
 * Breadcrumb banner shown at the top of interior public pages.
 * Optionally uses a background image (e.g. the listing thumbnail).
 */
export default function PageHeader({ title, breadcrumb = [], background }) {
    const { settings, appUrls } = usePage().props;
    const brand = settings?.color || '#6777ef';

    const backgroundStyle = background
        ? {
              backgroundImage: `linear-gradient(135deg, rgba(15,23,41,0.72), rgba(15,23,41,0.55)), url(${background})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
          }
        : { background: `linear-gradient(135deg, ${brand} 0%, #0f1729 120%)` };

    return (
        <div style={{ ...backgroundStyle, padding: '40px 24px' }}>
            <div
                style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'space-between',
                    flexWrap: 'wrap',
                    gap: 12,
                }}
            >
                <Title level={2} style={{ color: '#fff', margin: 0 }}>
                    {title}
                </Title>
                <Breadcrumb
                    items={[
                        {
                            title: (
                                <Link href={appUrls.home} style={{ color: 'rgba(255,255,255,0.85)' }}>
                                    <HomeOutlined /> Inicio
                                </Link>
                            ),
                        },
                        ...breadcrumb.map((b) => ({
                            title: b.href ? (
                                <Link href={b.href} style={{ color: 'rgba(255,255,255,0.85)' }}>
                                    {b.label}
                                </Link>
                            ) : (
                                <span style={{ color: '#fff' }}>{b.label}</span>
                            ),
                        })),
                    ]}
                />
            </div>
        </div>
    );
}
