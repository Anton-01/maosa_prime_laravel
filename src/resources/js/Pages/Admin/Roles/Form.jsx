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
    Input,
    Row,
    Typography,
} from 'antd';
import { ArrowLeftOutlined, SaveOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function RoleForm({ role, permissionsGrouped, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [roleName, setRoleName] = useState(role?.name ?? '');
    const [selected, setSelected] = useState(new Set(role?.permissions ?? []));
    const [submitting, setSubmitting] = useState(false);

    const isEditing = Boolean(role);
    const groupedEntries = Object.entries(permissionsGrouped ?? {});

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

    const toggleGroup = (permissions, checked) => {
        setSelected((previous) => {
            const next = new Set(previous);

            permissions.forEach((permission) => {
                if (checked) {
                    next.add(permission);
                } else {
                    next.delete(permission);
                }
            });

            return next;
        });
    };

    const handleSubmit = () => {
        const payload = {
            role_name: roleName,
            permissions: [...selected],
        };

        const visitOptions = {
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.put(`${urls.base}/${role.id}`, payload, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    return (
        <PageContainer
            title={
                isEditing ? t('roles.edit_title', { name: role.name }) : t('roles.create_title')
            }
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('roles.title'), href: urls.base },
                { title: isEditing ? t('users.breadcrumb_edit') : t('users.breadcrumb_create') },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                </Link>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                {errors.permissions && <Alert type="error" showIcon message={errors.permissions} />}

                <Card>
                    <Flex vertical gap={4} style={{ maxWidth: 420, marginBottom: 8 }}>
                        <Text strong>{t('roles.name')}</Text>
                        <Input
                            maxLength={40}
                            showCount
                            value={roleName}
                            onChange={(event) => setRoleName(event.target.value)}
                            placeholder={t('roles.name')}
                            status={errors.role_name ? 'error' : undefined}
                        />
                        {errors.role_name && <Text type="danger">{errors.role_name}</Text>}
                    </Flex>

                    {groupedEntries.map(([groupName, permissions]) => {
                        const selectedInGroup = permissions.filter((permission) =>
                            selected.has(permission),
                        ).length;
                        const allSelected = selectedInGroup === permissions.length;

                        return (
                            <React.Fragment key={groupName || 'general'}>
                                <Divider orientation="left" orientationMargin={0}>
                                    {groupName || t('common.general')}
                                    <Checkbox
                                        style={{ marginLeft: 12, fontWeight: 'normal' }}
                                        checked={allSelected}
                                        indeterminate={selectedInGroup > 0 && !allSelected}
                                        onChange={(event) => toggleGroup(permissions, event.target.checked)}
                                    >
                                        {t('common.select_all')}
                                    </Checkbox>
                                </Divider>
                                <Row gutter={[16, 8]}>
                                    {permissions.map((permission) => (
                                        <Col xs={24} sm={12} lg={8} key={permission}>
                                            <Checkbox
                                                checked={selected.has(permission)}
                                                onChange={(event) =>
                                                    togglePermission(permission, event.target.checked)
                                                }
                                            >
                                                {permission}
                                            </Checkbox>
                                        </Col>
                                    ))}
                                </Row>
                            </React.Fragment>
                        );
                    })}

                    <Button
                        type="primary"
                        icon={<SaveOutlined />}
                        loading={submitting}
                        onClick={handleSubmit}
                        style={{ marginTop: 24 }}
                    >
                        {isEditing ? t('roles.submit_update') : t('roles.submit_create')}
                    </Button>
                </Card>
            </Flex>
        </PageContainer>
    );
}
