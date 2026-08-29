import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Card, ColorPicker, Form, Image, Input, Space, Tabs, Typography, Upload } from 'antd';
import {
    BgColorsOutlined,
    PictureOutlined,
    SaveOutlined,
    SettingOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ImageDropUpload from '../../../Components/ImageDropUpload';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

function GeneralSettingsForm({ general, submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (values) => {
        router.post(submitUrl, values, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            initialValues={general}
            style={{ maxWidth: 640 }}
        >
            <Form.Item
                label={t('settings.site_name')}
                name="site_name"
                rules={[{ required: true, message: t('settings.site_name_required') }]}
                validateStatus={errors.site_name ? 'error' : undefined}
                help={errors.site_name}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item
                label={t('settings.site_email')}
                name="site_email"
                rules={[
                    { required: true, message: t('settings.site_email_required') },
                    { type: 'email', message: t('common.email_invalid') },
                ]}
                validateStatus={errors.site_email ? 'error' : undefined}
                help={errors.site_email}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item
                label={t('settings.site_phone')}
                name="site_phone"
                rules={[{ required: true, message: t('settings.site_phone_required') }]}
                validateStatus={errors.site_phone ? 'error' : undefined}
                help={errors.site_phone}
            >
                <Input maxLength={255} />
            </Form.Item>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    {t('common.update')}
                </Button>
            </Form.Item>
        </Form>
    );
}

function LogoSettingsForm({ logos, submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [submitting, setSubmitting] = useState(false);
    const [logoFile, setLogoFile] = useState(null);
    const [faviconFile, setFaviconFile] = useState(null);

    const handleSubmit = () => {
        router.post(
            submitUrl,
            {
                logo: logoFile,
                favicon: faviconFile,
                old_logo: logos.logo,
                old_favicon: logos.favicon,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => {
                    setSubmitting(false);
                    setLogoFile(null);
                    setFaviconFile(null);
                },
            },
        );
    };

    return (
        <Space direction="vertical" size="large" style={{ display: 'flex', maxWidth: 640 }}>
            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    {t('settings.logo')}
                </Text>
                <ImageDropUpload
                    value={logoFile}
                    onChange={setLogoFile}
                    currentUrl={logos.logoUrl}
                    height={140}
                />
                {errors.logo && <Text type="danger">{errors.logo}</Text>}
            </div>

            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    {t('settings.favicon')}
                </Text>
                <ImageDropUpload
                    value={faviconFile}
                    onChange={setFaviconFile}
                    currentUrl={logos.faviconUrl}
                    height={120}
                />
                {errors.favicon && <Text type="danger">{errors.favicon}</Text>}
            </div>

            <Button
                type="primary"
                icon={<SaveOutlined />}
                loading={submitting}
                disabled={!logoFile && !faviconFile}
                onClick={handleSubmit}
            >
                {t('common.update')}
            </Button>
        </Space>
    );
}

function AppearanceSettingsForm({ appearance, submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [color, setColor] = useState(appearance.site_default_color || '#6777ef');
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = () => {
        router.post(submitUrl, { site_default_color: color }, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <Space direction="vertical" size="large" style={{ display: 'flex', maxWidth: 640 }}>
            <div>
                <Text strong style={{ display: 'block', marginBottom: 8 }}>
                    {t('settings.default_color')}
                </Text>
                <Space>
                    <ColorPicker
                        value={color}
                        showText
                        onChangeComplete={(value) => setColor(value.toHexString())}
                    />
                    <Text type="secondary">{color}</Text>
                </Space>
                {errors.site_default_color && (
                    <Text type="danger" style={{ display: 'block', marginTop: 8 }}>
                        {errors.site_default_color}
                    </Text>
                )}
            </div>

            <Button type="primary" icon={<SaveOutlined />} loading={submitting} onClick={handleSubmit}>
                {t('common.update')}
            </Button>
        </Space>
    );
}

export default function Index({ initialTab, general, logos, appearance, urls }) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState(initialTab || 'general');

    const handleTabChange = (tabKey) => {
        setActiveTab(tabKey);
        window.history.replaceState(window.history.state, '', `?tab=${tabKey}`);
    };

    const tabItems = [
        {
            key: 'general',
            label: t('settings.tab_general'),
            icon: <SettingOutlined />,
            children: <GeneralSettingsForm general={general} submitUrl={urls.generalUpdate} />,
        },
        {
            key: 'logos',
            label: t('settings.tab_logos'),
            icon: <PictureOutlined />,
            children: <LogoSettingsForm logos={logos} submitUrl={urls.logoUpdate} />,
        },
        {
            key: 'appearance',
            label: t('settings.tab_appearance'),
            icon: <BgColorsOutlined />,
            children: (
                <AppearanceSettingsForm appearance={appearance} submitUrl={urls.appearanceUpdate} />
            ),
        },
    ];

    return (
        <PageContainer
            title={t('settings.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('settings.title') }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs
                    activeKey={activeTab}
                    onChange={handleTabChange}
                    items={tabItems}
                    tabPosition="left"
                />
            </Card>
        </PageContainer>
    );
}
