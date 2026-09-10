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
import { store } from '@/routes/foyers';
import { factures } from '@/routes/ajout';

export default function CreateFacturerModal({ children }: PropsWithChildren) {
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
                    action={factures(page.props.currentFoyer.slug).url}
                    method='post'
                    className="space-y-6"
                    onSuccess={() => setOpen(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <DialogHeader>
                                <DialogTitle>Ajouter une nouvelle facture</DialogTitle>
                            </DialogHeader>

                            <div className="grid gap-2">
                                <Label htmlFor="periode">Periode</Label>
                                <Input
                                    id="periode"
                                    name="periode"
                                    type='month'
                                    placeholder='Janvier'
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="due_date">Echeance</Label>
                                <Input
                                    id="due_date"
                                    name="due_date"
                                    type='date'
                                    placeholder='jj-mm-aaaa'
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="electricite">Prix d'éléctricité</Label>
                                <Input
                                    id="electricite"
                                    name="electricite"
                                    type='number'
                                    placeholder='10000'
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="eau">Prix d'eau</Label>
                                <Input
                                    id="eau"
                                    name="eau"
                                    type='number'
                                    placeholder='10000'
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="consomation">consomation d'éléctricité</Label>
                                <Input
                                    id="consomation"
                                    name="consomation"
                                    type='number'
                                    placeholder='250 (W)'
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    data-test="create-foyer-submit"
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
