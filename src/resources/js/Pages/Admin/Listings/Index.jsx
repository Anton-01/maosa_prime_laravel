import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button, Dropdown, Flex, Image, Input, Modal, Table, Tag, Typography } from 'antd';
import {
    CheckCircleOutlined,
    ClockCircleOutlined,
    CoffeeOutlined,
    DeleteOutlined,
    EditOutlined,
    ExclamationCircleOutlined,
    MinusCircleOutlined,
    MoreOutlined,
    PlusOutlined,
    SearchOutlined,
} from '@ant-design/icons';
import PageContainer from '../../../Components/PageContainer';
import useTranslation from '../../../Hooks/useTranslation';

const { Text } = Typography;

function YesNoTag({ value, t }) {
    return value === 1 ? <Tag color="blue">{t('common.yes')}</Tag> : <Tag>{t('common.no')}</Tag>;
}

export default function Index({ listings, urls }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [deleting, setDeleting] = useState(false);
    const [modal, contextHolder] = Modal.useModal();

    const categoryFilters = useMemo(
        () =>
            [...new Set(listings.map((listing) => listing.category))].map((name) => ({
                text: name,
                value: name,
            })),
        [listings],
    );

    const locationFilters = useMemo(
        () =>
            [...new Set(listings.map((listing) => listing.location))].map((name) => ({
                text: name,
                value: name,
            })),
        [listings],
    );

    const filteredListings = useMemo(() => {
        if (!search) return listings;

        const term = search.toLowerCase();

        return listings.filter((listing) => listing.title.toLowerCase().includes(term));
    }, [listings, search]);

    const handleDelete = (listing) => {
        modal.confirm({
            title: t('listings.delete_title'),
            icon: <ExclamationCircleOutlined />,
            content: t('listings.delete_confirm', { title: listing.title }),
            okText: t('common.delete'),
            cancelText: t('common.cancel'),
            okButtonProps: { danger: true },
            onOk: () =>
                router.delete(`${urls.base}/${listing.id}`, {
                    preserveScroll: true,
                    onStart: () => setDeleting(true),
                    onFinish: () => setDeleting(false),
                }),
        });
    };

    const columns = [
        {
            title: t('listings.image'),
            dataIndex: 'imageUrl',
            key: 'image',
            width: 84,
            render: (url) =>
                url ? (
                    <Image
                        src={url}
                        alt={t('listings.image')}
                        width={56}
                        height={56}
                        style={{ objectFit: 'cover', borderRadius: 8 }}
                        preview={{ mask: t('listings.view') }}
                    />
                ) : (
                    '—'
                ),
        },
        {
            title: t('users.name'),
            dataIndex: 'title',
            key: 'title',
            sorter: (a, b) => a.title.localeCompare(b.title),
            render: (title, record) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{title}</div>
                    <Text style={{ fontSize: 12, color: 'rgba(0,0,0,0.45)' }}>
                        {record.status === 1 ? (
                            <>
                                <CheckCircleOutlined style={{ color: '#52c41a', marginRight: 4 }} />
                                {t('common.active')}
                            </>
                        ) : (
                            <>
                                <MinusCircleOutlined style={{ color: '#bfbfbf', marginRight: 4 }} />
                                {t('common.inactive')}
                            </>
                        )}
                    </Text>
                </div>
            ),
        },
        {
            title: t('listings.category'),
            dataIndex: 'category',
            key: 'category',
            width: 150,
            filters: categoryFilters,
            onFilter: (value, record) => record.category === value,
            render: (value) => <Tag color="blue">{value}</Tag>,
        },
        {
            title: t('listings.location'),
            dataIndex: 'location',
            key: 'location',
            width: 150,
            filters: locationFilters,
            onFilter: (value, record) => record.location === value,
        },
        {
            title: t('listings.featured'),
            dataIndex: 'isFeatured',
            key: 'isFeatured',
            width: 110,
            filters: [
                { text: t('common.yes'), value: 1 },
                { text: t('common.no'), value: 0 },
            ],
            onFilter: (value, record) => record.isFeatured === value,
            render: (value) => <YesNoTag value={value} t={t} />,
        },
        {
            title: t('listings.verified'),
            dataIndex: 'isVerified',
            key: 'isVerified',
            width: 110,
            filters: [
                { text: t('common.yes'), value: 1 },
                { text: t('common.no'), value: 0 },
            ],
            onFilter: (value, record) => record.isVerified === value,
            render: (value) => <YesNoTag value={value} t={t} />,
        },
        {
            title: t('common.actions'),
            key: 'actions',
            width: 100,
            render: (_, record) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'edit',
                                icon: <EditOutlined />,
                                label: (
                                    <Link href={`${urls.base}/${record.id}/edit`}>
                                        {t('common.edit')}
                                    </Link>
                                ),
                            },
                            {
                                key: 'schedules',
                                icon: <ClockCircleOutlined />,
                                label: (
                                    <Link href={`${urls.schedulesBase}/${record.id}`}>
                                        {t('listings.schedules')}
                                    </Link>
                                ),
                            },
                            {
                                key: 'amenities',
                                icon: <CoffeeOutlined />,
                                label: (
                                    <Link href={`${urls.base}/${record.id}/amenities`}>
                                        {t('listings.amenities')}
                                    </Link>
                                ),
                            },
                            { type: 'divider' },
                            {
                                key: 'delete',
                                icon: <DeleteOutlined />,
                                label: t('common.delete'),
                                danger: true,
                                onClick: () => handleDelete(record),
                            },
                        ],
                    }}
                >
                    <Button size="small" icon={<MoreOutlined />}>
                        {t('common.actions')}
                    </Button>
                </Dropdown>
            ),
        },
    ];

    return (
        <PageContainer
            title={t('listings.title')}
            breadcrumbItems={[{ title: t('nav.suppliers') }, { title: t('listings.title') }]}
            extra={
                <Link href={urls.create}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        {t('listings.new')}
                    </Button>
                </Link>
            }
        >
            {contextHolder}

            <Flex style={{ marginBottom: 16 }}>
                <Input
                    allowClear
                    prefix={<SearchOutlined />}
                    placeholder={t('common.search_by_name')}
                    style={{ maxWidth: 320 }}
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
            </Flex>

            <Table
                rowKey="id"
                columns={columns}
                dataSource={filteredListings}
                loading={deleting}
                scroll={{ x: 900 }}
                pagination={{
                    position: ['bottomRight'],
                    pageSize: 10,
                    showSizeChanger: true,
                    showTotal: (total) => t('listings.total', { count: total }),
                }}
                locale={{ emptyText: t('listings.empty') }}
            />
        </PageContainer>
    );
}
