export type FoyerRole = 'owner' | 'admin' | 'member';

export type Foyer = {
    id: number;
    name: string;
    slug: string;
    isPersonal: boolean;
    role?: FoyerRole;
    roleLabel?: string;
    isCurrent?: boolean;
};

export type FoyerMember = {
    id: number;
    name: string;
    email: string;
    avatar?: string | null;
    role: FoyerRole;
    role_label: string;
};

export type FoyerInvitation = {
    code: string;
    email: string;
    role: FoyerRole;
    role_label: string;
    created_at: string;
};

export type FoyerInvitationContext = {
    code: string;
    foyerName: string;
};

export type DashboardInvitation = {
    code: string;
    inviterName: string;
    foyer: {
        name: string;
        slug: string;
    };
};

export type FoyerPermissions = {
    canUpdateFoyer: boolean;
    canDeleteFoyer: boolean;
    canAddMember: boolean;
    canUpdateMember: boolean;
    canRemoveMember: boolean;
    canCreateInvitation: boolean;
    canCancelInvitation: boolean;
};

export type RoleOption = {
    value: FoyerRole;
    label: string;
};
