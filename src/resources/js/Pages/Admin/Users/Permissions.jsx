import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Card,
    Checkbox,
    Col,
    Divider,
    Flex,
    Row,
    Space,
    Tag,
    Tooltip,
    Typography,
} from 'antd';
import { ArrowLeftOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

/**
 * Assign direct permissions to a user. Permissions inherited from roles
 * are shown checked and disabled; only direct ones are editable.
 */
export default function Permissions({ user, allPermissionsGrouped, directPermissions, rolePermissions, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [selected, setSelected] = useState(new Set(directPermissions));
    const [submitting, setSubmitting] = useState(false);

    const roleSet = new Set(rolePermissions);
    const groupedEntries = Object.entries(allPermissionsGrouped ?? {});

    const togglePermission = (permission, checked) => {
        setSelected((previous) => {
            const next = new Set(previous);

            if (checked) {
                next.add(permission);
            } else {
                next.delete(permission);
            }

            return next;
        });
    };

    const handleSubmit = () => {
        router.put(
            urls.update,
            { permissions: [...selected] },
            {
                preserveScroll: true,
                onStart: () => setSubmitting(true),
                onFinish: () => setSubmitting(false),
            },
        );
    };

    return (
        <PageContainer
            title={t('users.permissions_title', { name: user.name })}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users'), href: urls.base },
                { title: t('users.direct_permissions') },
            ]}
            extra={
                <Space wrap>
                    <Link href={urls.show}>
                        <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_detail')}</Button>
                    </Link>
                    <Button
                        type="primary"
                        icon={<SaveOutlined />}
                        loading={submitting}
                        disabled={user.isSuperAdmin}
                        onClick={handleSubmit}
                    >
                        {t('users.permissions_save')}
                    </Button>
                </Space>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                {user.isSuperAdmin && (
                    <Alert
                        type="warning"
                        showIcon
                        message={t('users.permissions_super_admin')}
                    />
                )}

                {errors.permissions && <Alert type="error" showIcon message={errors.permissions} />}

                <Card>
                    <Space size={[4, 8]} wrap style={{ marginBottom: 8 }}>
                        <Text strong>{user.email}</Text>
                        {user.roles.map((role) => (
                            <Tag key={role} color="green">
                                {role}
                            </Tag>
                        ))}
                    </Space>
                    <Text type="secondary" style={{ display: 'block' }}>
                        {t('users.permissions_edit_hint')}
                    </Text>

                    {groupedEntries.map(([groupName, permissions]) => (
                        <React.Fragment key={groupName || 'general'}>
                            <Divider orientation="left" orientationMargin={0}>
                                {groupName || t('common.general')}
                            </Divider>
                            <Row gutter={[16, 8]}>
                                {permissions.map((permission) => {
                                    const inheritedFromRole = roleSet.has(permission);

                                    return (
                                        <Col xs={24} sm={12} lg={8} key={permission}>
                                            <Tooltip
                                                title={
                                                    inheritedFromRole
                                                        ? t('users.permissions_from_role')
                                                        : undefined
                                                }
                                            >
                                                <Checkbox
                                                    checked={inheritedFromRole || selected.has(permission)}
                                                    disabled={inheritedFromRole || user.isSuperAdmin}
                                                    onChange={(event) =>
                                                        togglePermission(permission, event.target.checked)
                                                    }
                                                >
                                                    {permission}
                                                </Checkbox>
                                            </Tooltip>
                                        </Col>
                                    );
                                })}
                            </Row>
                        </React.Fragment>
                    ))}
                </Card>
            </Flex>
        </PageContainer>
    );
}
