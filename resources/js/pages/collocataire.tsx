import Heading from "@/components/heading";
import InviteMemberModal from "@/components/invite-member-modal";
import { Button } from "@/components/ui/button";
import { Head } from "@inertiajs/react";
import type {
    RoleOption,
    Foyer,
    FoyerInvitation,
    FoyerMember,
    FoyerPermissions,
} from "@/types";
import { useState } from "react";
import { Avatar } from "@radix-ui/react-avatar";
import { AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { useInitials } from "@/hooks/use-initials";

type Props = {
    foyer: Foyer;
    members: FoyerMember[];
    invitations: FoyerInvitation[];
    permissions: FoyerPermissions;
    availableRoles: RoleOption[];
};

export default function Collocataire({
    foyer,
    members,
    invitations,
    permissions,
    availableRoles,
}: Props) {
    const [inviteDialogOpen, setInviteDialogOpen] = useState(false);

    console.log('permissions:', permissions);
    console.log('canCreateInvitation:', permissions?.canCreateInvitation);
    const getInitials = useInitials();

    return (
        <>
            <Head title="Collocataires" />
            <div className=" gap-4 overflow-x-auto rounded-xl p-5 px-8">
                <div className="flex items-center justify-between flex-wrap gap-2 m-3">
                    <Heading
                        variant="small"
                        title="Collocataires"
                        description="Qui sont vos colocataires et comment les contacter ?"
                    />

                    {permissions.canCreateInvitation ? (
                        <>
                            <Button
                                type="button"
                                onClick={() => setInviteDialogOpen(true)}
                            >
                                Inviter un colocataire
                            </Button>
                            <InviteMemberModal
                                foyer={foyer}
                                availableRoles={availableRoles}
                                open={inviteDialogOpen}
                                onOpenChange={setInviteDialogOpen}
                            />
                        </>
                    ) : null}

                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-2 m-3">
                    {
                        members.map((member) => (
                            <div
                                key={member.id}
                                data-test="member-row"
                                className="flex items-center justify-between flex-1 rounded-lg border p-4"
                            >
                                <div className="flex items-center gap-4">
                                    <Avatar className="h-10 w-10">
                                        {member.avatar ? (
                                            <AvatarImage
                                                src={member.avatar}
                                                alt={member.name}
                                            />
                                        ) : null}
                                        <AvatarFallback>
                                            {getInitials(member.name)}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <div className="font-medium">
                                            {member.name}
                                        </div>
                                        <div className="text-muted-foreground text-sm">
                                            {member.email}
                                        </div>
                                    </div>
                                </div>
                
                            </div>
                            
                        ))
                    }
                </div>
            </div>
        </>
    );
}

Collocataire.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: "Collocataires",
            href: props.currentFoyer
                ? `/${props.currentFoyer.slug}/collocataires`
                : "/",
        },
    ],
});