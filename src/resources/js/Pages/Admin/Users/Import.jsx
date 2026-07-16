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

const { Text } = Typography;
const { Dragger } = Upload;

const IMPORT_NOTES = [
    'Las columnas NOMBRE y EMAIL son obligatorias (el email debe ser único).',
    'Los usuarios importados tendrán rol "User" y estado aprobado.',
    'La contraseña se genera automáticamente (12 caracteres seguros).',
    'Al finalizar podrás descargar un TXT con el resultado y las contraseñas asignadas.',
];

export default function Import({ urls }) {
    const { errors } = usePage().props;
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
            title="Importar usuarios"
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Usuarios', href: urls.users },
                { title: 'Importar' },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.users}>
                        <Button icon={<ArrowLeftOutlined />}>Volver a usuarios</Button>
                    </Link>
                    <Button icon={<DownloadOutlined />} href={urls.layout}>
                        Descargar layout
                    </Button>
                </Flex>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16} style={{ maxWidth: 720 }}>
                <Card title="Instrucciones">
                    <List
                        size="small"
                        dataSource={IMPORT_NOTES}
                        renderItem={(note) => (
                            <List.Item>
                                <Text>{note}</Text>
                            </List.Item>
                        )}
                    />
                </Card>

                <Card title="Archivo Excel">
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
                            {file ? file.name : 'Haz clic o arrastra el archivo Excel aquí'}
                        </p>
                        <p className="ant-upload-hint">Formatos permitidos: .xlsx, .xls</p>
                    </Dragger>

                    <Button
                        type="primary"
                        icon={<UploadOutlined />}
                        loading={uploading}
                        disabled={!file}
                        onClick={handleImport}
                        style={{ marginTop: 16 }}
                    >
                        Importar usuarios
                    </Button>
                </Card>
            </Flex>
        </PageContainer>
    );
}
