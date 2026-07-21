import React, { useMemo, useState } from 'react';
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
    Typography,
} from 'antd';
import {
    AppstoreOutlined,
    DashboardOutlined,
    DollarOutlined,
    HomeOutlined,
    LogoutOutlined,
    MenuOutlined,
    UserOutlined,
} from '@ant-design/icons';

import asset from '../Utils/asset';
import classicFormPost from '../Utils/classicFormPost';
import useFlash from '../Hooks/useFlash';

const { Sider, Content, Header } = Layout;
const { Text, Title } = Typography;
const { useBreakpoint } = Grid;

export default function UserLayout({ children }) {
    useFlash();
    const { auth, urls, settings } = usePage().props;
    const screens = useBreakpoint();
    const [drawerOpen, setDrawerOpen] = useState(false);

    const brand = settings?.color || '#6777ef';
    const user = auth?.user;
    const isMobile = !screens.lg;

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    const menuItems = useMemo(() => {
        const items = [];
        if (user?.is_admin) {
            items.push({
                key: '/admin/dashboard',
                icon: <DashboardOutlined />,
                label: <a href={urls.dashboard}>Panel de administración</a>,
            });
        }
        items.push({
            key: urls.userProfile,
            icon: <UserOutlined />,
            label: <Link href={urls.userProfile}>Mi perfil</Link>,
        });
        if (user?.can_view_price_table) {
            items.push({
                key: urls.priceTable,
                icon: <DollarOutlined />,
                label: <Link href={urls.priceTable}>Tabla de precios</Link>,
            });
        }
        items.push({
            key: urls.listings,
            icon: <AppstoreOutlined />,
            label: <Link href={urls.listings}>Ver proveedores</Link>,
        });
        items.push({
            key: 'home',
            icon: <HomeOutlined />,
            label: <Link href={urls.home}>Ir al sitio</Link>,
        });
        return items;
    }, [user, urls]);

    const selectedKeys = menuItems
        .map((item) => item.key)
        .filter((key) => key.startsWith('/') && currentPath.startsWith(key));

    const sidebarContent = (
        <>
            <div style={{ padding: '28px 20px 20px', textAlign: 'center' }}>
                <Avatar
                    size={72}
                    src={user?.avatar ? asset(user.avatar) : undefined}
                    icon={<UserOutlined />}
                    style={{ border: '3px solid rgba(255,255,255,0.15)' }}
                />
                <Title level={5} style={{ color: '#fff', marginTop: 12, marginBottom: 2 }}>
                    {user?.name}
                </Title>
                <Text style={{ color: 'rgba(255,255,255,0.55)', fontSize: 12 }}>{user?.email}</Text>
            </div>
            <Menu
                theme="dark"
                mode="inline"
                items={menuItems}
                selectedKeys={selectedKeys}
                onClick={() => setDrawerOpen(false)}
                style={{ background: 'transparent', borderInlineEnd: 'none' }}
            />
            <div style={{ padding: 16, marginTop: 8 }}>
                <Button
                    danger
                    block
                    icon={<LogoutOutlined />}
                    onClick={() => classicFormPost(urls.logout)}
                >
                    Cerrar sesión
                </Button>
            </div>
        </>
    );

    return (
        <ConfigProvider theme={{ token: { colorPrimary: brand } }}>
            <Layout style={{ minHeight: '100vh', ['--brand-color']: brand }}>
                {!isMobile && (
                    <Sider
                        width={260}
                        style={{
                            background: '#0f1729',
                            position: 'sticky',
                            top: 0,
                            height: '100vh',
                            overflow: 'auto',
                        }}
                    >
                        {sidebarContent}
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

                    <Content style={{ padding: isMobile ? 16 : 32 }}>
                        <div style={{ maxWidth: 1100, margin: '0 auto' }}>{children}</div>
                    </Content>
                </Layout>

                <Drawer
                    placement="left"
                    open={drawerOpen}
                    onClose={() => setDrawerOpen(false)}
                    width={260}
                    styles={{ body: { padding: 0, background: '#0f1729' }, header: { display: 'none' } }}
                >
                    {sidebarContent}
                </Drawer>
            </Layout>
        </ConfigProvider>
    );
}
