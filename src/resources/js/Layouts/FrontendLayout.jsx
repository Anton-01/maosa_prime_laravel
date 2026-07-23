import React, { useMemo, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Button,
    ConfigProvider,
    Drawer,
    Dropdown,
    Grid,
    Layout,
    Menu,
    Space,
    Typography,
} from 'antd';
import {
    DownOutlined,
    LoginOutlined,
    MailOutlined,
    MenuOutlined,
    PhoneOutlined,
    UserOutlined,
} from '@ant-design/icons';

import asset from '../Utils/asset';
import useFlash from '../Hooks/useFlash';

const { Header, Content, Footer } = Layout;
const { Text, Title } = Typography;
const { useBreakpoint } = Grid;

function isExternal(link) {
    return /^https?:\/\//i.test(link);
}

/** Render a menu link as an Inertia <Link> for internal URLs, <a> otherwise. */
function MenuLink({ link, children }) {
    if (!link || link === '#') return <span>{children}</span>;
    if (isExternal(link)) {
        return (
            <a href={link} target="_blank" rel="noreferrer">
                {children}
            </a>
        );
    }
    return <Link href={link}>{children}</Link>;
}

function buildMenuItems(items = []) {
    return items.map((item, index) => {
        const key = `${item.label}-${index}`;
        if (item.children && item.children.length > 0) {
            return {
                key,
                label: item.label,
                children: item.children.map((child, i) => ({
                    key: `${key}-${i}`,
                    label: <MenuLink link={child.link}>{child.label}</MenuLink>,
                })),
            };
        }
        return { key, label: <MenuLink link={item.link}>{item.label}</MenuLink> };
    });
}

export default function FrontendLayout({ children }) {
    useFlash();
    const { settings, siteMenu, footerInfo, auth, appUrls } = usePage().props;
    const screens = useBreakpoint();
    const [drawerOpen, setDrawerOpen] = useState(false);

    const brand = settings?.color || '#6777ef';
    const mainMenu = siteMenu?.main ?? [];
    const menuItems = useMemo(() => buildMenuItems(mainMenu), [mainMenu]);
    const isMobile = !screens.md;

    const accountControl = auth?.user ? (
        <Link href={appUrls.userDashboard}>
            <Button type="primary" ghost icon={<UserOutlined />}>
                Mi Panel
            </Button>
        </Link>
    ) : (
        <Link href={appUrls.login}>
            <Button type="primary" icon={<LoginOutlined />}>
                Iniciar sesión
            </Button>
        </Link>
    );

    return (
        <ConfigProvider theme={{ token: { colorPrimary: brand } }}>
            <Layout style={{ minHeight: '100vh', background: '#f5f6fa', ['--brand-color']: brand }}>
                {/* Top contact strip */}
                <div style={{ background: brand, color: '#fff', fontSize: 13 }}>
                    <div
                        style={{
                            maxWidth: 1200,
                            margin: '0 auto',
                            padding: '6px 24px',
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            flexWrap: 'wrap',
                            gap: 8,
                        }}
                    >
                        <Space size="large" wrap>
                            {settings?.email && (
                                <a href={`mailto:${settings.email}`} style={{ color: '#fff' }}>
                                    <MailOutlined /> {settings.email}
                                </a>
                            )}
                            {settings?.phone && (
                                <a href={`tel:${settings.phone}`} style={{ color: '#fff' }}>
                                    <PhoneOutlined /> {settings.phone}
                                </a>
                            )}
                        </Space>
                    </div>
                </div>

                <Header
                    style={{
                        position: 'sticky',
                        top: 0,
                        zIndex: 20,
                        background: '#fff',
                        boxShadow: '0 2px 12px rgba(0,0,0,0.06)',
                        padding: 0,
                        height: 'auto',
                        lineHeight: 'normal',
                    }}
                >
                    <div
                        style={{
                            maxWidth: 1200,
                            margin: '0 auto',
                            padding: '10px 24px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            gap: 16,
                        }}
                    >
                        <Link href={appUrls.home} style={{ display: 'flex', alignItems: 'center' }}>
                            {settings?.logo ? (
                                <img
                                    src={asset(settings.logo)}
                                    alt={settings?.siteName}
                                    style={{ height: 42, objectFit: 'contain' }}
                                />
                            ) : (
                                <Title level={4} style={{ margin: 0, color: brand }}>
                                    {settings?.siteName}
                                </Title>
                            )}
                        </Link>

                        {!isMobile && (
                            <Menu
                                mode="horizontal"
                                items={menuItems}
                                selectable={false}
                                style={{
                                    flex: 1,
                                    justifyContent: 'center',
                                    borderBottom: 'none',
                                    background: 'transparent',
                                }}
                                overflowedIndicator={<DownOutlined />}
                            />
                        )}

                        {!isMobile ? (
                            accountControl
                        ) : (
                            <Button
                                type="text"
                                icon={<MenuOutlined style={{ fontSize: 20 }} />}
                                onClick={() => setDrawerOpen(true)}
                                aria-label="Abrir menú"
                            />
                        )}
                    </div>
                </Header>

                <Drawer
                    title={settings?.siteName}
                    placement="right"
                    open={drawerOpen}
                    onClose={() => setDrawerOpen(false)}
                    styles={{ body: { padding: 0 } }}
                >
                    <Menu
                        mode="inline"
                        items={menuItems}
                        selectable={false}
                        onClick={() => setDrawerOpen(false)}
                        style={{ borderInlineEnd: 'none' }}
                    />
                    <div style={{ padding: 16 }}>{accountControl}</div>
                </Drawer>

                <Content>
                    <div style={{ maxWidth: 1200, margin: '0 auto', width: '100%' }}>{children}</div>
                </Content>

                <Footer style={{ background: '#0f1729', color: 'rgba(255,255,255,0.75)', padding: 0 }}>
                    <div style={{ maxWidth: 1200, margin: '0 auto', padding: '48px 24px 24px' }}>
                        <div
                            style={{
                                display: 'grid',
                                gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
                                gap: 32,
                            }}
                        >
                            <div>
                                <Title level={5} style={{ color: '#fff' }}>
                                    Sobre Nosotros
                                </Title>
                                <div
                                    className="rich-content"
                                    style={{ color: 'rgba(255,255,255,0.6)' }}
                                    dangerouslySetInnerHTML={{ __html: footerInfo?.shortDescription || '' }}
                                />
                            </div>

                            <FooterMenuColumn title="Mi cuenta" items={siteMenu?.footerOne} />
                            <FooterMenuColumn title="Enlaces útiles" items={siteMenu?.footerTwo} />

                            <div>
                                <Title level={5} style={{ color: '#fff' }}>
                                    Información
                                </Title>
                                <Space direction="vertical" size="small">
                                    {footerInfo?.address && (
                                        <Text style={{ color: 'rgba(255,255,255,0.6)' }}>
                                            {footerInfo.address}
                                        </Text>
                                    )}
                                    {footerInfo?.email && (
                                        <a href={`mailto:${footerInfo.email}`} style={{ color: 'rgba(255,255,255,0.75)' }}>
                                            <MailOutlined /> {footerInfo.email}
                                        </a>
                                    )}
                                    {footerInfo?.phone && (
                                        <a href={`tel:${footerInfo.phone}`} style={{ color: 'rgba(255,255,255,0.75)' }}>
                                            <PhoneOutlined /> {footerInfo.phone}
                                        </a>
                                    )}
                                </Space>
                            </div>
                        </div>

                        <div
                            style={{
                                marginTop: 32,
                                paddingTop: 20,
                                borderTop: '1px solid rgba(255,255,255,0.1)',
                                textAlign: 'center',
                                color: 'rgba(255,255,255,0.5)',
                                fontSize: 13,
                            }}
                        >
                            {footerInfo?.copyright ||
                                `© ${new Date().getFullYear()} ${settings?.siteName}`}
                        </div>
                    </div>
                </Footer>
            </Layout>
        </ConfigProvider>
    );
}

function FooterMenuColumn({ title, items = [] }) {
    if (!items || items.length === 0) return null;
    return (
        <div>
            <Title level={5} style={{ color: '#fff' }}>
                {title}
            </Title>
            <Space direction="vertical" size="small">
                {items.map((item, index) => (
                    <span key={index} style={{ color: 'rgba(255,255,255,0.75)' }}>
                        <MenuLink link={item.link}>
                            <span style={{ color: 'rgba(255,255,255,0.75)' }}>{item.label}</span>
                        </MenuLink>
                    </span>
                ))}
            </Space>
        </div>
    );
}
