import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Alert, Button, Card, Flex, List, Typography, Upload } from 'antd';
import {
    ArrowLeftOutlined,
    DownloadOutlined,
    FileExcelOutlined,
    InboxOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;
const { Dragger } = Upload;

/** Claves de las notas de la pantalla, en el orden en que se listan. */
const IMPORT_NOTE_KEYS = ['note_columns', 'note_role', 'note_password', 'note_report'];

export default function Import({ urls }) {
    const { errors } = usePage().props;
    const { t } = useTranslation();
    const [file, setFile] = useState(null);
    const [uploading, setUploading] = useState(false);

    const handleImport = () => {
        router.post(urls.import, { excel_file: file }, {
            forceFormData: true,
            onStart: () => setUploading(true),
            onFinish: () => setUploading(false),
            onSuccess: () => setFile(null),
        });
    };

    return (
        <PageContainer
            title={t('user_import.title')}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users'), href: urls.users },
                { title: t('user_import.breadcrumb') },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.users}>
                        <Button icon={<ArrowLeftOutlined />}>{t('user_import.back_to_users')}</Button>
                    </Link>
                    <Button icon={<DownloadOutlined />} href={urls.layout}>
                        {t('user_import.download_layout')}
                    </Button>
                </Flex>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16} style={{ maxWidth: 720 }}>
                <Card title={t('user_import.instructions')}>
                    <List
                        size="small"
                        dataSource={IMPORT_NOTE_KEYS}
                        renderItem={(key) => (
                            <List.Item>
                                <Text>{t(`user_import.${key}`)}</Text>
                            </List.Item>
                        )}
                    />
                </Card>

                <Card title={t('user_import.excel_file')}>
                    {errors.excel_file && (
                        <Alert
                            type="error"
                            showIcon
                            message={errors.excel_file}
                            style={{ marginBottom: 16 }}
                        />
                    )}

                    <Dragger
                        accept=".xlsx,.xls"
                        maxCount={1}
                        showUploadList={false}
                        beforeUpload={(selected) => {
                            setFile(selected);
                            return false;
                        }}
                        disabled={uploading}
                    >
                        <p className="ant-upload-drag-icon">
                            {file ? <FileExcelOutlined /> : <InboxOutlined />}
                        </p>
                        <p className="ant-upload-text">
                            {file ? file.name : t('user_import.dropzone')}
                        </p>
                        <p className="ant-upload-hint">{t('user_import.allowed_formats')}</p>
                    </Dragger>

                    <Button
                        type="primary"
                        icon={<UploadOutlined />}
                        loading={uploading}
                        disabled={!file}
                        onClick={handleImport}
                        style={{ marginTop: 16 }}
                    >
                        {t('user_import.submit')}
                    </Button>
                </Card>
            </Flex>
        </PageContainer>
    );
}
