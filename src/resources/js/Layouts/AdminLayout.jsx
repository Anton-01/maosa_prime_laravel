import React, { useEffect, useMemo, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    App,
    Avatar,
    Dropdown,
    Grid,
    Layout,
    Menu,
    Space,
    Typography,
    theme,
} from 'antd';
import {
    AppstoreOutlined,
    BarChartOutlined,
    DashboardOutlined,
    FileTextOutlined,
    InfoCircleOutlined,
    LogoutOutlined,
    MenuFoldOutlined,
    MenuUnfoldOutlined,
    ReadOutlined,
    SafetyOutlined,
    SettingOutlined,
    ToolOutlined,
    UnorderedListOutlined,
    UserOutlined,
} from '@ant-design/icons';

import classicFormPost from '../Utils/classicFormPost';
import asset from '../Utils/asset';
import { useThemeMode } from '../Providers/ThemeProvider';

const { Header, Sider, Content, Footer } = Layout;
const { Text } = Typography;
const { useBreakpoint } = Grid;

/** First letters of the user's name, used when there is no avatar image. */
function getInitials(name) {
    if (!name) return '';
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase();
}

/** Sun / moon theme toggle matching the reference top bar. */
function ThemeToggle() {
    const { mode, toggle } = useThemeMode();
    const isDark = mode === 'dark';

    return (
        <span
            role="button"
            tabIndex={0}
            aria-label={isDark ? 'Activar tema claro' : 'Activar tema oscuro'}
            onClick={toggle}
            onKeyDown={(event) => event.key === 'Enter' && toggle()}
            style={{ cursor: 'pointer', display: 'inline-flex', fontSize: 18, lineHeight: 1 }}
        >
            {isDark ? (
                // Moon
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path
                        d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"
                        fill="currentColor"
                    />
                </svg>
            ) : (
                // Sun
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4" fill="currentColor" stroke="none" />
                    <line x1="12" y1="2" x2="12" y2="4" />
                    <line x1="12" y1="20" x2="12" y2="22" />
                    <line x1="2" y1="12" x2="4" y2="12" />
                    <line x1="20" y1="12" x2="22" y2="12" />
                    <line x1="4.9" y1="4.9" x2="6.3" y2="6.3" />
                    <line x1="17.7" y1="17.7" x2="19.1" y2="19.1" />
                    <line x1="4.9" y1="19.1" x2="6.3" y2="17.7" />
                    <line x1="17.7" y1="6.3" x2="19.1" y2="4.9" />
                </svg>
            )}
        </span>
    );
}

const NAVIGATION_ICONS = {
    dashboard: <DashboardOutlined />,
    sections: <AppstoreOutlined />,
    blog: <ReadOutlined />,
    suppliers: <UnorderedListOutlined />,
    pages: <FileTextOutlined />,
    footer: <InfoCircleOutlined />,
    access: <SafetyOutlined />,
    statistics: <BarChartOutlined />,
    menus: <ToolOutlined />,
    settings: <SettingOutlined />,
};

/**
 * Screens already migrated to Inertia navigate with <Link> (SPA);
 * classic Blade screens need a full page load with a plain anchor.
 */
function renderNavigationLink(item) {
    if (!item.url) return item.label;

    return item.inertia ? (
        <Link href={item.url}>{item.label}</Link>
    ) : (
        <a href={item.url}>{item.label}</a>
    );
}

/**
 * Maps the navigation tree shared by HandleInertiaRequests
 * into Ant Design <Menu /> items.
 */
function buildMenuItems(navigation) {
    return navigation.map((item) => ({
        key: item.key,
        icon: NAVIGATION_ICONS[item.icon] ?? null,
        label: renderNavigationLink(item),
        children: item.children?.map((child) => ({
            key: child.key,
            label: renderNavigationLink(child),
        })),
    }));
}

/**
 * Logout must be a classic form POST: the redirect target may be a
 * Blade page, which an Inertia visit cannot render.
 */
function submitLogout(logoutUrl) {
    classicFormPost(logoutUrl);
}

function findSelectedKeys(navigation) {
    const selected = [];

    navigation.forEach((item) => {
        if (item.children) {
            item.children.forEach((child) => {
                if (child.active) selected.push(child.key);
            });
        } else if (item.active) {
            selected.push(item.key);
        }
    });

    return selected;
}

export default function AdminLayout({ children }) {
    const { appName, auth, navigation, appUrls, flash } = usePage().props;
    const { message } = App.useApp();
    const { token } = theme.useToken();
    const screens = useBreakpoint();

    const [collapsed, setCollapsed] = useState(false);
    const [openKeys, setOpenKeys] = useState(
        () => navigation.filter((item) => item.children && item.active).map((item) => item.key),
    );

    const menuItems = useMemo(() => buildMenuItems(navigation), [navigation]);
    const selectedKeys = useMemo(() => findSelectedKeys(navigation), [navigation]);

    // Keep the active submenu open after every Inertia navigation.
    useEffect(() => {
        const activeParents = navigation
            .filter((item) => item.children && item.active)
            .map((item) => item.key);

        setOpenKeys((previous) => [...new Set([...previous, ...activeParents])]);
    }, [navigation]);

    // Surface Laravel session flash messages as Ant Design messages.
    useEffect(() => {
        if (flash?.success) message.success(flash.success);
        if (flash?.error) message.error(flash.error);
        if (flash?.warning) message.warning(flash.warning);
        if (flash?.info) message.info(flash.info);
    }, [flash, message]);

    const userMenuItems = [
        {
            key: 'profile',
            icon: <UserOutlined />,
            label: <Link href={appUrls.profile}>Perfil</Link>,
        },
        {
            key: 'settings',
            icon: <SettingOutlined />,
            label: <Link href={appUrls.settings}>Configuración</Link>,
        },
        { type: 'divider' },
        {
            key: 'logout',
            icon: <LogoutOutlined />,
            label: 'Cerrar sesión',
            danger: true,
            onClick: () => submitLogout(appUrls.logout),
        },
    ];

    return (
        <Layout style={{ minHeight: '100vh' }}>
            <Sider
                collapsible
                collapsed={collapsed}
                trigger={null}
                breakpoint="lg"
                collapsedWidth={screens.xs ? 0 : 80}
                width={250}
                onBreakpoint={(broken) => setCollapsed(broken)}
                style={{
                    position: 'sticky',
                    top: 0,
                    height: '100vh',
                    overflow: 'auto',
                }}
            >
                <div
                    style={{
                        height: 64,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '0 16px',
                    }}
                >
                    <Link href={appUrls.dashboard} style={{ overflow: 'hidden' }}>
                        <Text
                            strong
                            style={{
                                color: '#ffffff',
                                fontSize: collapsed ? 16 : 18,
                                whiteSpace: 'nowrap',
                            }}
                        >
                            {collapsed ? appName?.substring(0, 2).toUpperCase() : appName}
                        </Text>
                    </Link>
                </div>

                <Menu
                    theme="dark"
                    mode="inline"
                    items={menuItems}
                    selectedKeys={selectedKeys}
                    openKeys={collapsed ? undefined : openKeys}
                    onOpenChange={setOpenKeys}
                />
            </Sider>

            <Layout>
                <Header
                    style={{
                        position: 'sticky',
                        top: 0,
                        zIndex: 10,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        padding: '0 24px',
                        boxShadow: token.boxShadowTertiary,
                    }}
                >
                    <span
                        role="button"
                        tabIndex={0}
                        aria-label={collapsed ? 'Expandir menú' : 'Contraer menú'}
                        onClick={() => setCollapsed(!collapsed)}
                        onKeyDown={(event) => event.key === 'Enter' && setCollapsed(!collapsed)}
                        style={{ cursor: 'pointer', fontSize: 18 }}
                    >
                        {collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                    </span>

                    <Space size="large" align="center">
                        <ThemeToggle />

                        <Dropdown
                            menu={{ items: userMenuItems }}
                            placement="bottomRight"
                            trigger={['click']}
                            dropdownRender={(menu) => (
                                <div
                                    style={{
                                        minWidth: 240,
                                        background: token.colorBgElevated,
                                        borderRadius: token.borderRadiusLG,
                                        boxShadow: token.boxShadowSecondary,
                                        overflow: 'hidden',
                                    }}
                                >
                                    <div style={{ display: 'flex', gap: 12, alignItems: 'center', padding: 16 }}>
                                        <Avatar
                                            size={44}
                                            src={auth.user?.avatar ? asset(auth.user.avatar) : undefined}
                                            style={{ backgroundColor: token.colorPrimary, flexShrink: 0 }}
                                        >
                                            {getInitials(auth.user?.name)}
                                        </Avatar>
                                        <div style={{ overflow: 'hidden' }}>
                                            <Text strong style={{ display: 'block' }} ellipsis>
                                                {auth.user?.name}
                                            </Text>
                                            <Text type="secondary" style={{ fontSize: 12 }} ellipsis>
                                                {auth.user?.email}
                                            </Text>
                                        </div>
                                    </div>
                                    {menu}
                                </div>
                            )}
                        >
                            <Avatar
                                src={auth.user?.avatar ? asset(auth.user.avatar) : undefined}
                                style={{ backgroundColor: token.colorPrimary, cursor: 'pointer' }}
                            >
                                {getInitials(auth.user?.name)}
                            </Avatar>
                        </Dropdown>
                    </Space>
                </Header>

                <Content style={{ padding: 24 }}>{children}</Content>

                <Footer style={{ textAlign: 'center', color: token.colorTextSecondary }}>
                    Copyright © {new Date().getFullYear()} · Design By{' '}
                    <a href="https://bullup.com.mx/" target="_blank" rel="noreferrer">
                        BullUp
                    </a>
                </Footer>
            </Layout>
        </Layout>
    );
}
