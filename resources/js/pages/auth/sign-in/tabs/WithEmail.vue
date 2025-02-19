<template>
    <VForm class="form w-100" @submit="submit" :validation-schema="login">
        <!--begin::Input group-->
        <div class="fv-row mb-5">
            <!--begin::Label-->
            <label class="form-label fs-6 fw-bold">Email</label>
            <!--end::Label-->

            <!--begin::Input-->
            <Field tabindex="1" class="form-control form-control-lg form-control-solid" type="text" name="email"
                autocomplete="off" v-model="data.email" style="margin-bottom: 10px;" />
            <!--end::Input-->
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="email" />
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-5">
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-2">
                <!--begin::Label-->
                <label class="form-label fw-bold fs-6 mb-0">Password</label>
                <!--end::Label-->

                <!--begin::Link-->
                <router-link to="/lupa-password" class="link-primary fs-6 fw-bold">
                    Lupa Password ?
                </router-link>
                <!--end::Link-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Input-->
            <div class="position-relative mb-3">
                <!--begin::Input-->
                <Field tabindex="2" class="form-control form-control-lg form-control-solid" type="password"
                    name="password" v-model="data.password" autocomplete="off" />
                <!--end::Input-->

                <!--begin::Visibility toggle-->
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 ms-1">
                    <i class="bi bi-eye-slash fs-2" @click="togglePassword"></i>
                </span>
                <!--end::Visibility toggle-->
            </div>
            <!--end::Input-->
            <div class="fv-plugins-message-container">
                <div class="fv-help-block">
                    <ErrorMessage name="password" />
                </div>
            </div>
        </div>
        <!--end::Input group-->

        <!-- <div class="form-check mb-10">
            <Field tabindex="3" class="form-check-input" type="checkbox" id="remember_me" name="remember_me" value="1"
                v-model="data.remember_me" />
            <label class="form-check-label" for="remember_me">
                {{ $t('login.remember') }}
            </label>
        </div> -->

        <!--begin::Actions-->
        <div class="text-center">
            <!--begin::Submit button-->
            <button tabindex="3" type="submit" ref="submitButton" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Login</span>

                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>

            <div v-if="showRegisterLink">
                <router-link to="/register" class="link-primary fs-6 fw-bold" style="color: white !important;">
                    Belum punya akun? Register
                </router-link>
            </div>
            <!--end::Submit button-->
        </div>
        <!--end::Actions-->
    </VForm>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify"
import { blockBtn, unblockBtn } from "@/libs/utils"

export default defineComponent({
    setup() {
        const store = useAuthStore();
        const router = useRouter();

        const showRegisterLink = computed(() => {
            return router.options.history.state.back?.startsWith("/userpage/store/");
        });
        const submitButton = ref(null);

        //Create form validation object
        const login = Yup.object().shape({
            email: Yup.string().email('Email tidak valid').required("Harap masukkan Email").label("Email"),
            password: Yup.string().min(8, 'Password minimal terdiri dari 8 karakter').required('Harap masukkan password').label("Password"),
        });

        return {
            login,
            submitButton,
            getAssetPath,
            store,
            router,
            showRegisterLink,
        };
    },
    data() {
        return {
            data: {
                email: '',
                password: '',
            },
        }
    },
    methods: {
    async submit() {
        blockBtn(this.submitButton);
        
        try {
            const res = await axios.post("/auth/login", { ...this.data, type: "email" });
            
            // Set auth data
            this.store.setAuth(res.data.user, res.data.token);
            
            // Set authentication state
            localStorage.setItem('isAuthenticated', 'true');
            
            // Handle navigation after login
            const intendedRoute = localStorage.getItem('intended_route');
            if (intendedRoute) {
                const route = JSON.parse(intendedRoute);
                localStorage.removeItem('intended_route');
                
                // Validate route data before navigation
                if (route.name && route.params) {
                    this.router.push(route);
                } else {
                    this.handleDefaultNavigation(res.data.user.role_id);
                }
            } else {
                this.handleDefaultNavigation(res.data.user.role_id);
            }
            
        } catch (error) {
            toast.error(error.response.data.message);
        } finally {
            unblockBtn(this.submitButton);
        }
    },

    handleDefaultNavigation(roleId) {
        if (roleId === 3) {
            this.router.push("/userpage");
        } else {
            this.router.push("/dashboard");
        }
    },

    togglePassword(ev) {
        const passwordInput = document.querySelector("[name=password]");
        const isPassword = passwordInput.type === 'password';
        
        passwordInput.type = isPassword ? 'text' : 'password';
        ev.target.classList.toggle("bi-eye", isPassword);
        ev.target.classList.toggle("bi-eye-slash", !isPassword);
    }
}
})
</script>
