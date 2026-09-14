import { Head } from '@inertiajs/react';
import { useState } from 'react';
import PendingInvitationsModal from '@/components/pending-invitations-modal';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { dashboard } from '@/routes';
import type { DashboardInvitation } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartLine, PieChartIcon } from 'lucide-react';
import { ChartConfig, ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { CartesianGrid, Line, LineChart, Pie, PieSectorShapeProps, Sector, XAxis, PieChart } from 'recharts';
// import PendingAbsenceModal from '@/components/pending-absence-modal';

type Props = {
    pendingInvitations?: DashboardInvitation[];
};



export default function Dashboard({ pendingInvitations = [] }: Props) {

    const [showInvitations, setShowInvitations] = useState(
        pendingInvitations.length > 0,
    );
    // const [showAbsences, setShowAbsences] = useState(
    //     absenceRequests.length > 0,
    // );


    const chartData = [
        { month: "January", desktop: 186, mobile: 80 },
        { month: "February", desktop: 305, mobile: 200 },
        { month: "March", desktop: 237, mobile: 120 },
        { month: "April", desktop: 73, mobile: 190 },
        { month: "May", desktop: 209, mobile: 130 },
        { month: "June", desktop: 214, mobile: 140 },
    ]

    const chartConfig = {
        desktop: {
            label: "Desktop",
            color: "#2563eb",
        },
        mobile: {
            label: "Mobile",
            color: "#60a5fa",
        },
    } satisfies ChartConfig

    const chartDataPie = [
        { browser: "chrome", visitors: 275, fill: "var(--color-chrome)" },
        { browser: "safari", visitors: 200, fill: "var(--color-safari)" },
        { browser: "firefox", visitors: 187, fill: "var(--color-firefox)" },
        { browser: "edge", visitors: 173, fill: "var(--color-edge)" },
        { browser: "other", visitors: 90, fill: "var(--color-other)" },
    ]

    const chartConfigPie = {
        visitors: {
            label: "Visitors",
        },
        chrome: {
            label: "Chrome",
            color: "var(--chart-1)",
        },
        safari: {
            label: "Safari",
            color: "var(--chart-2)",
        },
        firefox: {
            label: "Firefox",
            color: "var(--chart-3)",
        },
        edge: {
            label: "Edge",
            color: "var(--chart-4)",
        },
        other: {
            label: "Other",
            color: "var(--chart-5)",
        },
    } satisfies ChartConfig

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
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                        {/* Membre      */}

                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                        {/* Appareil */}
                    </div>
                    <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
                        {/* Facture */}

                    </div>
                </div>
                <div className="flex gap-2 wrap">
                    <Card className='flex-1'>
                        <CardHeader className=''>
                            <CardTitle className='flex gap-2 align-center items-center'><PieChartIcon />  Graphe en pie</CardTitle>
                        </CardHeader>
                        <CardContent className="flex-1 pb-0">
                            <ChartContainer
                                config={chartConfigPie}
                                className="mx-auto aspect-square max-h-62.5"
                            >
                                <PieChart>
                                    <ChartTooltip
                                        cursor={false}
                                        content={<ChartTooltipContent hideLabel />}
                                    />
                                    <Pie
                                        data={chartDataPie}
                                        dataKey="visitors"
                                        nameKey="browser"
                                        innerRadius={60}
                                        strokeWidth={5}
                                        shape={({
                                            index,
                                            outerRadius = 0,
                                            ...props
                                        }: PieSectorShapeProps) =>
                                            index === ACTIVE_INDEX ? (
                                                <Sector {...props} outerRadius={outerRadius + 10} />
                                            ) : (
                                                <Sector {...props} outerRadius={outerRadius} />
                                            )
                                        }
                                    />
                                </PieChart>
                            </ChartContainer>
                        </CardContent>
                    </Card>
                    <Card className='flex-1'>
                        <CardHeader className=''>
                            <CardTitle className='flex gap-2 align-center items-center'><ChartLine /> Graphe de présentation</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ChartContainer config={chartConfig}>
                                <LineChart
                                    accessibilityLayer
                                    data={chartData}
                                    margin={{
                                        left: 12,
                                        right: 12,
                                    }}
                                >
                                    <CartesianGrid vertical={false} />
                                    <XAxis
                                        dataKey="month"
                                        tickLine={false}
                                        axisLine={false}
                                        tickMargin={8}
                                        tickFormatter={(value) => value.slice(0, 3)}
                                    />
                                    <ChartTooltip cursor={false} content={<ChartTooltipContent />} />
                                    <Line
                                        dataKey="desktop"
                                        type="monotone"
                                        stroke="var(--color-desktop)"
                                        strokeWidth={2}
                                        dot={false}
                                    />
                                    <Line
                                        dataKey="mobile"
                                        type="monotone"
                                        stroke="var(--color-mobile)"
                                        strokeWidth={2}
                                        dot={false}
                                    />
                                </LineChart>
                            </ChartContainer>
                        </CardContent>
                    </Card>
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
