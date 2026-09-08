import { Form } from '@inertiajs/react';
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
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy } from '@/routes/foyers';
import type { Foyer } from '@/types';

type Props = {
    foyer: Foyer;
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function DeleteFoyerModal({ foyer, open, onOpenChange }: Props) {
    const [confirmationName, setConfirmationName] = useState('');

    const canDeleteFoyer = confirmationName === foyer.name;

    const handleOpenChange = (nextOpen: boolean) => {
        onOpenChange(nextOpen);

        if (!nextOpen) {
            setConfirmationName('');
        }
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <DialogContent>
                <Form
                    key={String(open)}
                    {...destroy.form(foyer.slug)}
                    className="space-y-6"
                    onSuccess={() => handleOpenChange(false)}
                >
                    {({ errors, processing }) => (
                        <>
                            <DialogHeader>
                                <DialogTitle>Are you sure?</DialogTitle>
                                <DialogDescription>
                                    This action cannot be undone. This will
                                    permanently delete the foyer{' '}
                                    <strong>"{foyer.name}"</strong>.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="space-y-4 py-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="confirmation-name">
                                        Type <strong>"{foyer.name}"</strong> to
                                        confirm
                                    </Label>
                                    <Input
                                        id="confirmation-name"
                                        name="name"
                                        data-test="delete-foyer-name"
                                        value={confirmationName}
                                        onChange={(event) =>
                                            setConfirmationName(
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Enter foyer name"
                                        autoComplete="off"
                                    />
                                    <InputError message={errors.name} />
                                </div>
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>

                                <Button
                                    variant="destructive"
                                    type="submit"
                                    data-test="delete-foyer-confirm"
                                    disabled={!canDeleteFoyer || processing}
                                >
                                    Delete foyer
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
