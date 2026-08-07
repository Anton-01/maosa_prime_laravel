import React, { useCallback, useMemo, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Avatar,
    Button,
    ConfigProvider,
    Drawer,
    Grid,
    Layout,
    Menu,
    Space,
    Tooltip,
    Typography,
    theme,
} from 'antd';
import {
    AppstoreOutlined,
    DashboardOutlined,
    DollarOutlined,
    HomeOutlined,
    LogoutOutlined,
    MenuFoldOutlined,
    MenuOutlined,
    MenuUnfoldOutlined,
    UserOutlined,
} from '@ant-design/icons';

import asset from '../Utils/asset';
import classicFormPost from '../Utils/classicFormPost';
import useFlash from '../Hooks/useFlash';

const { Sider, Content, Header } = Layout;
const { Text, Title } = Typography;
const { useBreakpoint } = Grid;

const SIDER_WIDTH = 260;
const SIDER_COLLAPSED_WIDTH = 80;
const COLLAPSED_STORAGE_KEY = 'maosa:user-sidebar-collapsed';

export default function UserLayout({ children }) {
    useFlash();
    const { auth, appUrls, settings } = usePage().props;
    const screens = useBreakpoint();
    const [drawerOpen, setDrawerOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(() => {
        if (typeof window === 'undefined') return false;
        return window.localStorage.getItem(COLLAPSED_STORAGE_KEY) === '1';
    });

    const brand = settings?.color || '#6777ef';
    const user = auth?.user;
    const isMobile = !screens.lg;

    const toggleCollapsed = useCallback(() => {
        setCollapsed((previous) => {
            const next = !previous;
            try {
                window.localStorage.setItem(COLLAPSED_STORAGE_KEY, next ? '1' : '0');
            } catch (e) {
                // Storage may be unavailable (private mode); the toggle still works.
            }
            return next;
        });
    }, []);

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    const menuItems = useMemo(() => {
        const items = [];
        if (user?.is_admin) {
            items.push({
                key: '/admin/dashboard',
                icon: <DashboardOutlined />,
                // `title` is what the Menu shows as a tooltip while collapsed.
                title: 'Panel de administración',
                label: <a href={appUrls.dashboard}>Panel de administración</a>,
            });
        }
        items.push({
            key: appUrls.userProfile,
            icon: <UserOutlined />,
            title: 'Mi perfil',
            label: <Link href={appUrls.userProfile}>Mi perfil</Link>,
        });
        if (user?.can_view_price_table) {
            items.push({
                key: appUrls.priceTable,
                icon: <DollarOutlined />,
                title: 'Tabla de precios',
                label: <Link href={appUrls.priceTable}>Tabla de precios</Link>,
            });
        }
        items.push({
            key: appUrls.listings,
            icon: <AppstoreOutlined />,
            title: 'Ver proveedores',
            label: <Link href={appUrls.listings}>Ver proveedores</Link>,
        });
        items.push({
            key: 'home',
            icon: <HomeOutlined />,
            title: 'Ir al sitio',
            label: <Link href={appUrls.home}>Ir al sitio</Link>,
        });
        return items;
    }, [user, appUrls]);

    const selectedKeys = menuItems
        .map((item) => item.key)
        .filter((key) => key.startsWith('/') && currentPath.startsWith(key));

    // `compact` renders the icon-only variant used by the collapsed sider; the
    // mobile drawer always gets the full version.
    const renderSidebar = (compact = false) => (
        <div style={{ display: 'flex', flexDirection: 'column', minHeight: '100%' }}>
            <div style={{ padding: compact ? '20px 8px 12px' : '28px 20px 20px', textAlign: 'center' }}>
                <Avatar
                    size={compact ? 40 : 72}
                    src={user?.avatar ? asset(user.avatar) : undefined}
                    icon={<UserOutlined />}
                    style={{ border: '3px solid rgba(255,255,255,0.15)' }}
                />
                {!compact && (
                    <>
                        <Title level={5} style={{ color: '#fff', marginTop: 12, marginBottom: 2 }}>
                            {user?.name}
                        </Title>
                        <Text style={{ color: 'rgba(255,255,255,0.55)', fontSize: 12 }}>
                            {user?.email}
                        </Text>
                    </>
                )}
            </div>
            <Menu
                theme="dark"
                mode="inline"
                items={menuItems}
                selectedKeys={selectedKeys}
                onClick={() => setDrawerOpen(false)}
                style={{ background: 'transparent', borderInlineEnd: 'none' }}
            />
            <div style={{ padding: compact ? '16px 12px' : 16, marginTop: 8 }}>
                <Tooltip title={compact ? 'Cerrar sesión' : ''} placement="right">
                    <Button
                        danger
                        block
                        icon={<LogoutOutlined />}
                        aria-label="Cerrar sesión"
                        onClick={() => classicFormPost(appUrls.logout)}
                    >
                        {compact ? null : 'Cerrar sesión'}
                    </Button>
                </Tooltip>
            </div>
        </div>
    );

    return (
        <ConfigProvider theme={{ algorithm: theme.defaultAlgorithm, token: { colorPrimary: brand } }}>
            <Layout style={{ minHeight: '100vh', ['--brand-color']: brand }}>
                {!isMobile && (
                    <Sider
                        width={SIDER_WIDTH}
                        collapsible
                        collapsed={collapsed}
                        collapsedWidth={SIDER_COLLAPSED_WIDTH}
                        trigger={null}
                        style={{
                            background: '#0f1729',
                            position: 'sticky',
                            top: 0,
                            height: '100vh',
                            overflow: 'auto',
                        }}
                    >
                        <div
                            style={{
                                display: 'flex',
                                justifyContent: collapsed ? 'center' : 'flex-end',
                                padding: '12px 12px 0',
                            }}
                        >
                            <Tooltip
                                title={collapsed ? 'Expandir menú' : 'Contraer menú'}
                                placement="right"
                            >
                                <Button
                                    type="text"
                                    aria-label={collapsed ? 'Expandir menú' : 'Contraer menú'}
                                    onClick={toggleCollapsed}
                                    icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                                    style={{ color: 'rgba(255,255,255,0.75)' }}
                                />
                            </Tooltip>
                        </div>
                        {renderSidebar(collapsed)}
                    </Sider>
                )}

                <Layout style={{ background: '#f4f6f9' }}>
                    {isMobile && (
                        <Header
                            style={{
                                background: '#fff',
                                padding: '0 16px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'space-between',
                                boxShadow: '0 2px 8px rgba(0,0,0,0.06)',
                            }}
                        >
                            <Space>
                                <Button
                                    type="text"
                                    icon={<MenuOutlined />}
                                    onClick={() => setDrawerOpen(true)}
                                    aria-label="Abrir menú"
                                />
                                <Text strong>{settings?.siteName}</Text>
                            </Space>
                            <Avatar
                                size="small"
                                src={user?.avatar ? asset(user.avatar) : undefined}
                                icon={<UserOutlined />}
                            />
                        </Header>
                    )}

                    <Content style={{ padding: isMobile ? 16 : 24 }}>
                        <div
                            style={{
                                maxWidth: collapsed ? 1600 : 1280,
                                margin: '0 auto',
                                transition: 'max-width 0.2s',
                            }}
                        >
                            {children}
                        </div>
                    </Content>
                </Layout>

                <Drawer
                    placement="left"
                    open={drawerOpen}
                    onClose={() => setDrawerOpen(false)}
                    width={SIDER_WIDTH}
                    styles={{ body: { padding: 0, background: '#0f1729' }, header: { display: 'none' } }}
                >
                    {renderSidebar(false)}
                </Drawer>
            </Layout>
        </ConfigProvider>
    );
}
