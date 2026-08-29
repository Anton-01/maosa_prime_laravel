/**
 * Copia texto al portapapeles. Usa la Clipboard API cuando está disponible
 * (requiere contexto seguro: https o localhost) y cae a un textarea oculto
 * con execCommand en el resto de los casos.
 *
 * @returns {Promise<boolean>} true si el navegador confirmó la copia.
 */
export default async function copyToClipboard(text) {
    if (!text) return false;

    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch (e) {
        // El navegador negó el permiso: se intenta el método clásico.
    }

    try {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        const copied = document.execCommand('copy');
        textarea.remove();

        return copied;
    } catch (e) {
        return false;
    }
}
