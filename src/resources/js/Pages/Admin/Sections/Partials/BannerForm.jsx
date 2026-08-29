import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Image, Input, Space, Typography, Upload } from 'antd';
import { SaveOutlined, UploadOutlined } from '@ant-design/icons';
import HtmlEditor from '../../../../Components/HtmlEditor';
import ImageDropUpload from '../../../../Components/ImageDropUpload';
import useTranslation from '../../../../Hooks/useTranslation';

const { Text } = Typography;

/**
 * Shared form for the private and public banners. Submits as a
 * multipart POST with method spoofing (the routes are PUT).
 */
export default function BannerForm({ hero, submitUrl, description }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [backgroundFile, setBackgroundFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(hero?.backgroundUrl ?? null);

    useEffect(() => {
        form.setFieldsValue({
            title: hero?.title ?? '',
            sub_title: hero?.subTitle ?? '',
        });
        setPreviewUrl(hero?.backgroundUrl ?? null);
        setBackgroundFile(null);
    }, [hero, form]);

    if (!hero) {
        return <Alert type="warning" showIcon message={t('sections.no_active_banner')} />;
    }

    const handleSelectFile = (file) => {
        setBackgroundFile(file);
        setPreviewUrl(URL.createObjectURL(file));

        // Prevent antd from uploading the file itself; Inertia sends it.
        return false;
    };

    const handleSubmit = (values) => {
        router.post(
            submitUrl,
            {
                _method: 'put',
                title: values.title,
                sub_title: values.sub_title,
                background: backgroundFile,
                old_background: hero.background,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => setSubmitting(false),
            },
        );
    };

    return (
        <Space direction="vertical" size="middle" style={{ display: 'flex', maxWidth: 720 }}>
            <Text type="secondary">{description}</Text>

            <Form form={form} layout="vertical" onFinish={handleSubmit} disabled={submitting}>
                <Form.Item
                    label={t('sections.background_image')}
                    validateStatus={errors.background ? 'error' : undefined}
                    help={errors.background}
                >
                    <ImageDropUpload
                        value={backgroundFile}
                        onChange={setBackgroundFile}
                        currentUrl={hero?.backgroundUrl}
                        height={220}
                    />
                </Form.Item>

                <Form.Item
                    label={t('common.title')}
                    name="title"
                    rules={[{ required: true, message: t('sections.title_required') }]}
                    validateStatus={errors.title ? 'error' : undefined}
                    help={errors.title}
                >
                    <Input maxLength={255} showCount placeholder={t('sections.banner_title')} />
                </Form.Item>

                <Form.Item
                    label={t('sections.subtitle')}
                    name="sub_title"
                    rules={[{ required: true, message: t('sections.subtitle_required') }]}
                    validateStatus={errors.sub_title ? 'error' : undefined}
                    help={errors.sub_title}
                >
                    <HtmlEditor rows={3} placeholder={t('sections.subtitle_placeholder')} />
                </Form.Item>

                <Form.Item>
                    <Button
                        type="primary"
                        htmlType="submit"
                        icon={<SaveOutlined />}
                        loading={submitting}
                    >
                        {t('common.update')}
                    </Button>
                </Form.Item>
            </Form>
        </Space>
    );
}
