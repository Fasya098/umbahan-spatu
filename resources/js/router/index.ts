import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useConfigStore } from "@/stores/config";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

declare module "vue-router" {
    interface RouteMeta {
        pageTitle?: string;
        permission?: string;
    }
}

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        redirect: "/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: {
            middleware: "auth",
        },
        children: [
            {
                path: "/dashboard",
                name: "dashboard",
                component: () => import("@/pages/dashboard/Index.vue"),
                meta: {
                    pageTitle: "Dashboard",
                    breadcrumbs: ["Dashboard"],
                },
            },
            {
                path: "/dashboard/profile",
                name: "dashboard.profile",
                component: () => import("@/pages/dashboard/profile/Index.vue"),
                meta: {
                    pageTitle: "Profil Toko",
                    breadcrumbs: ["Profil-Toko"],
                },
            },
            {
                path: "/dashboard/setting",
                name: "dashboard.setting",
                component: () => import("@/pages/dashboard/setting/Index.vue"),
                meta: {
                    pageTitle: "Website Setting",
                    breadcrumbs: ["Website", "Setting"],
                },
            },

            // MASTER
            {
                path: "/dashboard/master/users/roles",
                name: "dashboard.master.users.roles",
                component: () =>
                    import("@/pages/dashboard/master/users/roles/Index.vue"),
                meta: {
                    pageTitle: "User Roles",
                    breadcrumbs: ["Master", "Users", "Roles"],
                },
            },
            {
                path: "/dashboard/master/users",
                name: "dashboard.master.users",
                component: () =>
                    import("@/pages/dashboard/master/users/Index.vue"),
                meta: {
                    pageTitle: "Users",
                    breadcrumbs: ["Master", "Users"],
                },
            },
            {
                path: "/dashboard/master/pesanan",
                name: "dashboard.master.pesanan",
                component: () =>
                    import("@/pages/dashboard/master/pesanan/Index.vue"),
                meta: {
                    pageTitle: "Pesanan",
                    breadcrumbs: ["Master", "Pesanan"],
                },
            },
            {
                path: "/dashboard/master/datapesanan",
                name: "dashboard.master.datapesanan",
                component: () =>
                    import("@/pages/dashboard/master/datapesanan/Index.vue"),
                meta: {
                    pageTitle: "Data Pesanan",
                    breadcrumbs: ["Master", "Data Pesanan"],
                },
            },
            {
                path: "/dashboard/master/toko",
                name: "dashboard.master.toko",
                component: () =>
                    import("@/pages/dashboard/master/toko/Index.vue"),
                meta: {
                    pageTitle: "Toko",
                    breadcrumbs: ["Master", "Toko"],
                },
            },
            {
                path: "/dashboard/master/referensi/layanan",
                name: "dashboard.master.referensi.layanan",
                component: () =>
                    import(
                        "@/pages/dashboard/master/referensi/layanan/Index.vue"
                    ),
                meta: {
                    pageTitle: "Referensi Layanan",
                    breadcrumbs: ["Master", "Referensi Layanan"],
                },
            },
            {
                path: "/dashboard/master/request/layanan",
                name: "dashboard.master.request.layanan",
                component: () =>
                    import(
                        "@/pages/dashboard/master/request/layanan/Index.vue"
                    ),
                meta: {
                    pageTitle: "Request Layanan",
                    breadcrumbs: ["Master", "Request Layanan"],
                },
            },
            {
                path: "/dashboard/master/layanan",
                name: "dashboard.master.layanan",
                component: () =>
                    import("@/pages/dashboard/master/layanan/Index.vue"),
                meta: {
                    pageTitle: "Layanan",
                    breadcrumbs: ["master", "Layanan"],
                },
            },
            {
                path: "/dashboard/master/datalayanan",
                name: "dashboard.master.datalayanan",
                component: () =>
                    import("@/pages/dashboard/master/datalayanan/Index.vue"),
                meta: {
                    pageTitle: "Data Layanan",
                    breadcrumbs: ["master", "Data Layanan"],
                },
            },
            {
                path: "/dashboard/master/promo",
                name: "dashboard.master.promo",
                component: () =>
                    import("@/pages/dashboard/master/promo/Index.vue"),
                meta: {
                    pageTitle: "Promo",
                    breadcrumbs: ["master", "Promo"],
                },
            },
            {
                path: "/dashboard/master/terima",
                name: "dashboard.master.terima",
                component: () =>
                    import("@/pages/dashboard/master/terima/Index.vue"),
                meta: {
                    pageTitle: "Terima",
                    breadcrumbs: ["Master", "Terima"],
                },
            },
            {
                path: "/dashboard/master/terima/layanan",
                name: "dashboard.master.terima.layanan",
                component: () =>
                    import("@/pages/dashboard/master/terima/layanan/Index.vue"),
                meta: {
                    pageTitle: "Terima Layanan",
                    breadcrumbs: ["Master", "Terima Layanan"],
                },
            },
        ],
    },
    {
        path: "/register",
        name: "/register",
        component: () => import("@/pages/userpage/register/Index.vue"),
    },
    {
        path: "/cek-pesanan",
        name: "/cek-pesanan",
        component: () => import("@/pages/userpage/cekpesanan/Index.vue"),
    },
    {
        path: "/lupa-password",
        name: "/lupa-password",
        component: () => import("@/pages/userpage/lupa/Index.vue"),
    },
    {
        path: "/login",
        name: "/login",
        component: () => import("@/pages/auth/sign-in/user/Login.vue"),
    },
    {
        path: "/userpage",
        name: "userpage",
        component: () => import("@/pages/userpage/Index.vue"),
    },
    {
        path: "/userpage/otp",
        name: "userpage.otp",
        component: () => import("@/pages/userpage/KodeOtp.vue"),
    },
    {
        path: "/userpage/store/:uuid",
        name: "userpage.store",
        component: () => import("@/pages/userpage/Store.vue"),
    },
    {
        path: "/userpage",
        name: "/userpage",
        component: () => import("@/pages/userpage/Index.vue"),
    },
    {
        path: "/userpage/mitra",
        name: "/userpage/mitra",
        component: () => import("@/pages/userpage/mitra/Index.vue"),
    },
    {
        path: "/userpage/form/:uuid/:userId",
        name: "userpage.form",
        component: () => import("@/pages/userpage/Form.vue"),
    },
    {
        path: "/",
        component: () => import("@/layouts/AuthLayout.vue"),
        children: [
            {
                path: "/sign-in",
                name: "sign-in",
                component: () => import("@/pages/auth/sign-in/Index.vue"),
                meta: {
                    pageTitle: "Sign In",
                    middleware: "guest",
                },
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/AuthLayout.vue"),
        children: [
            {
                path: "/sign-in",
                name: "sign-in",
                component: () => import("@/pages/auth/sign-in/Index.vue"),
                meta: {
                    pageTitle: "Sign In",
                    middleware: "guest",
                },
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/SystemLayout.vue"),
        children: [
            {
                // the 404 route, when none of the above matches
                path: "/404",
                name: "404",
                component: () => import("@/pages/errors/Error404.vue"),
                meta: {
                    pageTitle: "Error 404",
                },
            },
            {
                path: "/500",
                name: "500",
                component: () => import("@/pages/errors/Error500.vue"),
                meta: {
                    pageTitle: "Error 500",
                },
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },
    {
        path: "/userpage/index",
        name: "userpage.index",
        component: () => import("@/pages/userpage/Index.vue"),
        // meta: {
        //     pageTitle: "User Roles",
        //     breadcrumbs: ["Master", "Users", "Roles"],
        // },
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
    scrollBehavior(to) {
        // If the route has a hash, scroll to the section with the specified ID; otherwise, scroll to the top of the page.
        if (to.hash) {
            return {
                el: to.hash,
                top: 80,
                behavior: "smooth",
            };
        } else {
            return {
                top: 0,
                left: 0,
                behavior: "smooth",
            };
        }
    },
});

router.beforeEach((to, from, next) => {
    if (to.name === 'userpage.form') {
        // Check if user is authenticated
        const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
        
        if (!isAuthenticated) {
            next('/sign-in');
        } else {
            next();
        }
    } else {
        next();
    }
});

router.beforeEach(async (to, from, next) => {
    if (to.name) {
        // Start the route progress bar.
        NProgress.start();
    }

    const authStore = useAuthStore();
    const configStore = useConfigStore();

    // current page view title
    if (to.meta.pageTitle) {
        document.title = `${to.meta.pageTitle} - ${
            import.meta.env.VITE_APP_NAME
        }`;
    } else {
        document.title = import.meta.env.VITE_APP_NAME as string;
    }

    // reset config to initial state
    configStore.resetLayoutConfig();

    // verify auth token before each page change
    if (!authStore.isAuthenticated) await authStore.verifyAuth();

    // before page access check if page requires authentication
    if (to.meta.middleware == "auth") {
        if (authStore.isAuthenticated) {
            if (
                to.meta.permission &&
                !authStore.user.permission.includes(to.meta.permission)
            ) {
                next({ name: "404" });
            } else if (to.meta.checkDetail == false) {
                next();
            }

            next();
        } else {
            next({ name: "sign-in" });
        }
    } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
        if (authStore.user.role_id === 3) {
            next({ name: "userpage" });
        } else {
            next({ name: "dashboard" }); // Assuming there's a separate dashboard for other roles
        }
    } else {
        next();
    }
});

router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done();
});

export default router;
