import { useCallback, useEffect, useRef, useState } from 'react';

/**
 * Reactive data source for Ant Design <Table /> backed by a Yajra
 * DataTables JSON endpoint.
 *
 * Request contract sent to the backend:
 *   page, per_page, sort_field, sort_order (asc|desc), search, filters[column]
 *
 * Expected JSON response shape:
 *   { data: [...], total: <int> }
 *
 * Usage:
 *   const { data, total, loading, tableParams, handleTableChange, setSearch, refresh } =
 *       useDataTable(listUrl);
 *
 *   <Table
 *       dataSource={data}
 *       loading={loading}
 *       onChange={handleTableChange}
 *       pagination={{
 *           position: ['bottomRight'],
 *           current: tableParams.page,
 *           pageSize: tableParams.perPage,
 *           total,
 *           showSizeChanger: true,
 *       }}
 *   />
 */
export default function useDataTable(url, { defaultPageSize = 10, extraParams = {} } = {}) {
    const [data, setData] = useState([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');
    const [tableParams, setTableParams] = useState({
        page: 1,
        perPage: defaultPageSize,
        sortField: null,
        sortOrder: null,
        filters: {},
    });

    const requestIdRef = useRef(0);

    // Serialized so the effect below re-fetches when the values change,
    // not when the caller passes a new (but equal) object literal.
    const extraParamsKey = JSON.stringify(extraParams);

    const fetchData = useCallback(async () => {
        const extra = JSON.parse(extraParamsKey);
        const requestId = ++requestIdRef.current;
        setLoading(true);

        const query = new URLSearchParams({
            page: String(tableParams.page),
            per_page: String(tableParams.perPage),
        });

        Object.entries(extra).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                query.set(key, String(value));
            }
        });

        if (search) query.set('search', search);
        if (tableParams.sortField) {
            query.set('sort_field', tableParams.sortField);
            query.set('sort_order', tableParams.sortOrder === 'descend' ? 'desc' : 'asc');
        }

        Object.entries(tableParams.filters).forEach(([column, value]) => {
            if (value === null || value === undefined || value === '') return;
            if (Array.isArray(value) && value.length === 0) return;

            query.set(`filters[${column}]`, Array.isArray(value) ? value.join(',') : String(value));
        });

        try {
            const response = await fetch(`${url}?${query.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const payload = await response.json();

            // Ignore responses that arrive out of order.
            if (requestId === requestIdRef.current) {
                setData(payload.data ?? []);
                setTotal(payload.total ?? 0);
            }
        } catch (error) {
            if (requestId === requestIdRef.current) {
                setData([]);
                setTotal(0);
            }
        } finally {
            if (requestId === requestIdRef.current) {
                setLoading(false);
            }
        }
    }, [url, search, tableParams, extraParamsKey]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    /** Plug directly into Ant Design's <Table onChange />. */
    const handleTableChange = useCallback((pagination, filters, sorter) => {
        setTableParams({
            page: pagination.current,
            perPage: pagination.pageSize,
            sortField: sorter.field ?? null,
            sortOrder: sorter.order ?? null,
            filters,
        });
    }, []);

    /** Debounced-friendly search setter: resets to the first page. */
    const applySearch = useCallback((value) => {
        setSearch(value);
        setTableParams((previous) => ({ ...previous, page: 1 }));
    }, []);

    /** Programmatic filter setter (for filters outside the table columns). */
    const setFilter = useCallback((column, value) => {
        setTableParams((previous) => ({
            ...previous,
            page: 1,
            filters: { ...previous.filters, [column]: value },
        }));
    }, []);

    return {
        data,
        total,
        loading,
        tableParams,
        handleTableChange,
        setSearch: applySearch,
        setFilter,
        refresh: fetchData,
    };
}
