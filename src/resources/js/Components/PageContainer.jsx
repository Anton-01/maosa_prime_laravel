import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Breadcrumb, Card, Flex, Space, Typography } from 'antd';
import { HomeOutlined } from '@ant-design/icons';

const { Title } = Typography;

/**
 * Standard wrapper for every migrated admin screen: sets the document
 * title, renders the breadcrumb trail, the page title, optional action
 * buttons (extra) and wraps the content in a Card.
 *
 * The breadcrumb always starts with a home icon linking to the admin
 * dashboard. Each item may carry an optional leading `icon`.
 *
 * breadcrumbItems: [
 *   { title: 'Proveedores', href: '/admin/listing', icon: <ShopOutlined /> },
 *   { title: 'Crear' },
 * ]
 */
export default function PageContainer({
    title,
    breadcrumbItems = [],
    extra = null,
    wrapInCard = true,
    children,
}) {
    const { appUrls } = usePage().props;

    const withIcon = (icon, node) =>
        icon ? (
            <Space size={4}>
                {icon}
                {node}
            </Space>
        ) : (
            node
        );

    const items = [
        {
            title: (
                <Link href={appUrls?.dashboard || '/admin/dashboard'} aria-label="Inicio">
                    <HomeOutlined />
                </Link>
            ),
        },
        ...breadcrumbItems.map((item) => ({
            title: item.href
                ? withIcon(item.icon, <Link href={item.href}>{item.title}</Link>)
                : withIcon(item.icon, item.title),
        })),
    ];

    return (
        <>
            <Head title={title} />

            <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                <Breadcrumb items={items} />

                <Flex justify="space-between" align="center" wrap gap="small">
                    <Title level={3} style={{ margin: 0 }}>
                        {title}
                    </Title>
                    {extra && <Space wrap>{extra}</Space>}
                </Flex>

                {wrapInCard ? <Card>{children}</Card> : children}
            </Space>
        </>
    );
}
