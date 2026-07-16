import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Alert, Button, Form, Image, Input, Space, Typography, Upload } from 'antd';
import { SaveOutlined, UploadOutlined } from '@ant-design/icons';

const { Text } = Typography;

/**
 * Shared form for the private and public banners. Submits as a
 * multipart POST with method spoofing (the routes are PUT).
 */
export default function BannerForm({ hero, submitUrl, description }) {
    const { errors } = usePage().props;
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
        return <Alert type="warning" showIcon message="No hay un banner activo configurado." />;
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
                    label="Imagen de fondo"
                    validateStatus={errors.background ? 'error' : undefined}
                    help={errors.background}
                >
                    <Space direction="vertical" size="small">
                        {previewUrl && (
                            <Image
                                src={previewUrl}
                                alt="Imagen de fondo del banner"
                                width={320}
                                style={{ borderRadius: 8, objectFit: 'cover' }}
                            />
                        )}
                        <Upload
                            accept="image/*"
                            maxCount={1}
                            showUploadList={false}
                            beforeUpload={handleSelectFile}
                        >
                            <Button icon={<UploadOutlined />}>Seleccionar imagen</Button>
                        </Upload>
                        {backgroundFile && <Text type="secondary">{backgroundFile.name}</Text>}
                    </Space>
                </Form.Item>

                <Form.Item
                    label="Título"
                    name="title"
                    rules={[{ required: true, message: 'El título es obligatorio.' }]}
                    validateStatus={errors.title ? 'error' : undefined}
                    help={errors.title}
                >
                    <Input maxLength={255} showCount placeholder="Título del banner" />
                </Form.Item>

                <Form.Item
                    label="Sub título"
                    name="sub_title"
                    rules={[{ required: true, message: 'El sub título es obligatorio.' }]}
                    validateStatus={errors.sub_title ? 'error' : undefined}
                    help={errors.sub_title}
                >
                    <Input.TextArea rows={3} maxLength={255} showCount placeholder="Sub título del banner" />
                </Form.Item>

                <Form.Item>
                    <Button
                        type="primary"
                        htmlType="submit"
                        icon={<SaveOutlined />}
                        loading={submitting}
                    >
                        Actualizar
                    </Button>
                </Form.Item>
            </Form>
        </Space>
    );
}
