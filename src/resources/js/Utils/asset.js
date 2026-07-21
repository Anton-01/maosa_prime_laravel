/**
 * Build a public asset URL from a stored (relative) path.
 * Mirrors Laravel's asset() helper for values persisted by the upload trait
 * (e.g. "uploads/media_xxx.jpg"). Absolute URLs and data URIs pass through.
 */
export default function asset(path) {
    if (!path) return '';
    if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:')) {
        return path;
    }
    return '/' + String(path).replace(/^\/+/, '');
}
