export function hasPermission(user, permission) {
    if (!permission) return true;

    if (!user) return false;

    // Super Admin sees everything
    if (user.user_type === "super_admin") {
        return true;
    }

    const permissions = user.permissions ?? [];

    if (Array.isArray(permission)) {
        return permission.some(perm => permissions.some(p => p.name === perm));
    }

    if (typeof permission === 'string' && permission.includes(',')) {
        return permission.split(',').map(s => s.trim()).some(perm => permissions.some(p => p.name === perm));
    }

    return permissions.some((p) => p.name === permission);
}