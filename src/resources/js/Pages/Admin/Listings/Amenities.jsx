import React, { useEffect, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Button,
    Flex,
    Form,
    Input,
    Modal,
    Transfer,
    Typography,
} from 'antd';
import {
    ArrowLeftOutlined,
    PlusOutlined,
    SaveOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

export default function Amenities({ listing, amenities, assignedAmenityIds, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [targetKeys, setTargetKeys] = useState(assignedAmenityIds.map(String));
    const [saving, setSaving] = useState(false);
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [creating, setCreating] = useState(false);

    // Refresh the selection when the server sends fresh props
    // (e.g. after creating a new amenity).
    useEffect(() => {
        setTargetKeys(assignedAmenityIds.map(String));
    }, [assignedAmenityIds]);

    const dataSource = amenities.map((amenity) => ({
        key: String(amenity.id),
        title: amenity.name,
    }));

    const handleSave = () => {
        router.put(urls.update, { amenities: targetKeys.map(Number) }, {
            preserveScroll: true,
            onStart: () => setSaving(true),
            onFinish: () => setSaving(false),
        });
    };

    const handleCreate = (values) => {
        router.post(urls.create, values, {
            preserveScroll: true,
            onStart: () => setCreating(true),
            onFinish: () => setCreating(false),
            onSuccess: () => {
                setCreateModalOpen(false);
                form.resetFields();
            },
        });
    };

    return (
        <PageContainer
            title={t('listings.amenities_title', { title: listing.title })}
            breadcrumbItems={[
                { title: t('nav.suppliers') },
                { title: t('listings.title'), href: urls.listings },
                { title: t('listings.amenities') },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.listings}>
                        <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                    </Link>
                    <Button icon={<PlusOutlined />} onClick={() => setCreateModalOpen(true)}>
                        {t('listings.new_amenity')}
                    </Button>
                    <Button type="primary" icon={<SaveOutlined />} loading={saving} onClick={handleSave}>
                        {t('listings.save_changes')}
                    </Button>
                </Flex>
            }
        >
            <Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
                {t('listings.amenities_hint')}
            </Text>

            <Transfer
                dataSource={dataSource}
                targetKeys={targetKeys}
                onChange={(keys) => setTargetKeys(keys.map(String))}
                render={(item) => item.title}
                showSearch
                filterOption={(input, item) => item.title.toLowerCase().includes(input.toLowerCase())}
                titles={[t('listings.available'), t('listings.assigned')]}
                listStyle={{ width: '100%', minWidth: 260, height: 420 }}
                locale={{
                    itemUnit: t('listings.amenity_unit'),
                    itemsUnit: t('listings.amenity_units'),
                    notFoundContent: t('listings.no_amenities'),
                    searchPlaceholder: t('listings.search_amenity'),
                }}
            />

            <Modal
                title={t('listings.new_amenity')}
                open={createModalOpen}
                onCancel={() => setCreateModalOpen(false)}
                onOk={() => form.submit()}
                okText={t('listings.create_and_assign')}
                cancelText={t('common.cancel')}
                confirmLoading={creating}
                destroyOnHidden
            >
                <Form form={form} layout="vertical" onFinish={handleCreate} disabled={creating}>
                    <Form.Item
                        label={t('users.name')}
                        name="name"
                        rules={[{ required: true, message: t('common.name_required') }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Form>
            </Modal>
        </PageContainer>
    );
}
