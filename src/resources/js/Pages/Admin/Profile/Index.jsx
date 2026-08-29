import React, { useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Avatar,
    Button,
    Card,
    Col,
    Form,
    Image,
    Input,
    Row,
    Space,
    Tabs,
    Typography,
    Upload,
} from 'antd';
import { LockOutlined, SaveOutlined, UploadOutlined, UserOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import ImageDropUpload from '../../../Components/ImageDropUpload';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

function ProfileForm({ profile, submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);
    const [avatarFile, setAvatarFile] = useState(null);
    const [bannerFile, setBannerFile] = useState(null);

    const handleSubmit = (values) => {
        router.post(
            submitUrl,
            {
                _method: 'put',
                ...values,
                avatar: avatarFile,
                banner: bannerFile,
            },
            {
                forceFormData: true,
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSubmitting(true),
                onFinish: () => {
                    setSubmitting(false);
                    setAvatarFile(null);
                    setBannerFile(null);
                },
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
                name: profile.name,
                email: profile.email,
                phone: profile.phone,
                address: profile.address,
            }}
        >
            <Row gutter={24}>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('profile.avatar')}
                        validateStatus={errors.avatar ? 'error' : undefined}
                        help={errors.avatar}
                    >
                        <ImageDropUpload
                            value={avatarFile}
                            onChange={setAvatarFile}
                            currentUrl={profile.avatarUrl}
                            height={160}
                        />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('profile.banner')}
                        validateStatus={errors.banner ? 'error' : undefined}
                        help={errors.banner}
                    >
                        <ImageDropUpload
                            value={bannerFile}
                            onChange={setBannerFile}
                            currentUrl={profile.bannerUrl}
                            height={160}
                        />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('users.name')}
                        name="name"
                        rules={[{ required: true, message: t('common.name_required') }]}
                        validateStatus={errors.name ? 'error' : undefined}
                        help={errors.name}
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
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('common.phone')}
                        name="phone"
                        rules={[{ required: true, message: t('common.phone_required') }]}
                        validateStatus={errors.phone ? 'error' : undefined}
                        help={errors.phone}
                    >
                        <Input maxLength={50} />
                    </Form.Item>
                </Col>
                <Col xs={24} md={12}>
                    <Form.Item
                        label={t('common.address')}
                        name="address"
                        rules={[{ required: true, message: t('common.address_required') }]}
                        validateStatus={errors.address ? 'error' : undefined}
                        help={errors.address}
                    >
                        <Input maxLength={255} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    {t('profile.submit_profile')}
                </Button>
            </Form.Item>
        </Form>
    );
}

function PasswordForm({ submitUrl }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [form] = Form.useForm();
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = (values) => {
        router.put(submitUrl, values, {
            preserveScroll: true,
            preserveState: true,
            onStart: () => setSubmitting(true),
            onFinish: () => setSubmitting(false),
            onSuccess: () => form.resetFields(),
        });
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={handleSubmit}
            disabled={submitting}
            style={{ maxWidth: 420 }}
        >
            <Form.Item
                label={t('profile.new_password')}
                name="password"
                rules={[
                    { required: true, message: t('users.password_required') },
                    { min: 5, message: t('profile.password_min', { count: 5 }) },
                ]}
                validateStatus={errors.password ? 'error' : undefined}
                help={errors.password}
            >
                <Input.Password autoComplete="new-password" />
            </Form.Item>

            <Form.Item
                label={t('users.password_confirm')}
                name="password_confirmation"
                dependencies={['password']}
                rules={[
                    { required: true, message: t('profile.confirm_password_required') },
                    ({ getFieldValue }) => ({
                        validator(_, value) {
                            if (!value || getFieldValue('password') === value) {
                                return Promise.resolve();
                            }

                            return Promise.reject(new Error(t('profile.password_mismatch')));
                        },
                    }),
                ]}
            >
                <Input.Password autoComplete="new-password" />
            </Form.Item>

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={submitting}>
                    {t('profile.submit_password')}
                </Button>
            </Form.Item>
        </Form>
    );
}

export default function Index({ profile, urls }) {
    const { t } = useTranslation();

    const tabItems = [
        {
            key: 'profile',
            label: t('profile.tab_info'),
            icon: <UserOutlined />,
            children: <ProfileForm profile={profile} submitUrl={urls.update} />,
        },
        {
            key: 'password',
            label: t('profile.tab_password'),
            icon: <LockOutlined />,
            children: <PasswordForm submitUrl={urls.passwordUpdate} />,
        },
    ];

    return (
        <PageContainer
            title={t('profile.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('profile.title') }]}
            wrapInCard={false}
        >
            <Card>
                <Tabs items={tabItems} />
            </Card>
        </PageContainer>
    );
}
