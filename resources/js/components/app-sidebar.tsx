import { Link, usePage } from '@inertiajs/react';
import { BookOpen, BoxesIcon, FileChartColumnIncreasing, FolderGit2, LayoutGrid, PlugIcon, Users2, ZapIcon } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { FoyerSwitcher } from '@/components/foyer-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const page = usePage();
    const dashboardUrl = page.props.currentFoyer
        ? dashboard(page.props.currentFoyer.slug)
        : '/';
    const appareilsUrl = page.props.currentFoyer
        ? `/${page.props.currentFoyer.slug}/appareils`
        : '/';
    const facturesUrl = page.props.currentFoyer
        ? `/${page.props.currentFoyer.slug}/factures`
        : '/';
    const absencesUrl = page.props.currentFoyer
        ? `/${page.props.currentFoyer.slug}/absences`
        : '/';
    const collocatairesUrl = page.props.currentFoyer
        ? `/${page.props.currentFoyer.slug}/collocataires`
        : '/';

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboardUrl,
            icon: LayoutGrid,
        },
        {
            title: 'Collocataires',
            href: collocatairesUrl,
            icon: Users2,
        },
        {
            title: 'Appareils',
            href: appareilsUrl,
            icon: PlugIcon,
        },
        {
            title: 'Factures',
            href: facturesUrl,
            icon: FileChartColumnIncreasing,
        },
        {
            title: 'Absences',
            href: absencesUrl,
            icon: BoxesIcon,
        },
    ];


    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardUrl} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <FoyerSwitcher />
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
