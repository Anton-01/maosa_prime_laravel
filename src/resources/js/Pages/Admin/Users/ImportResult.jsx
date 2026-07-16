import React from 'react';
import { Link } from '@inertiajs/react';
import { Alert, Button, Card, Flex, Typography, theme } from 'antd';
import { ArrowLeftOutlined, DownloadOutlined, RedoOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';

const { Paragraph } = Typography;

export default function ImportResult({ content, filename, urls }) {
    const { token } = theme.useToken();

    return (
        <PageContainer
            title="Resultado de la importación"
            breadcrumbItems={[
                { title: 'Gestión de accesos' },
                { title: 'Usuarios', href: urls.users },
                { title: 'Resultado de importación' },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.import}>
                        <Button icon={<RedoOutlined />}>Nueva importación</Button>
                    </Link>
                    <Link href={urls.users}>
                        <Button icon={<ArrowLeftOutlined />}>Volver a usuarios</Button>
                    </Link>
                    <Button type="primary" icon={<DownloadOutlined />} href={urls.download}>
                        Descargar reporte ({filename})
                    </Button>
                </Flex>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Alert
                    type="warning"
                    showIcon
                    message="Descarga el reporte antes de salir"
                    description="El reporte incluye las contraseñas generadas y solo está disponible en esta sesión; al descargarlo se elimina del servidor."
                />

                <Card>
                    <Paragraph>
                        <pre
                            style={{
                                margin: 0,
                                maxHeight: 480,
                                overflow: 'auto',
                                background: token.colorFillTertiary,
                                padding: 16,
                                borderRadius: token.borderRadius,
                            }}
                        >
                            {content}
                        </pre>
                    </Paragraph>
                </Card>
            </Flex>
        </PageContainer>
    );
}
