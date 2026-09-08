import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { destroy as destroyMember } from '@/routes/foyers/members';
import type { Foyer, FoyerMember } from '@/types';

type Props = {
    foyer: Foyer;
    member: FoyerMember | null;
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function RemoveMemberModal({
    foyer,
    member,
    open,
    onOpenChange,
}: Props) {
    const [processing, setProcessing] = useState(false);

    const removeMember = () => {
        if (!member) {
            return;
        }

        router.visit(destroyMember([foyer.slug, member.id]), {
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onSuccess: () => onOpenChange(false),
        });
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Remove foyer member</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to remove{' '}
                        <strong>{member?.name}</strong> from this foyer?
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        data-test="remove-member-confirm"
                        disabled={processing}
                        onClick={removeMember}
                    >
                        Remove member
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
