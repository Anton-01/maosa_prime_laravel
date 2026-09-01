import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Head } from '@inertiajs/react';
import {
    Alert,
    App,
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
    FileExcelOutlined,
    FileImageOutlined,
    FilePdfOutlined,
    ReloadOutlined,
} from '@ant-design/icons';
import dayjs from 'dayjs';

import useTranslation from '../../Hooks/useTranslation';

const { Title, Text } = Typography;

const DATE_FORMAT = 'YYYY-MM-DD';

/**
 * Inyecta el fragmento HTML de la API y garantiza que la tabla quede dentro de
 * un contenedor con scroll horizontal: la API no siempre envuelve la tabla en
 * `.table-responsive` y `.precio-layout` recorta lo que se desborda.
 */
function LayoutFragment({ html }) {
    const containerRef = useRef(null);

    useEffect(() => {
        const container = containerRef.current;
        if (!container || !html) return;

        container.querySelectorAll('.precio-layout table').forEach((table) => {
            const parent = table.parentElement;
            if (!parent || parent.classList.contains('table-responsive') || parent.classList.contains('pl-table-scroll')) {
                return;
            }

            const scroller = document.createElement('div');
            scroller.className = 'pl-table-scroll';
            parent.insertBefore(scroller, table);
            scroller.appendChild(table);
        });
    }, [html]);

    return <div ref={containerRef} dangerouslySetInnerHTML={{ __html: html ?? '' }} />;
}

/** Nombre del archivo que anuncia el backend en el `Content-Disposition`. */
function filenameFromResponse(response, fallback) {
    const disposition = response.headers.get('Content-Disposition') || '';
    const match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);

    return match ? decodeURIComponent(match[1].trim()) : fallback;
}

/**
 * Submódulo "Precios PEMEX" (REQ-04 a REQ-07).
 *
 * - Las estaciones son las asignadas al usuario en `usuario_estacion` y se
 *   eligen en un multiselect (REQ-06).
 * - La fecha de vigencia sólo admite ayer, hoy o mañana, igual que en Precios
 *   Internacionales; el backend valida exactamente la misma regla.
 * - Sea una o sean N estaciones, el navegador manda **una sola** petición por
 *   acción: el backend resuelve el ciclo contra la API y devuelve todo junto
 *   (los layouts HTML en un JSON, y las descargas como archivo único o como
 *   un .zip con un archivo por estación).
 */
export default function PreciosPemex({
    stations = [],
    endpoints,
    dates,
    maxStations = 50,
    stylesheet,
}) {
    const { message } = App.useApp();
    const { t } = useTranslation();

    const [selectedIds, setSelectedIds] = useState(() =>
        stations.length ? [stations[0].id_estacion] : [],
    );
    const [date, setDate] = useState(dayjs(dates?.today));
    const [layouts, setLayouts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [failed, setFailed] = useState(false);
    const [downloading, setDownloading] = useState(null);

    const stationOptions = useMemo(
        () =>
            stations.map((station) => ({
                value: station.id_estacion,
                label: station.estacion,
            })),
        [stations],
    );

    // El multiselect ya impide repetidos, pero la petición se arma siempre a
    // partir de un Set: nunca sale un id de estación duplicado hacia el back.
    const uniqueIds = useMemo(() => [...new Set(selectedIds)], [selectedIds]);
    const dateString = date ? date.format(DATE_FORMAT) : dates?.today;

    const buildQuery = useCallback(
        (ids, fecha) => {
            const params = new URLSearchParams();
            ids.forEach((id) => params.append('estaciones[]', id));
            params.append('fecha_vigencia', fecha);

            return params.toString();
        },
        [],
    );

    const stationName = useCallback(
        (id) =>
            stations.find((station) => station.id_estacion === id)?.estacion ??
            `${t('prices.station')} ${id}`,
        [stations, t],
    );

    // Una sola petición para todas las estaciones seleccionadas: el backend
    // devuelve el arreglo de fragmentos ya resuelto.
    const loadLayouts = useCallback(
        async (ids, fecha, signal) => {
            if (!ids.length || !fecha) {
                setLayouts([]);
                setFailed(false);
                return;
            }

            setLoading(true);
            setFailed(false);

            try {
                const response = await fetch(`${endpoints.html}?${buildQuery(ids, fecha)}`, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    signal,
                });

                if (!response.ok) throw new Error(`layouts ${response.status}`);

                const data = await response.json();

                setLayouts(data.layouts ?? []);
            } catch (error) {
                if (error.name === 'AbortError') return;
                setLayouts([]);
                setFailed(true);
            } finally {
                if (!signal?.aborted) setLoading(false);
            }
        },
        [buildQuery, endpoints.html],
    );

    useEffect(() => {
        const controller = new AbortController();
        loadLayouts(uniqueIds, dateString, controller.signal);

        return () => controller.abort();
    }, [uniqueIds, dateString, loadLayouts]);

    // Descarga forzada: el archivo (o el .zip con todas las estaciones) llega
    // como blob y se ancla a un <a download> temporal, así el navegador no
    // intenta abrirlo en una pestaña.
    const download = async (format, endpoint, errorKey) => {
        if (!uniqueIds.length || downloading) return;

        setDownloading(format);

        try {
            const response = await fetch(`${endpoint}?${buildQuery(uniqueIds, dateString)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error(`download ${response.status}`);

            const failedStations = response.headers.get('X-Estaciones-Fallidas');
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = filenameFromResponse(
                response,
                `precios-pemex-${format}-${dateString}`,
            );
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);

            if (failedStations) {
                message.warning(
                    t('prices.partial_download', {
                        stations: failedStations
                            .split(',')
                            .map((id) => stationName(Number(id)))
                            .join(', '),
                    }),
                );
            }
        } catch (error) {
            message.error(t(errorKey));
        } finally {
            setDownloading(null);
        }
    };

    const failedLayouts = layouts.filter((layout) => !layout.ok);
    const disabledActions = !uniqueIds.length || downloading !== null;

    return (
        <>
            <Head title={t('prices.pemex_title')}>
                {/* Hoja de estilos del layout que devuelve la API (REQ-05). */}
                <link rel="stylesheet" href={stylesheet} />
            </Head>

            <Card style={{ marginBottom: 24 }}>
                <Title level={4} style={{ marginBottom: 4 }}>
                    {t('prices.pemex_title')}
                </Title>
                <Text type="secondary">{t('prices.pemex_subtitle')}</Text>

                <Row gutter={[16, 16]} align="bottom" style={{ marginTop: 20 }}>
                    <Col xs={24} md={14} xl={11}>
                        <div style={{ marginBottom: 6, fontWeight: 600 }}>
                            {t('prices.stations')}
                        </div>
                        <Select
                            mode="multiple"
                            style={{ width: '100%' }}
                            size="large"
                            allowClear
                            showSearch
                            optionFilterProp="label"
                            maxTagCount="responsive"
                            maxCount={maxStations}
                            value={selectedIds}
                            onChange={setSelectedIds}
                            options={stationOptions}
                            disabled={!stations.length}
                            placeholder={t('prices.stations_placeholder')}
                        />
                        <Text type="secondary" style={{ fontSize: 12 }}>
                            {stations.length > maxStations
                                ? t('prices.stations_limit', { max: maxStations })
                                : t('prices.stations_hint')}
                        </Text>
                    </Col>

                    <Col xs={24} md={10} xl={6}>
                        <div style={{ marginBottom: 6, fontWeight: 600 }}>
                            {t('prices.effective_date')}
                        </div>
                        <DatePicker
                            style={{ width: '100%' }}
                            size="large"
                            value={date}
                            allowClear={false}
                            format="DD/MM/YYYY"
                            inputReadOnly
                            disabledDate={(current) =>
                                current &&
                                (current < dayjs(dates.min).startOf('day') ||
                                    current > dayjs(dates.max).endOf('day'))
                            }
                            onChange={(value) => value && setDate(value)}
                        />
                        <Text type="secondary" style={{ fontSize: 12 }}>
                            {t('prices.effective_date_hint')}
                        </Text>
                    </Col>

                    {/* REQ-07: una sola petición por formato, sin importar
                        cuántas estaciones estén seleccionadas. */}
                    <Col xs={24} xl={7}>
                        <Row gutter={[8, 8]}>
                            <Col xs={12} sm={6} xl={12}>
                                <Button
                                    block
                                    icon={<ReloadOutlined />}
                                    onClick={() => loadLayouts(uniqueIds, dateString)}
                                    disabled={!uniqueIds.length || loading}
                                >
                                    {t('common.refresh')}
                                </Button>
                            </Col>
                            <Col xs={12} sm={6} xl={12}>
                                <Button
                                    block
                                    icon={<FileExcelOutlined />}
                                    loading={downloading === 'Excel'}
                                    disabled={disabledActions}
                                    onClick={() =>
                                        download('Excel', endpoints.excel, 'prices.excel_error')
                                    }
                                >
                                    {t('prices.excel')}
                                </Button>
                            </Col>
                            <Col xs={12} sm={6} xl={12}>
                                <Button
                                    block
                                    danger
                                    icon={<FilePdfOutlined />}
                                    loading={downloading === 'pdf'}
                                    disabled={disabledActions}
                                    onClick={() => download('pdf', endpoints.pdf, 'prices.pdf_error')}
                                >
                                    {t('prices.pdf')}
                                </Button>
                            </Col>
                            <Col xs={12} sm={6} xl={12}>
                                <Button
                                    block
                                    icon={<FileImageOutlined />}
                                    loading={downloading === 'imagen'}
                                    disabled={disabledActions}
                                    onClick={() =>
                                        download('imagen', endpoints.imagen, 'prices.image_error')
                                    }
                                >
                                    {t('prices.image')}
                                </Button>
                            </Col>
                        </Row>
                    </Col>
                </Row>

                <Space wrap size={[8, 8]} style={{ marginTop: 16 }}>
                    <Tag icon={<CalendarOutlined />}>
                        {t('prices.validity', { date: date ? date.format('DD/MM/YYYY') : '' })}
                    </Tag>
                    <Tag color="blue">
                        {t('prices.selected_stations', { count: uniqueIds.length })}
                    </Tag>
                </Space>
            </Card>

            {failed && (
                <Alert
                    type="error"
                    showIcon
                    style={{ marginBottom: 24 }}
                    message={t('prices.load_error_title')}
                    description={t('prices.load_error_description')}
                />
            )}

            {!failed && failedLayouts.length > 0 && (
                <Alert
                    type="warning"
                    showIcon
                    style={{ marginBottom: 24 }}
                    message={t('prices.partial_load', {
                        stations: failedLayouts.map((layout) => layout.estacion).join(', '),
                    })}
                />
            )}

            {!stations.length ? (
                <Card>
                    <Empty description={t('prices.no_stations')} style={{ padding: 40 }} />
                </Card>
            ) : (
                <Spin spinning={loading}>
                    <Space direction="vertical" size="middle" style={{ display: 'flex' }}>
                        {uniqueIds.length === 0 && (
                            <Card>
                                <Empty description={t('prices.select_station')} style={{ padding: 40 }} />
                            </Card>
                        )}

                        {layouts.map((layout) => (
                            <Card
                                key={layout.id_estacion}
                                title={uniqueIds.length > 1 ? layout.estacion : undefined}
                                styles={{ body: { padding: 0 } }}
                                style={{ overflow: 'hidden' }}
                            >
                                {/* El fragmento trae su propio layout (.precio-layout). */}
                                <LayoutFragment html={layout.html} />
                            </Card>
                        ))}
                    </Space>
                </Spin>
            )}
        </>
    );
}
