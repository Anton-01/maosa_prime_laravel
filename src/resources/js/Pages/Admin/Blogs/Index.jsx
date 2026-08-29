import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Flex, Image, Input, Popconfirm, Table, Tag, Tooltip } from 'antd';
import { DeleteOutlined, EditOutlined, PlusOutlined, SearchOutlined } from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

export default function Index({ blogs, urls }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [deleting, setDeleting] = useState(false);

    const filteredBlogs = useMemo(() => {
        if (!search) return blogs;

        const term = search.toLowerCase();

        return blogs.filter((blog) => blog.title.toLowerCase().includes(term));
    }, [blogs, search]);

    const handleDelete = (blog) => {
        router.delete(`${urls.base}/${blog.id}`, {
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
        });
    };

    const columns = [
        {
            title: t('blogs.image'),
            dataIndex: 'imageUrl',
            key: 'image',
            width: 90,
            render: (url) =>
                url ? (
                    <Image src={url} alt={t('blogs.image')} width={60} style={{ borderRadius: 6 }} />
                ) : (
                    '—'
                ),
        },
        {
            title: t('common.title'),
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
            ellipsis: true,
        },
        { title: t('blogs.author'), dataIndex: 'author', key: 'author', width: 150, ellipsis: true },
        {
            title: t('common.status'),
            dataIndex: 'status',
            key: 'status',
            width: 110,
            filters: [
                { text: t('common.active'), value: 1 },
                { text: t('common.inactive'), value: 0 },
            ],
            onFilter: (value, record) => record.status === value,
            render: (status) =>
                status === 1 ? (
                    <Tag color="success">{t('common.active')}</Tag>
                ) : (
                    <Tag color="error">{t('common.inactive')}</Tag>
                ),
        },
        {
            title: t('blogs.created_at'),
            dataIndex: 'createdAt',
            key: 'createdAt',
            width: 110,
        },
        {
            title: t('common.actions'),
            key: 'actions',
            width: 120,
            render: (_, record) => (
                <Flex gap="small">
                    <Tooltip title={t('common.edit')}>
                        <Link href={`${urls.base}/${record.id}/edit`}>
                            <Button type="primary" size="small" icon={<EditOutlined />} />
                        </Link>
                    </Tooltip>
                    <Popconfirm
                        title={t('blogs.delete_title')}
                        description={t('blogs.delete_confirm')}
                        okText={t('common.delete')}
                        cancelText={t('common.cancel')}
                        okButtonProps={{ danger: true }}
                        onConfirm={() => handleDelete(record)}
                    >
                        <Button danger size="small" icon={<DeleteOutlined />} />
                    </Popconfirm>
                </Flex>
            ),
        },
    ];

    return (
        <PageContainer
            title={t('blogs.title')}
            breadcrumbItems={[{ title: t('common.control_panel') }, { title: t('blogs.title') }]}
            extra={
                <Link href={urls.create}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        {t('blogs.new')}
                    </Button>
                </Link>
            }
        >
            <Flex style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('blogs.search_by_title')}
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredBlogs}
                loading={deleting}
                scroll={{ x: 800 }}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => t('blogs.total', { count: total }),
                }}
                locale={{ emptyText: t('blogs.empty') }}
            />
        </PageContainer>
    );
}
