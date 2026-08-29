import React from 'react';
import { Link } from '@inertiajs/react';
import { Alert, Button, Card, Flex, Typography, theme } from 'antd';
import { ArrowLeftOutlined, DownloadOutlined, RedoOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Paragraph } = Typography;

export default function ImportResult({ content, filename, urls }) {
    const { t } = useTranslation();
    const { token } = theme.useToken();

    return (
        <PageContainer
            title={t('user_import.result_title')}
            breadcrumbItems={[
                { title: t('users.breadcrumb_access') },
                { title: t('users.breadcrumb_users'), href: urls.users },
                { title: t('user_import.result_breadcrumb') },
            ]}
            extra={
                <Flex gap="small" wrap>
                    <Link href={urls.import}>
                        <Button icon={<RedoOutlined />}>{t('user_import.new_import')}</Button>
                    </Link>
                    <Link href={urls.users}>
                        <Button icon={<ArrowLeftOutlined />}>{t('user_import.back_to_users')}</Button>
                    </Link>
                    <Button type="primary" icon={<DownloadOutlined />} href={urls.download}>
                        {t('user_import.download_report', { filename })}
                    </Button>
                </Flex>
            }
            wrapInCard={false}
        >
            <Flex vertical gap={16}>
                <Alert
                    type="warning"
                    showIcon
                    message={t('user_import.warning_title')}
                    description={t('user_import.warning_description')}
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
