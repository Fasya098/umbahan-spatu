import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                    {
                        heading: "Terima Mitra",
                        name: "master-terima",
                        route: "/dashboard/master/terima",
                    },
                    {
                        heading: "Toko",
                        name: "master-toko",
                        route: "/dashboard/master/toko",
                    },
                    {
                        heading: "Layanan",
                        name: "master-layanan",
                        route: "/dashboard/master/layanan",
                    },
                    {
                        heading: "Pesanan",
                        name: "master-pesanan",
                        route: "/dashboard/master/pesanan",
                    },
                    {
                        heading: "Referensi Layanan",
                        name: "master-referensi-layanan",
                        route: "/dashboard/master/referensi/layanan",
                    },
                ],
            },

            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
        ],
    },
];

export default MainMenuConfig;
