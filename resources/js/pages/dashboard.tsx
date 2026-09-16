import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PendingInvitationsModal from '@/components/pending-invitations-modal';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import type { DashboardInvitation } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { CalendarClock, ChartLine, LucideCoins, LucideUsersRound, PieChartIcon, PlugIcon, WalletMinimal } from 'lucide-react';
import { ChartConfig, ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { CartesianGrid, Line, LineChart, Pie, PieSectorShapeProps, Sector, XAxis, PieChart, AreaChart, Area } from 'recharts';
import { Badge } from '@/components/ui/badge';
import Heading from '@/components/heading';
// import PendingAbsenceModal from '@/components/pending-absence-modal';

interface members {
    name: string;
}

interface appareils {
    name: string;
}
interface facture {
    total: number;
    eau: number;
    electricite: number;
    periode: Date;
    repartition: {
        reste: number;
    }[];
    due_date: Date;
}

type Props = {
    pendingInvitations?: DashboardInvitation[];
    members: members[];
    appareils: appareils[];
    facture?: facture;
    electricite?: number[];
    eau?: number[];
    labels?: string[]
    consomAppareils: number,
    consomMembres: number,
    jourRestant?: number
};


export default function Dashboard({ pendingInvitations = [], members, appareils, facture, labels, electricite, eau, consomAppareils, consomMembres, jourRestant }: Props) {

    const RepartitionNonPayer = facture?.repartition.filter((repartition) => repartition.reste > 0)
    const RepartitionPayer = facture?.repartition.filter((repartition) => repartition.reste <= 0).length
    const TotalRepartition = facture?.repartition.length
    const totalReste = facture?.repartition.reduce(
        (total, repartition) =>
            total + Math.max(0, Number(repartition.reste)),
        0
    ) ?? 0;
    const [showInvitations, setShowInvitations] = useState(
        pendingInvitations.length > 0,
    );

    const chartData = labels ? labels.map((label, index) => ({
        mois: label,
        eau: eau ? eau[index] : 0,
        electricite: electricite ? electricite[index] : 0
    })) : [];

    const chartConfig = {
        eau: {
            label: "Eau",
            color: "#2563eb",
        },
        electricite: {
            label: "Eléctricité",
            color: "#60a5fa",
        },
    } satisfies ChartConfig

    const chartDataPie = [
        { Consomation: "eau", Prix: facture?.eau, fill: "var(--color-eau)" },
        { Consomation: "membre", Prix: consomMembres, fill: "var(--color-membre)" },
        { Consomation: "appareil", Prix: consomAppareils, fill: "var(--color-appareil)" },

    ]

    const chartConfigPie = {
        Prix: {
            label: "Prix",
        },
        eau: {
            label: "Eau",
            color: "#2563eb",
        },
        membre: {
            label: "Membres",
            color: "#16a34a",
        },
        appareil: {
            label: "Appareils",
            color: "#f97316",
        },
    } satisfies ChartConfig;

    const ACTIVE_INDEX = 0

    return (
        <>
            <Head title="Dashboard" />

            <PendingInvitationsModal
                invitations={pendingInvitations}
                open={pendingInvitations.length > 0 && showInvitations}
                onOpenChange={setShowInvitations}
            />
            {/* {<PendingAbsenceModal
                absences={absenceRequests}
                open={absenceRequests.length > 0 && showAbsences}
                onOpenChange={setShowAbsences}
            />} */}
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Heading
                    variant="small"
                    title="Dashboard"
                    description="Gerez votre foyer avec facilité"
                />
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">

                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative  overflow-hidden rounded-xl border p-2 bg-yellow-500/15">

                        <div className="flex items-center gap-4">
                            <LucideUsersRound className="h-15 w-15" />

                            <div>
                                <div className="font-medium">
                                    Collocataires
                                </div>
                                <div className="text-muted-foreground text-xl">

                                    {/* Membre      */
                                        members.length
                                    }

                                </div>
                                <div className='font-medium text-sm'>
                                    Les membre du foyer
                                </div>
                            </div>
                        </div>

                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative  overflow-hidden rounded-xl border p-2 bg-blue-500/15">
                        <div className="flex items-center gap-4">
                            <PlugIcon className="h-15 w-15" />

                            <div>
                                <div className="font-medium">
                                    Appareils
                                </div>
                                <div className="text-muted-foreground text-xl">
                                    {/* Appareil      */
                                        appareils.length
                                    }
                                </div>
                                <div className='font-medium text-sm'>
                                    Les appareils enregisté
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative  overflow-hidden rounded-xl border p-2 bg-green-500/15">

                        <div className="flex items-center gap-4">
                            <LucideCoins className="h-15 w-15" />

                            <div>
                                <div className="font-medium">
                                    Facture
                                </div>
                                <div className="text-muted-foreground text-xl">

                                    {/* Facture      */
                                        facture ? `${facture.total} Ar` : "Aucune facture"
                                    }

                                </div>
                                <Badge className='font-medium text-sm bg-primary rounded-[80px]'>
                                    {
                                        facture ? new Date(facture.periode).toLocaleDateString("fr-FR", {
                                            month: "long",
                                        }) : "Periode"
                                    }
                                </Badge>
                            </div>
                        </div>

                    </div>
                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-2">

                    <Card className='bg-blue-500/15'>
                        <CardHeader className=''>
                            <CardTitle className='flex gap-2 align-center items-center'><ChartLine /> Graphe de présentation</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ChartContainer config={chartConfig}>
                                <AreaChart
                                    accessibilityLayer
                                    data={chartData}
                                    margin={{
                                        left: 12,
                                        right: 12,
                                    }}
                                >
                                    <CartesianGrid vertical={false} />

                                    <XAxis
                                        dataKey="mois"
                                        tickLine={false}
                                        axisLine={false}
                                        tickMargin={8}
                                        tickFormatter={(value) => value.slice(0, 3)}
                                    />

                                    <ChartTooltip
                                        cursor={false}
                                        content={<ChartTooltipContent />}
                                    />

                                    <Area
                                        dataKey="eau"
                                        type="monotone"
                                        stroke="var(--color-eau)"
                                        strokeWidth={2}
                                        dot={false}
                                    />

                                    <Area
                                        dataKey="electricite"
                                        type="monotone"
                                        stroke="var(--color-electricite)"
                                        strokeWidth={2}
                                        dot={false}
                                    />
                                </AreaChart>
                            </ChartContainer>
                        </CardContent>
                    </Card>

                    <div className="grid gap-4 md:grid-rows-2">

                        <div className="border-sidebar-border/70 dark:border-sidebar-border relative  overflow-hidden rounded-xl border p-2 bg-orange-500/15">

                            <div className='flex items-center gap-1 m-2'>
                                <WalletMinimal /> Etat de payement
                            </div>
                            <div className='m-2 flex justify-between'>
                                <div className="font-medium">
                                    Echeance
                                </div>
                                <div className="">

                                    {/* Membre      */
                                        facture ? new Date(facture.due_date).toLocaleDateString("fr-FR", {
                                            day: "numeric",
                                            month: "long",
                                            year: "numeric"
                                        }) : "Aucune facture"
                                    }

                                </div>
                                <div className="">

                                    {/* Membre      */
                                        jourRestant !== undefined && jourRestant !== null ? (jourRestant > 0 ? `${jourRestant}j restant` : "Temps dépasé") : "Pas encore d'echeance"
                                    }

                                </div>
                            </div>

                            <div className='m-2 flex justify-between'>
                                <div className="font-medium">
                                    Repartition payer
                                </div>
                                <div className="">

                                    {/* Membre      */
                                        facture && RepartitionPayer !== undefined ? `${RepartitionPayer}/${facture.repartition.length}` : "0/0"
                                    }

                                </div>
                                <div className="">

                                    {/* Membre      */
                                        facture && RepartitionPayer ? `${(RepartitionPayer / facture.repartition.length) * 100}%` : "0%"
                                    }

                                </div>
                            </div>
                            <div className='m-2 flex justify-between'>
                                <div className="font-medium">
                                    Repartition non payer
                                </div>
                                <div className="">

                                    {/* Membre      */
                                        facture && RepartitionNonPayer ? `${RepartitionNonPayer.length}/${facture.repartition.length}` : "0/0"
                                    }

                                </div>
                                <div className="">

                                    {/* Membre      */
                                        RepartitionNonPayer && TotalRepartition ? `${(RepartitionNonPayer.length / TotalRepartition) * 100}%` : "0%"
                                    }

                                </div>
                            </div>
                            <hr className='border-orange-300' />
                            <div className='m-2 flex items-center justify-between'>
                                <div className="font-medium">
                                    SOMME A PAYER
                                </div>
                                <div className="text-orange-500 text-2xl font-mono">

                                    {/* Membre      */
                                        facture ? `${totalReste.toLocaleString('fr-FR')} Ar` : "0 Ar"
                                    }

                                </div>
                            </div>
                        </div>
                        <div className="border-sidebar-border/70 dark:border-sidebar-border relative  overflow-hidden rounded-xl border p-2 bg-blue-500/15 gap-4">
                            <Heading
                                title='Consomation du mois'
                                description='Comment est repartie votre facture'
                                
                            />
                            <div className='text-xl'>
                                <div className='m-2 my-5 flex justify-between'>
                                    <div className="font-medium text-yellow-500">
                                        Consomation éléctrique
                                    </div>
                                    <div className="">

                                        {/* Membre      */
                                            facture ? `${facture.electricite} Ar` : "0 Ar"
                                        }

                                    </div>
                                </div>
                                <div className='m-2 flex justify-between'>
                                    <div className="font-medium text-green-500">
                                        Consomation d'eau
                                    </div>
                                    <div className="">

                                        {/* Membre      */
                                            facture ? `${facture.eau} Ar` : "0 Ar"
                                        }

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div >
        </>
    );
}

Dashboard.layout = (props: { currentFoyer?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: props.currentFoyer ? dashboard(props.currentFoyer.slug) : '/',
        },
    ],
});
