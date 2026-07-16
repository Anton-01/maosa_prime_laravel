import React from 'react';
import { usePage } from '@inertiajs/react';
import { Card, Flex, Layout, Typography, theme } from 'antd';

const { Title, Text } = Typography;

/**
 * Minimal centered layout for guest screens (login, forgot password).
 */
export default function AuthLayout({ title, subtitle, children }) {
    const { appName } = usePage().props;
    const { token } = theme.useToken();

    return (
        <Layout style={{ minHeight: '100vh' }}>
            <Flex
                align="center"
                justify="center"
                style={{ minHeight: '100vh', padding: 16, background: token.colorBgLayout }}
            >
                <Flex vertical align="center" gap={16} style={{ width: '100%', maxWidth: 400 }}>
                    <Title level={3} style={{ margin: 0, color: token.colorPrimary }}>
                        {appName}
                    </Title>

                    <Card style={{ width: '100%' }}>
                        <Flex vertical gap={4} style={{ marginBottom: 16 }}>
                            <Title level={4} style={{ margin: 0 }}>
                                {title}
                            </Title>
                            {subtitle && <Text type="secondary">{subtitle}</Text>}
                        </Flex>

                        {children}
                    </Card>

                    <Text type="secondary" style={{ fontSize: 12 }}>
                        Copyright © {new Date().getFullYear()} · Design By BullUp
                    </Text>
                </Flex>
            </Flex>
        </Layout>
    );
}
