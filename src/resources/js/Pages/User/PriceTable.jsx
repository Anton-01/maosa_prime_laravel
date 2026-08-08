import React, { useCallback, useEffect, useRef, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import {
    Alert,
    App,
    Avatar,
    Button,
    Card,
    Col,
    DatePicker,
    Empty,
    Row,
    Select,
    Space,
    Spin,
    Tag,
    Typography,
} from 'antd';
import {
    CalendarOutlined,
    DollarOutlined,
    EnvironmentOutlined,
    FilePdfOutlined,
    MailOutlined,
    UserOutlined,
} from '@ant-design/icons';
import dayjs from 'dayjs';

import asset from '../../Utils/asset';

const { Title, Text } = Typography;

const DATE_FORMAT = 'YYYY-MM-DD';

export default function PriceTable({ endpoints, dates }) {
    const { message } = App.useApp();
    const { auth } = usePage().props;
    const user = auth?.user;

    const [stations, setStations] = useState([]);
    const [loadingStations, setLoadingStations] = useState(true);
    const [stationId, setStationId] = useState(null);
    const [date, setDate] = useState(dayjs(dates.today));
    const [priceHtml, setPriceHtml] = useState('');
    const [loadingPrices, setLoadingPrices] = useState(false);
    const [downloading, setDownloading] = useState(false);
    const [needsScroll, setNeedsScroll] = useState(false);

    const scrollRef = useRef(null);

    // Load the user's assigned stations once.
    useEffect(() => {
        let active = true;
        (async () => {
            try {
                const res = await fetch(endpoints.stations, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('stations');
                const data = await res.json();
                if (!active) return;
                setStations(data || []);
                if (data && data.length) setStationId(data[0].id_estacion);
            } catch (e) {
                if (active) message.error('Error al obtener la configuración de precios.');
            } finally {
                if (active) setLoadingStations(false);
            }
        })();
        return () => {
            active = false;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // (Re)load the price fragment whenever the station or date changes.
    useEffect(() => {
        if (!stationId) return;
        let active = true;
        (async () => {
            setLoadingPrices(true);
            try {
                const params = new URLSearchParams({ estacion_id: stationId });
                if (date) params.append('fecha_vigencia', date.format(DATE_FORMAT));
                const res = await fetch(`${endpoints.html}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const text = await res.text();
                if (active) setPriceHtml(text);
            } catch (e) {
                if (active) setPriceHtml('<p style="text-align:center;padding:24px">Error de conexión.</p>');
            } finally {
                if (active) setLoadingPrices(false);
            }
        })();
        return () => {
            active = false;
        };
    }, [stationId, date, endpoints.html]);

    // The price fragment comes from an external API and its column count depends
    // on each user's configuration, so the horizontal overflow is measured after
    // every render instead of being assumed from a fixed breakpoint.
    const measureOverflow = useCallback(() => {
        const el = scrollRef.current;
        if (!el) return;
        setNeedsScroll(el.scrollWidth - el.clientWidth > 4);
    }, []);

    useEffect(() => {
        const el = scrollRef.current;
        if (!el) return undefined;

        measureOverflow();

        const observer = new ResizeObserver(measureOverflow);
        observer.observe(el);
        // Track the injected fragment too: images (logo/watermark) settle late.
        if (el.firstElementChild) observer.observe(el.firstElementChild);

        window.addEventListener('resize', measureOverflow);
        return () => {
            observer.disconnect();
            window.removeEventListener('resize', measureOverflow);
        };
    }, [priceHtml, measureOverflow]);

    const downloadPdf = async () => {
        if (downloading) return;
        setDownloading(true);
        try {
            const params = new URLSearchParams();
            if (date) params.append('fecha_vigencia', date.format(DATE_FORMAT));
            const res = await fetch(`${endpoints.pdf}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error('pdf');
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `maosa-prime-precios-combustible-${date.format(DATE_FORMAT)}.pdf`;
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        } catch (e) {
            message.error('No fue posible descargar el PDF. Intente más tarde.');
        } finally {
            setDownloading(false);
        }
    };

    const currentStation = stations.find((s) => s.id_estacion === stationId);
    const currentStationName =
        currentStation?.estacion || currentStation?.id_socio || null;

    return (
        <>
            <Head title="Tabla de precios">
                <link rel="stylesheet" href="/frontend/css/maosa/table-prices.css" />
            </Head>

            {/* Welcome card: confirms these prices belong to the signed-in account. */}
            <Card styles={{ body: { padding: 0 } }} style={{ marginBottom: 24, overflow: 'hidden' }}>
                <div
                    style={{
                        height: 110,
                        // Same banner (and same fallback) as the profile card.
                        background: user?.banner
                            ? `url(${asset(user.banner)}) center/cover`
                            : 'linear-gradient(135deg, var(--brand-color, #6777ef), #0f1729)',
                    }}
                />
                <div style={{ padding: '0 24px 24px', display: 'flex', gap: 18, alignItems: 'flex-end' }}>
                    <Avatar
                        size={80}
                        src={user?.avatar ? asset(user.avatar) : undefined}
                        icon={<UserOutlined />}
                        style={{ marginTop: -40, border: '4px solid #fff', background: '#eee' }}
                    />
                    <div style={{ paddingTop: 12 }}>
                        <Title level={4} style={{ margin: 0 }}>
                            Tabla de precios de {user?.name}
                        </Title>
                        <Text type="secondary">
                            <MailOutlined /> {user?.email}
                        </Text>
                        <div style={{ marginTop: 8 }}>
                            <Space wrap>
                                <Tag color="orange" icon={<DollarOutlined />}>
                                    Precios personalizados
                                </Tag>
                                {currentStationName && (
                                    <Tag color="blue" icon={<EnvironmentOutlined />}>
                                        {currentStationName}
                                    </Tag>
                                )}
                                <Tag icon={<CalendarOutlined />}>
                                    Vigencia {date.format('DD/MM/YYYY')}
                                </Tag>
                            </Space>
                        </div>
                    </div>
                </div>
            </Card>

            <Card style={{ marginBottom: 24 }}>
                <Row gutter={[16, 16]} align="bottom">
                    {stations.length > 1 && (
                        <Col xs={24} md={10}>
                            <div style={{ marginBottom: 6, fontWeight: 600 }}>Estación</div>
                            <Select
                                style={{ width: '100%' }}
                                size="large"
                                loading={loadingStations}
                                value={stationId}
                                onChange={setStationId}
                                options={stations.map((s) => ({
                                    value: s.id_estacion,
                                    label: s.estacion || s.id_socio || `Estación ${s.id_estacion}`,
                                }))}
                            />
                        </Col>
                    )}
                    <Col xs={24} md={8}>
                        <div style={{ marginBottom: 6, fontWeight: 600 }}>Fecha de vigencia</div>
                        <DatePicker
                            style={{ width: '100%' }}
                            size="large"
                            value={date}
                            allowClear={false}
                            format="DD/MM/YYYY"
                            disabledDate={(current) =>
                                current &&
                                (current < dayjs(dates.min).startOf('day') ||
                                    current > dayjs(dates.max).endOf('day'))
                            }
                            onChange={(value) => value && setDate(value)}
                        />
                    </Col>
                    <Col xs={24} md={6}>
                        <Button
                            danger
                            size="large"
                            block
                            icon={<FilePdfOutlined />}
                            loading={downloading}
                            disabled={!stationId}
                            onClick={downloadPdf}
                        >
                            Descargar PDF
                        </Button>
                    </Col>
                </Row>
            </Card>

            {/* The fragment renders its own full-bleed layout, so the card body
                keeps no horizontal padding of its own. */}
            <Card styles={{ body: { padding: 0 } }} style={{ overflow: 'hidden' }}>
                {loadingStations ? (
                    <div style={{ textAlign: 'center', padding: 48 }}>
                        <Spin tip="Cargando configuración de precios..." />
                    </div>
                ) : !stations.length ? (
                    <Empty description="No tiene estaciones asignadas." style={{ padding: 40 }} />
                ) : (
                    <Spin spinning={loadingPrices}>
                        {needsScroll && (
                            <Alert
                                banner
                                type="info"
                                showIcon
                                message="Debido a la configuración visual de su tabla de precios, es necesario desplazarse horizontalmente para ver el resto del contenido."
                                style={{ fontSize: 13 }}
                            />
                        )}
                        <div className="price-table-viewport">
                            <div className="price-table-scroll" ref={scrollRef}>
                                <div
                                    className="price-table-frame"
                                    dangerouslySetInnerHTML={{ __html: priceHtml }}
                                />
                            </div>
                        </div>
                    </Spin>
                )}
            </Card>
        </>
    );
}
