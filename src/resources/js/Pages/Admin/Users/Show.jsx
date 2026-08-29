import React from 'react';
import { Link } from '@inertiajs/react';
import { Button, Card, Collapse, Descriptions, Empty, Flex, Space, Tag, Typography } from 'antd';
import { ArrowLeftOutlined, EditOutlined, KeyOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function Show({ user, urls }) {
    const { t } = useTranslation();
    const groupedEntries = Object.entries(user.allPermissionsGrouped ?? {});
    const directSet = new Set(user.directPermissions);

    const collapseItems = groupedEntries.map(([groupName, permissions]) => ({
        key: groupName || 'general',
        label: `${groupName || t('common.general')} (${permissions.length})`,
        children: (
            <Space size={[4, 8]} wrap>
                {permissions.map((permission) => (
                    <Tag key={permission} color={directSet.has(permission) ? 'default' : 'blue'}>
                        {permission}
                    </Tag>
                ))}
            </Space>
        ),
    }));

    return (
        <PageContainer
            title={t('users.show_title', { name: user.name })}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users'), href: urls.base },
                { title: t('users.breadcrumb_detail') },
            ]}
            extra={
                <Space wrap>
                    <Link href={urls.base}>
                        <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                    </Link>
                    <Link href={`${urls.base}/${user.id}/edit`}>
                        <Button icon={<EditOutlined />}>{t('common.edit')}</Button>
                    </Link>
                    <Link href={`${urls.permissionsBase}/${user.id}/edit`}>
                        <Button type="primary" icon={<KeyOutlined />}>
                            {t('users.direct_permissions')}
                        </Button>
                    </Link>
                </Space>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Card title={t('users.general_info')}>
                    <Descriptions
                        bordered
                        size="small"
                        column={{ xs: 1, md: 2 }}
                        items={[
                            { key: 'id', label: 'ID', children: user.id },
                            { key: 'name', label: t('users.name'), children: user.name },
                            { key: 'email', label: t('users.email'), children: user.email },
                            { key: 'userType', label: t('users.type'), children: user.userType || '—' },
                            { key: 'phone', label: t('users.phone'), children: user.phone || '—' },
                            { key: 'address', label: t('users.address'), children: user.address || '—' },
                            {
                                key: 'station',
                                label: t('users.station_id'),
                                children: user.stationId ?? '—',
                            },
                            {
                                key: 'partner',
                                label: t('users.partner_id'),
                                children: user.partnerId ?? '—',
                            },
                            {
                                key: 'roles',
                                label: t('users.roles'),
                                children: user.roles.length ? (
                                    <Space size={[4, 8]} wrap>
                                        {user.roles.map((role) => (
                                            <Tag key={role} color="green">
                                                {role}
                                            </Tag>
                                        ))}
                                    </Space>
                                ) : (
                                    <Tag>{t('users.no_role')}</Tag>
                                ),
                            },
                            {
                                key: 'approved',
                                label: t('users.approved'),
                                children: user.isApproved ? (
                                    <Tag color="success">{t('common.yes')}</Tag>
                                ) : (
                                    <Tag color="error">{t('common.no')}</Tag>
                                ),
                            },
                            {
                                key: 'priceTable',
                                label: t('users.international_prices'),
                                children: user.canViewPriceTable ? (
                                    <Tag color="success">{t('common.yes')}</Tag>
                                ) : (
                                    <Tag>{t('common.no')}</Tag>
                                ),
                            },
                            { key: 'createdAt', label: t('users.created_at'), children: user.createdAt },
                        ]}
                    />
                </Card>

                <Card
                    title={t('users.permissions')}
                    extra={
                        <Space size="small">
                            <Tag color="blue">
                                {t('users.permissions_by_role', { count: user.rolePermissions.length })}
                            </Tag>
                            <Tag color="default">
                                {t('users.permissions_direct', { count: user.directPermissions.length })}
                            </Tag>
                        </Space>
                    }
                >
                    {collapseItems.length > 0 ? (
                        <>
                            <Text type="secondary" style={{ display: 'block', marginBottom: 12 }}>
                                {t('users.permissions_hint')}
                            </Text>
                            <Collapse items={collapseItems} />
                        </>
                    ) : (
                        <Empty description={t('users.permissions_empty')} />
                    )}
                </Card>
            </Flex>
        </PageContainer>
    );
}
