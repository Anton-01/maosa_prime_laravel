import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Col, Form, Input, Row } from 'antd';
import { SaveOutlined } from '@ant-design/icons';
import useTranslation from '../../../../Hooks/useTranslation';

export default function ContactForm({ contact, submitUrl }) {
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
            initialValues={contact}
        >
            <Row gutter={24}>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('common.phone')}
                        name="phone"
                        rules={[{ required: true, message: t('common.phone_required') }]}
                        validateStatus={errors.phone ? 'error' : undefined}
                        help={errors.phone}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('common.email')}
                        name="email"
                        rules={[
                            { required: true, message: t('common.email_required') },
                            { type: 'email', message: t('common.email_invalid') },
                        ]}
                        validateStatus={errors.email ? 'error' : undefined}
                        help={errors.email}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item
                label={t('common.address')}
                name="address"
                rules={[{ required: true, message: t('common.address_required') }]}
                validateStatus={errors.address ? 'error' : undefined}
                help={errors.address}
            >
                <Input.TextArea rows={2} maxLength={500} showCount />
            </Form.Item>

            <Form.Item
                label={t('pages.map_link')}
                name="map_link"
                rules={[{ required: true, message: t('pages.map_link_required') }]}
                validateStatus={errors.map_link ? 'error' : undefined}
                help={errors.map_link}
            >
                <Input.TextArea rows={3} placeholder={t('pages.map_link_placeholder')} />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    {t('common.update')}
                </Button>
            </Form.Item>
        </Form>
    );
}
