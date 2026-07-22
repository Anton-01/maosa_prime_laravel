import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Button, Col, Form, Input, Row } from 'antd';
import { SaveOutlined } from '@ant-design/icons';

export default function ContactForm({ contact, submitUrl }) {
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
            initialValues={contact}
        >
            <Row gutter={24}>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Teléfono"
                        name="phone"
                        rules={[{ required: true, message: 'El teléfono es obligatorio.' }]}
                        validateStatus={errors.phone ? 'error' : undefined}
                        help={errors.phone}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label="Email"
                        name="email"
                        rules={[
                            { required: true, message: 'El email es obligatorio.' },
                            { type: 'email', message: 'Debe ser un email válido.' },
                        ]}
                        validateStatus={errors.email ? 'error' : undefined}
                        help={errors.email}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item
                label="Dirección"
                name="address"
                rules={[{ required: true, message: 'La dirección es obligatoria.' }]}
                validateStatus={errors.address ? 'error' : undefined}
                help={errors.address}
            >
                <Input.TextArea rows={2} maxLength={500} showCount />
            </Form.Item>

            <Form.Item
                label="Enlace del mapa (embed)"
                name="map_link"
                rules={[{ required: true, message: 'El enlace del mapa es obligatorio.' }]}
                validateStatus={errors.map_link ? 'error' : undefined}
                help={errors.map_link}
            >
                <Input.TextArea rows={3} placeholder="Código embed de Google Maps" />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    Actualizar
                </Button>
            </Form.Item>
        </Form>
    );
}
