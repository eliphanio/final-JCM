import { Head, Link } from '@inertiajs/react';
import { Eye, LogOut, Pencil, Plus } from 'lucide-react';
import { useState } from 'react';
import CreateFoyerModal from '@/components/create-foyer-modal';
import Heading from '@/components/heading';
import LeaveFoyerModal from '@/components/leave-foyer-modal';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit, index } from '@/routes/foyers';
import type { Foyer } from '@/types';

type Props = {
    foyers: Foyer[];
};

export default function FoyersIndex({ foyers }: Props) {
    const [leaveFoyerDialogOpen, setLeaveFoyerDialogOpen] = useState(false);
    const [foyerLeaving, setFoyerLeaving] = useState<Foyer | null>(null);

    const openLeaveFoyerDialog = (foyer: Foyer) => {
        setFoyerLeaving(foyer);
        setLeaveFoyerDialogOpen(true);
    };

    return (
        <>
            <Head title="Foyers" />

            <h1 className="sr-only">Foyers</h1>

            <div className="flex flex-col space-y-6">
                <div className="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Foyers"
                        description="Manage your foyers and foyer memberships"
                    />

                    <CreateFoyerModal>
                        <Button data-test="foyers-new-foyer-button">
                            <Plus /> New foyer
                        </Button>
                    </CreateFoyerModal>
                </div>

                <div className="space-y-3">
                    {foyers.map((foyer) => {
                        const canLeaveFoyer =
                            !foyer.isPersonal && foyer.role !== 'owner';

                        return (
                            <div
                                key={foyer.id}
                                data-test="foyer-row"
                                className="flex items-center justify-between gap-4 rounded-lg border p-4"
                            >
                                <div className="flex items-center gap-4">
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium">
                                                {foyer.name}
                                            </span>
                                            {foyer.isPersonal ? (
                                                <Badge variant="secondary">
                                                    Personal
                                                </Badge>
                                            ) : null}
                                        </div>
                                        <span className="text-muted-foreground text-sm">
                                            {foyer.roleLabel}
                                        </span>
                                    </div>
                                </div>

                                <TooltipProvider>
                                    <div className="flex items-center gap-2">
                                        {canLeaveFoyer ? (
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        data-test="foyer-leave-button"
                                                        onClick={() =>
                                                            openLeaveFoyerDialog(
                                                                foyer,
                                                            )
                                                        }
                                                    >
                                                        <LogOut className="h-4 w-4" />
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>Leave foyer</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        ) : null}

                                        {foyer.role === 'member' ? (
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        data-test="foyer-view-button"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={edit(
                                                                foyer.slug,
                                                            )}
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>View foyer</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        ) : (
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        data-test="foyer-edit-button"
                                                        asChild
                                                    >
                                                        <Link
                                                            href={edit(
                                                                foyer.slug,
                                                            )}
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>Edit foyer</p>
                                                </TooltipContent>
                                            </Tooltip>
                                        )}
                                    </div>
                                </TooltipProvider>
                            </div>
                        );
                    })}

                    {foyers.length === 0 ? (
                        <p className="text-muted-foreground py-8 text-center">
                            You don't belong to any foyers yet.
                        </p>
                    ) : null}
                </div>
            </div>

            <LeaveFoyerModal
                foyer={foyerLeaving}
                open={leaveFoyerDialogOpen}
                onOpenChange={setLeaveFoyerDialogOpen}
            />
        </>
    );
}

FoyersIndex.layout = {
    breadcrumbs: [
        {
            title: 'Foyers',
            href: index(),
        },
    ],
};
