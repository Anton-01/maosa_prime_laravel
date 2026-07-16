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
    SafetyOutlined,
    SettingOutlined,
    ToolOutlined,
    UnorderedListOutlined,
    UserOutlined,
} from '@ant-design/icons';

const { Header, Sider, Content, Footer } = Layout;
const { Text } = Typography;
const { useBreakpoint } = Grid;

const NAVIGATION_ICONS = {
    dashboard: <DashboardOutlined />,
    sections: <AppstoreOutlined />,
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
 * Logout must be a classic form POST: the redirect target (login) is a
 * Blade page, which an Inertia visit cannot render.
 */
function submitLogout(logoutUrl) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = logoutUrl;

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = csrfToken;

    form.appendChild(tokenInput);
    document.body.appendChild(form);
    form.submit();
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
    const { appName, auth, navigation, urls, flash } = usePage().props;
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
            label: <a href={urls.profile}>Perfil</a>,
        },
        {
            key: 'settings',
            icon: <SettingOutlined />,
            label: <a href={urls.settings}>Configuración</a>,
        },
        { type: 'divider' },
        {
            key: 'logout',
            icon: <LogoutOutlined />,
            label: 'Cerrar sesión',
            danger: true,
            onClick: () => submitLogout(urls.logout),
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
                    <Link href={urls.dashboard} style={{ overflow: 'hidden' }}>
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

                    <Dropdown menu={{ items: userMenuItems }} placement="bottomRight" arrow>
                        <Space style={{ cursor: 'pointer' }}>
                            <Avatar
                                src={auth.user?.avatar || undefined}
                                icon={<UserOutlined />}
                                size="small"
                            />
                            {!screens.xs && <Text>Hola, {auth.user?.name}</Text>}
                        </Space>
                    </Dropdown>
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
