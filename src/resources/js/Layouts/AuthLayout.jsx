import React from 'react';
import { usePage } from '@inertiajs/react';
import { Card, Flex, Layout, Typography, theme } from 'antd';

import useFlash from '../Hooks/useFlash';
import useTranslation from '../Hooks/useTranslation';
import LocaleSwitcher from '../Components/LocaleSwitcher';

const { Title, Text } = Typography;

/**
 * Minimal centered layout for guest screens (login, forgot password).
 */
export default function AuthLayout({ title, subtitle, children }) {
    useFlash();
    const { appName } = usePage().props;
    const { token } = theme.useToken();
    const { t } = useTranslation();

    return (
        <Layout style={{ minHeight: '100vh' }}>
            <Flex
                align="center"
                justify="center"
                style={{ minHeight: '100vh', padding: 16, background: token.colorBgLayout, position: 'relative' }}
            >
                <div style={{ position: 'absolute', top: 16, right: 16 }}>
                    <LocaleSwitcher />
                </div>

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
                        Copyright © {new Date().getFullYear()} · {t('header.designed_by')} BullUp
                    </Text>
                </Flex>
            </Flex>
        </Layout>
    );
}
