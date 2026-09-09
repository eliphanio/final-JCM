import Heading from "@/components/heading";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'; import { Button } from "@/components/ui/button";
import { Form, Head } from "@inertiajs/react"
import { CheckIcon, DropletIcon, PlugZap2, Plus, Wallet } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import CreateFacturerModal from "@/components/create-facture-modal";

export default function Facture() {
    return (
        <>
            <Head title="Facture" />

            <div className=" gap-4 overflow-x-auto rounded-xl p-5 px-8">
                <div className="flex items-center justify-between flex-wrap gap-2 m-3 ">
                    <Heading
                        variant="small"
                        title="Facture"
                        description="Gérez sans problème les factures de votre foyer"
                    />
                    <CreateFacturerModal>
                        <Button >
                            <Plus /> Nouvelle facture
                        </Button>
                    </CreateFacturerModal>
                </div>

                <div className="grid auto-rows-min gap-4 md:grid-cols-3 m-3">
                    <div className="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border p-5 bg-yellow-500/15 flex gap-2 items-center">
                        <PlugZap2 className="size-12" />
                        <div>
                            <Heading
                                variant="small"
                                title="Eléctricité"
                                description="500000 Ar"
                            />
                        </div>
                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border p-5 bg-blue-500/15 flex gap-2 items-center">
                        <DropletIcon className="size-12" />
                        <div>
                            <Heading
                                variant="small"
                                title="Eau"
                                description="500000 Ar"
                            />
                        </div>
                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border p-5 bg-green-500/15 flex gap-2 items-center">
                        <Wallet className="size-12" />
                        <div>
                            <Heading
                                variant="small"
                                title="Total"
                                description="500000 Ar"
                            />

                        </div>
                    </div>
                </div>

                <div className="border-sidebar-border/70 dark:border-sidebar-border relative flex-1 overflow-hidden rounded-xl border md:min-h-min p-5 m-3">

                    <>
                        <h2>Payement non effectué</h2>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nom</TableHead>
                                    <TableHead>Consommation appareil</TableHead>
                                    <TableHead>Consommation individuel</TableHead>
                                    <TableHead>Total à payé</TableHead>
                                    <TableHead>Reste à payé</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow >
                                    <TableCell>Nom</TableCell>
                                    <TableCell>consommation appareil</TableCell>
                                    <TableCell>Consommation individuel</TableCell>
                                    <TableCell>Total à payé</TableCell>
                                    <TableCell>Reste à payé</TableCell>
                                    <TableCell>
                                        <Dialog>
                                            <DialogTrigger asChild>
                                                <Button className="bg-green-500 hover:bg-green-400 h-8 w-8 p-0">
                                                    <CheckIcon className="h-4 w-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent className="sm:max-w-106.25">
                                                <Form
                                                    className="space-y-6"
                                                >
                                                    <DialogHeader>
                                                        <DialogTitle>Effectué un payement</DialogTitle>
                                                        <DialogDescription>
                                                            Payer une part ou la totalité de votre charge.
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="amount">Proprietaire</Label>
                                                        <Input
                                                            id="amount"
                                                            name="amount"
                                                            type="number"
                                                            placeholder="10000"
                                                            required
                                                        />
                                                    </div>
                                                    <DialogFooter className="gap-2">
                                                        <DialogClose asChild>
                                                            <Button variant="secondary">Cancel</Button>
                                                        </DialogClose>

                                                        <Button
                                                            type="submit"
                                                        >
                                                            Ajouté
                                                        </Button>
                                                    </DialogFooter>
                                                </Form>
                                            </DialogContent>
                                        </Dialog>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                    </>

                </div>

            </div>
        </>
    );
}

Facture.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Factures',
            href: props.currentFoyer ? `/${props.currentFoyer.slug}/factures` : '/',
        },
    ],
});