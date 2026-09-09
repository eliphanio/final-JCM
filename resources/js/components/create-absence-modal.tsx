import { Form, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { absence } from '@/routes/request';

export default function CreateAbsenceModal({ children }: PropsWithChildren) {
    const [open, setOpen] = useState(false);
    const page = usePage<{
        currentFoyer: {
            slug: string;
        };
    }>();
    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>{children}</DialogTrigger>
            <DialogContent>
                <Form
                    key={String(open)}
                    action={absence(page.props.currentFoyer.slug).url}
                    method='post'
                    className="space-y-6"
                    onSuccess={() => setOpen(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <DialogHeader>
                                <DialogTitle>Envoyer une demande d'absence</DialogTitle>
                                <DialogDescription>
                                    Vous n'aurrez pas de charge durant votre absence.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="grid gap-2">
                                <Label htmlFor="start_day">Début</Label>
                                <Input
                                    id="start_day"
                                    name="start_day"
                                    type="date"
                                    placeholder="jj-mm-aaaa"
                                    required
                                />
                                <InputError message={errors.start_day} />
                            </div>
                            
                            <div className="grid gap-2">
                                <Label htmlFor="end_day">Retour</Label>
                                <Input
                                    id="end_day"
                                    name="end_day"
                                    type="date"
                                    placeholder="jj-mm-aaaa"
                                    required
                                />
                                <InputError message={errors.end_day} />
                            </div>
                            
                            <div className="grid gap-2">
                                <Label htmlFor="motif">Motif (Facultatif)</Label>
                                <Input
                                    id="motif"
                                    name="motif"
                                    placeholder="Motif de votre absence"
                                />
                                <InputError message={errors.name} />
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    disabled={processing}
                                >
                                    Ajouté
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
