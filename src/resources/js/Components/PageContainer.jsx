import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Breadcrumb, Card, Flex, Space, Typography } from 'antd';

const { Title } = Typography;

/**
 * Standard wrapper for every migrated admin screen: sets the document
 * title, renders the breadcrumb trail, the page title, optional action
 * buttons (extra) and wraps the content in a Card.
 *
 * breadcrumbItems: [{ title: 'Proveedores', href: '/admin/listing' }, { title: 'Crear' }]
 */
export default function PageContainer({
    title,
    breadcrumbItems = [],
    extra = null,
    wrapInCard = true,
    children,
}) {
    const items = breadcrumbItems.map((item) => ({
        title: item.href ? <Link href={item.href}>{item.title}</Link> : item.title,
    }));

    return (
        <>
            <Head title={title} />

            <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                {items.length > 0 && <Breadcrumb items={items} />}

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
