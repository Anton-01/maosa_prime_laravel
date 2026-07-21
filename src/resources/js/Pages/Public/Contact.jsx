import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Button, Card, Col, Form, Input, Row, Typography } from 'antd';
import {
    EnvironmentOutlined,
    MailOutlined,
    PhoneOutlined,
    SendOutlined,
} from '@ant-design/icons';

import HtmlContent from '../../Components/HtmlContent';
import PageHeader from '../../Components/Frontend/PageHeader';

const { Title, Text } = Typography;

function ContactBox({ icon, children }) {
    return (
        <Card style={{ textAlign: 'center', height: '100%' }}>
            <div style={{ fontSize: 30, color: 'var(--brand-color, #6777ef)', marginBottom: 8 }}>
                {icon}
            </div>
            <Text style={{ fontSize: 15 }}>{children}</Text>
        </Card>
    );
}

export default function Contact({ contact }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
        honeypot: '',
    });

    const handleSubmit = () => {
        post('/contact', {
            preserveScroll: true,
            onSuccess: () => reset('name', 'email', 'subject', 'message'),
        });
    };

    return (
        <>
            <Head title="Contacto" />
            <PageHeader title="Contáctanos" breadcrumb={[{ label: 'Contacto' }]} />

            <div style={{ padding: '32px 24px' }}>
                <Row gutter={[24, 24]} style={{ marginBottom: 24 }}>
                    <Col xs={24} md={8}>
                        <ContactBox icon={<PhoneOutlined />}>
                            {contact?.phone ? <a href={`tel:${contact.phone}`}>{contact.phone}</a> : '—'}
                        </ContactBox>
                    </Col>
                    <Col xs={24} md={8}>
                        <ContactBox icon={<MailOutlined />}>
                            {contact?.email ? (
                                <a href={`mailto:${contact.email}`}>{contact.email}</a>
                            ) : (
                                '—'
                            )}
                        </ContactBox>
                    </Col>
                    <Col xs={24} md={8}>
                        <ContactBox icon={<EnvironmentOutlined />}>{contact?.address || '—'}</ContactBox>
                    </Col>
                </Row>

                <Card style={{ marginBottom: 24 }}>
                    <Title level={3}>Envíanos un mensaje</Title>
                    <Form layout="vertical" onFinish={handleSubmit} disabled={processing}>
                        <input
                            type="text"
                            value={data.honeypot}
                            onChange={(e) => setData('honeypot', e.target.value)}
                            tabIndex={-1}
                            autoComplete="off"
                            style={{ position: 'absolute', left: '-9999px' }}
                            aria-hidden="true"
                        />
                        <Row gutter={16}>
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label="Nombre"
                                    validateStatus={errors.name ? 'error' : undefined}
                                    help={errors.name}
                                >
                                    <Input
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                    />
                                </Form.Item>
                            </Col>
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label="Correo electrónico"
                                    validateStatus={errors.email ? 'error' : undefined}
                                    help={errors.email}
                                >
                                    <Input
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                    />
                                </Form.Item>
                            </Col>
                        </Row>
                        <Form.Item
                            label="Asunto"
                            validateStatus={errors.subject ? 'error' : undefined}
                            help={errors.subject}
                        >
                            <Input
                                value={data.subject}
                                onChange={(e) => setData('subject', e.target.value)}
                            />
                        </Form.Item>
                        <Form.Item
                            label="Mensaje"
                            validateStatus={errors.message ? 'error' : undefined}
                            help={errors.message}
                        >
                            <Input.TextArea
                                rows={5}
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                            />
                        </Form.Item>
                        <Button
                            type="primary"
                            htmlType="submit"
                            icon={<SendOutlined />}
                            loading={processing}
                        >
                            Enviar mensaje
                        </Button>
                    </Form>
                </Card>

                {contact?.map_link && (
                    <Card styles={{ body: { padding: 0, overflow: 'hidden' } }}>
                        <HtmlContent html={contact.map_link} />
                    </Card>
                )}
            </div>
        </>
    );
}
