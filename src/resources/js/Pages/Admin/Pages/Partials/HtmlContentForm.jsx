import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Form, Typography } from 'antd';
import { SaveOutlined } from '@ant-design/icons';
import HtmlEditor from '../../../../Components/HtmlEditor';

const { Text } = Typography;

/** Shared form for pages that only edit one HTML content field. */
export default function HtmlContentForm({ content, submitUrl, description }) {
    const { errors } = usePage().props;
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
            initialValues={{ description: content ?? '' }}
        >
            <Text type="secondary">{description}</Text>

            <Form.Item
                name="description"
                rules={[{ required: true, message: 'El contenido es obligatorio.' }]}
                validateStatus={errors.description ? 'error' : undefined}
                help={errors.description}
                style={{ marginTop: 16 }}
            >
                <HtmlEditor rows={16} />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar
                </Button>
            </Form.Item>
        </Form>
    );
}
