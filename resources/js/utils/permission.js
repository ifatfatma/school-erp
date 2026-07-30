export function hasPermission(user, permission) {
    if (!permission) return true;

    if (!user) return false;

    // Super Admin sees everything
    if (user.user_type === "super_admin") {
        return true;
    }

    const permissions = user.permissions ?? [];

    return permissions.some((p) => p.name === permission);
}