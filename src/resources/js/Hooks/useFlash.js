import { useEffect } from 'react';
import { App } from 'antd';
import { usePage } from '@inertiajs/react';

const ALERT_TYPE_MAP = {
    success: 'success',
    danger: 'error',
    error: 'error',
    warning: 'warning',
    info: 'info',
};

/**
 * Surface Laravel session flash messages as Ant Design toasts.
 * Handles the simple keys (success/error/warning/info) and the richer
 * `status` payload used across the frontend ({ main_message, description,
 * alert_type }) as well as a plain string status (Breeze).
 */
export default function useFlash() {
    const { flash } = usePage().props;
    const { message, notification } = App.useApp();

    useEffect(() => {
        if (!flash) return;

        if (flash.success) message.success(flash.success);
        if (flash.error) message.error(flash.error);
        if (flash.warning) message.warning(flash.warning);
        if (flash.info) message.info(flash.info);

        const status = flash.status;
        if (status) {
            if (typeof status === 'string') {
                message.success(status);
            } else if (typeof status === 'object') {
                const type = ALERT_TYPE_MAP[status.alert_type] ?? 'info';
                notification[type]({
                    message: status.main_message ?? 'Aviso',
                    description: status.description ?? null,
                    placement: 'topRight',
                });
            }
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [flash]);
}
