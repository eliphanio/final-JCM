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
import { cn } from '@/lib/utils';

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
                                <select name="periode" id="periode" className={cn(
                                        "border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
                                        "focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]",
                                        "aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive"
                                      )}>
                                    <option value="01">Janvier</option>
                                    <option value="02">Février</option>
                                    <option value="03">Mars</option>
                                    <option value="04">Avril</option>
                                    <option value="05">Mai</option>
                                    <option value="06">Juin</option>
                                    <option value="07">Juillet</option>
                                    <option value="08">Août</option>
                                    <option value="09">Septembre</option>
                                    <option value="10">Octobre</option>
                                    <option value="11">Novembre</option>
                                    <option value="12">Décembre</option>
                                </select>
                                <InputError message={errors.periode} />
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
                                <InputError message={errors.due_date} />
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
                                <InputError message={errors.electricite} />
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
                                <InputError message={errors.eau} />
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
                                <InputError message={errors.consomation} />
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
