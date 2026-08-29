import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Card,
    Col,
    Form,
    Image,
    Input,
    Row,
    Select,
    Space,
    Switch,
    Tabs,
    Typography,
    Upload,
} from 'antd';
import {
    ArrowLeftOutlined,
    DeleteOutlined,
    GlobalOutlined,
    InfoCircleOutlined,
    PlusOutlined,
    SaveOutlined,
    SearchOutlined,
    SettingOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import HtmlEditor from '../../../Components/HtmlEditor';
import ImageDropUpload from '../../../Components/ImageDropUpload';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

function ImageField({ label, currentUrl, file, onSelect, error, required }) {
    return (
        <Form.Item label={label} required={required} validateStatus={error ? 'error' : undefined} help={error}>
            <ImageDropUpload value={file} onChange={onSelect} currentUrl={currentUrl} height={180} />
        </Form.Item>
    );
}

export default function ListingForm({ listing, options, urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [thumbnailFile, setThumbnailFile] = useState(null);

    const isEditing = Boolean(listing);
    const hasErrors = Object.keys(errors).length > 0;

    const initialValues = isEditing
        ? {
              ...listing,
              social_links: listing.social_links?.length ? listing.social_links : [],
              status: listing.status === 1,
              is_featured: listing.is_featured === 1,
              is_verified: listing.is_verified === 1,
              is_previliged: listing.is_previliged === 1,
          }
        : {
              social_links: [],
              status: true,
              is_featured: false,
              is_verified: false,
              is_previliged: false,
          };

    const handleSubmit = (values) => {
        const payload = {
            title: values.title,
            category: values.category,
            location: values.location,
            address: values.address,
            phone: values.phone,
            email: values.email,
            website: values.website ?? '',
            description: values.description,
            google_map_embed_code: values.google_map_embed_code ?? '',
            seo_title: values.seo_title ?? '',
            seo_description: values.seo_description ?? '',
            status: values.status ? 1 : 0,
            is_featured: values.is_featured ? 1 : 0,
            is_verified: values.is_verified ? 1 : 0,
            is_previliged: values.is_previliged ? 1 : 0,
            social_links: (values.social_links ?? []).filter(
                (link) => link?.social_network_id && link?.url,
            ),
            image: imageFile,
            thumbnail_image: thumbnailFile,
        };

        const visitOptions = {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        };

        if (isEditing) {
            router.post(`${urls.base}/${listing.id}`, { _method: 'put', ...payload }, visitOptions);
        } else {
            router.post(urls.base, payload, visitOptions);
        }
    };

    const generalTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label={t('users.name')}
                    name="title"
                    rules={[{ required: true, message: t('common.name_required') }]}
                    validateStatus={errors.title ? 'error' : undefined}
                    help={errors.title}
                >
                    <Input maxLength={255} showCount />
                </Form.Item>

                <Form.Item
                    label={t('listings.category')}
                    name="category"
                    rules={[{ required: true, message: t('listings.category_required') }]}
                    validateStatus={errors.category ? 'error' : undefined}
                    help={errors.category}
                >
                    <Select
                        options={options.categories}
                        showSearch
                        optionFilterProp="label"
                        placeholder={t('listings.category_placeholder')}
                    />
                </Form.Item>

                <Form.Item
                    label={t('listings.location')}
                    name="location"
                    rules={[{ required: true, message: t('listings.location_required') }]}
                    validateStatus={errors.location ? 'error' : undefined}
                    help={errors.location}
                >
                    <Select
                        options={options.locations}
                        showSearch
                        optionFilterProp="label"
                        placeholder={t('listings.location_placeholder')}
                    />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <ImageField
                    label={t('listings.main_image')}
                    currentUrl={listing?.imageUrl}
                    file={imageFile}
                    onSelect={setImageFile}
                    error={errors.image}
                    required={!isEditing}
                />
                <ImageField
                    label={t('listings.thumbnail_image')}
                    currentUrl={listing?.thumbnailImageUrl}
                    file={thumbnailFile}
                    onSelect={setThumbnailFile}
                    error={errors.thumbnail_image}
                    required={!isEditing}
                />
            </Col>
            <Col xs={24}>
                <Form.Item
                    label={t('listings.description')}
                    name="description"
                    rules={[{ required: true, message: t('listings.description_required') }]}
                    validateStatus={errors.description ? 'error' : undefined}
                    help={errors.description}
                >
                    <HtmlEditor rows={10} placeholder={t('listings.description_placeholder')} />
                </Form.Item>
            </Col>
        </Row>
    );

    const contactTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label={t('listings.address')}
                    name="address"
                    rules={[{ required: true, message: t('listings.address_required') }]}
                    validateStatus={errors.address ? 'error' : undefined}
                    help={errors.address}
                >
                    <Input maxLength={255} />
                </Form.Item>

                <Form.Item
                    label={t('listings.phone')}
                    name="phone"
                    rules={[{ required: true, message: t('listings.phone_required') }]}
                    validateStatus={errors.phone ? 'error' : undefined}
                    help={errors.phone}
                >
                    <Input maxLength={255} />
                </Form.Item>

                <Form.Item
                    label={t('listings.email')}
                    name="email"
                    rules={[
                        { required: true, message: t('listings.email_required') },
                        { type: 'email', message: t('listings.email_invalid') },
                    ]}
                    validateStatus={errors.email ? 'error' : undefined}
                    help={errors.email}
                >
                    <Input maxLength={255} />
                </Form.Item>

                <Form.Item
                    label={t('listings.website')}
                    name="website"
                    validateStatus={errors.website ? 'error' : undefined}
                    help={errors.website}
                >
                    <Input placeholder="https://..." />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <Form.Item label={t('listings.social_links')}>
                    <Form.List name="social_links">
                        {(fields, { add, remove }) => (
                            <Space direction="vertical" size="small" style={{ display: 'flex' }}>
                                {fields.map(({ key, name, ...restField }) => (
                                    <Space key={key} align="baseline" wrap>
                                        <Form.Item
                                            {...restField}
                                            name={[name, 'social_network_id']}
                                            noStyle
                                        >
                                            <Select
                                                options={options.socialNetworks}
                                                placeholder={t('listings.social_network')}
                                                style={{ width: 160 }}
                                            />
                                        </Form.Item>
                                        <Form.Item {...restField} name={[name, 'url']} noStyle>
                                            <Input placeholder="https://..." style={{ width: 260 }} />
                                        </Form.Item>
                                        <Button
                                            type="text"
                                            danger
                                            icon={<DeleteOutlined />}
                                            onClick={() => remove(name)}
                                        />
                                    </Space>
                                ))}
                                <Button type="dashed" icon={<PlusOutlined />} onClick={() => add()}>
                                    {t('listings.add_social_link')}
                                </Button>
                            </Space>
                        )}
                    </Form.List>
                </Form.Item>

                <Form.Item
                    label={t('listings.maps_embed')}
                    name="google_map_embed_code"
                    validateStatus={errors.google_map_embed_code ? 'error' : undefined}
                    help={errors.google_map_embed_code}
                >
                    <Input.TextArea rows={4} placeholder='<iframe src="https://www.google.com/maps/..."></iframe>' />
                </Form.Item>
            </Col>
        </Row>
    );

    const seoTab = (
        <Row gutter={24}>
            <Col xs={24} lg={12}>
                <Form.Item
                    label={t('listings.seo_title')}
                    name="seo_title"
                    validateStatus={errors.seo_title ? 'error' : undefined}
                    help={errors.seo_title}
                >
                    <Input maxLength={255} showCount />
                </Form.Item>
            </Col>
            <Col xs={24} lg={12}>
                <Form.Item
                    label={t('listings.seo_description')}
                    name="seo_description"
                    validateStatus={errors.seo_description ? 'error' : undefined}
                    help={errors.seo_description}
                >
                    <Input.TextArea rows={3} maxLength={255} showCount />
                </Form.Item>
            </Col>
        </Row>
    );

    const settingsTab = (
        <Row gutter={[24, 8]}>
            <Col xs={12} md={6}>
                <Form.Item label={t('listings.active')} name="status" valuePropName="checked">
                    <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label={t('listings.featured')} name="is_featured" valuePropName="checked">
                    <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label={t('listings.verified')} name="is_verified" valuePropName="checked">
                    <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                </Form.Item>
            </Col>
            <Col xs={12} md={6}>
                <Form.Item label={t('listings.previliged')} name="is_previliged" valuePropName="checked">
                    <Switch checkedChildren={t('common.yes')} unCheckedChildren={t('common.no')} />
                </Form.Item>
            </Col>
        </Row>
    );

    const tabItems = [
        {
            key: 'general',
            label: t('listings.tab_general'),
            icon: <InfoCircleOutlined />,
            forceRender: true,
            children: generalTab,
        },
        {
            key: 'contact',
            label: t('listings.tab_contact'),
            icon: <GlobalOutlined />,
            forceRender: true,
            children: contactTab,
        },
        {
            key: 'seo',
            label: t('listings.tab_seo'),
            icon: <SearchOutlined />,
            forceRender: true,
            children: seoTab,
        },
        {
            key: 'settings',
            label: t('listings.tab_settings'),
            icon: <SettingOutlined />,
            forceRender: true,
            children: settingsTab,
        },
    ];

    return (
        <PageContainer
            title={
                isEditing
                    ? t('listings.edit_title', { title: listing.title })
                    : t('listings.create_title')
            }
            breadcrumbItems={[
                { title: t('nav.suppliers') },
                { title: t('listings.title'), href: urls.base },
                { title: isEditing ? t('users.breadcrumb_edit') : t('users.breadcrumb_create') },
            ]}
            extra={
                <Link href={urls.base}>
                    <Button icon={<ArrowLeftOutlined />}>{t('common.back_to_list')}</Button>
                </Link>
            }
            wrapInCard={false}
        >
            {hasErrors && (
                <Alert
                    type="error"
                    showIcon
                    style={{ marginBottom: 16 }}
                    message={t('listings.form_error_title')}
                    description={t('listings.form_error_description')}
                />
            )}

            <Card>
                <Form
                    form={form}
                    layout="vertical"
                    onFinish={handleSubmit}
                    disabled={submitting}
                    initialValues={initialValues}
                >
                    <Tabs items={tabItems} />

                    <Form.Item style={{ marginTop: 16, marginBottom: 0 }}>
                        <Button
                            type="primary"
                            htmlType="submit"
                            icon={<SaveOutlined />}
                            loading={submitting}
                        >
                            {isEditing ? t('listings.submit_update') : t('listings.submit_create')}
                        </Button>
                    </Form.Item>
                </Form>
            </Card>
        </PageContainer>
    );
}
