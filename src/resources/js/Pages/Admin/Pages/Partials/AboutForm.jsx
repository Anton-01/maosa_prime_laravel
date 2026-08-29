import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Form, Image, Input, Space, Typography, Upload } from 'antd';
import { SaveOutlined, UploadOutlined } from '@ant-design/icons';
import HtmlEditor from '../../../../Components/HtmlEditor';
import ImageDropUpload from '../../../../Components/ImageDropUpload';
import useTranslation from '../../../../Hooks/useTranslation';

const { Text } = Typography;

export default function AboutForm({ about, submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(about.imageUrl);

    const handleSelectFile = (file) => {
        setImageFile(file);
        setPreviewUrl(URL.createObjectURL(file));

        return false;
    };

    const handleSubmit = (values) => {
        router.post(
            submitUrl,
            {
                image: imageFile,
                old_image: about.image,
                video_url: values.video_url,
                description: values.description,
                button_url: values.button_url,
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
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            initialValues={{
                video_url: about.video_url,
                description: about.description ?? '',
                button_url: about.button_url,
            }}
        >
            <Form.Item
                label={t('pages.image')}
                validateStatus={errors.image ? 'error' : undefined}
                help={errors.image}
            >
                <ImageDropUpload
                    value={imageFile}
                    onChange={setImageFile}
                    currentUrl={about.imageUrl}
                    height={220}
                />
            </Form.Item>

            <Form.Item
                label={t('pages.video_url')}
                name="video_url"
                rules={[
                    { required: true, message: t('pages.video_url_required') },
                    { type: 'url', message: t('common.url_invalid') },
                ]}
                validateStatus={errors.video_url ? 'error' : undefined}
                help={errors.video_url}
            >
                <Input placeholder="https://..." />
            </Form.Item>

            <Form.Item
                label={t('common.description')}
                name="description"
                rules={[{ required: true, message: t('listings.description_required') }]}
                validateStatus={errors.description ? 'error' : undefined}
                help={errors.description}
            >
                <HtmlEditor placeholder={t('pages.about_description_placeholder')} />
            </Form.Item>

            <Form.Item
                label={t('pages.button_url')}
                name="button_url"
                rules={[{ type: 'url', message: t('common.url_invalid') }]}
                validateStatus={errors.button_url ? 'error' : undefined}
                help={errors.button_url}
            >
                <Input placeholder="https://..." />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    {t('common.update')}
                </Button>
            </Form.Item>
        </Form>
    );
}
