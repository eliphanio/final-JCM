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
import { Form, Head, usePage } from "@inertiajs/react"
import { CheckIcon, DropletIcon, PlugZap2, Plus, Wallet } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import CreateFacturerModal from "@/components/create-facture-modal";
import paye, { factures } from "@/routes/paye";

interface Facture {
    id: number;
    foyer_id: { name: string };
    electicite: number;
    eau: number;
    total: number;
    periode: Date;
    due_date: Date;
    sum: number;
}

interface Repartition {
    id: number;
    foyer: {
        name: string
    }
    user: {
        name: string
    }
    part_commun: number;
    part_appareil: number;
    total: number
}

interface FactureProps {
    facture: Facture;
}

interface RepartitonProps {
    repartitions: Repartition[]
}


export default function Facture({ facture }: FactureProps, { repartitions }: RepartitonProps) {

    const page = usePage<{
        currentFoyer: {
            slug: string;
        };
    }>();

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
                                description={
                                    facture ? (
                                        `${facture.electicite} Ar`) : (

                                        `0 Ar`
                                    )
                                }
                            />
                        </div>
                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border p-5 bg-blue-500/15 flex gap-2 items-center">
                        <DropletIcon className="size-12" />
                        <div>
                            <Heading
                                variant="small"
                                title="Eau"
                                description={
                                    facture ? (
                                        `${facture.eau} Ar`) : (

                                        `0 Ar`
                                    )
                                }
                            />
                        </div>
                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border p-5 bg-green-500/15 flex gap-2 items-center">
                        <Wallet className="size-12" />
                        <div>
                            <Heading
                                variant="small"
                                title="Total"
                                description={
                                    facture ? (
                                        `${facture.total} Ar`) : (

                                        `0 Ar`
                                    )
                                }
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
                                {
                                    repartitions.length !== 0 ? (
                                        repartitions.map((repartition)=>(
                                            <TableRow key={repartition.id}>
                                            <TableCell>{repartition.user.name}</TableCell>
                                            <TableCell>{repartition.part_appareil}</TableCell>
                                            <TableCell>{repartition.part_commun}</TableCell>
                                            <TableCell>{repartition.total}</TableCell>
                                            <TableCell>{repartition.total - facture.sum}</TableCell>
                                            <TableCell>
                                                <Dialog>
                                                    <DialogTrigger asChild>
                                                        <Button className="bg-green-500 hover:bg-green-400 h-8 w-8 p-0">
                                                            <CheckIcon className="h-4 w-4" />
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent className="sm:max-w-106.25">
                                                        <Form
                                                            action={paye.factures({
                                                                current_foyer: page.props.currentFoyer,
                                                                repartition: repartition.id // Remplacer par votre variable réelle
                                                            }).url}
                                                            method="post"
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
                                                                    Payé
                                                                </Button>
                                                            </DialogFooter>
                                                        </Form>
                                                    </DialogContent>
                                                </Dialog>
                                            </TableCell>
                                        </TableRow>
                                        ))
                                    ) : (
                                        <h2>Aucune répartition disponible</h2>
                                    )
                                }
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