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
import { appareil } from '@/routes/ajout';

export default function CreateAppareilModal({ children }: PropsWithChildren) {
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
                    action={appareil(page.props.currentFoyer.slug).url}
                    method='post'
                    className="space-y-6"
                    onSuccess={() => setOpen(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <DialogHeader>
                                <DialogTitle>Ajoutez un appareil</DialogTitle>
                                <DialogDescription>
                                    Ajoutez un appareil individuel.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="grid gap-2">
                                <Label htmlFor="name">Nom de l'appareil</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    placeholder="Nom de l'appareil"
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="owner">Proprietaire</Label>
                                <Input
                                    id="owner"
                                    name="owner"
                                    type="email"
                                    placeholder="proprietaire@gmail.com"
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="power">Puissance</Label>
                                <Input
                                    id="power"
                                    name="power_watt"
                                    type="number"
                                    placeholder="Puissance de l'appareil (W)"
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="usage">Utilisation</Label>
                                <Input
                                    id="usage"
                                    name="usage"
                                    type="number"
                                    placeholder="Heure d'utilisation de l'appareil (h)"
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
