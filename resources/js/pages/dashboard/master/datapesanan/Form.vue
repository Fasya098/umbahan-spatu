<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User, Role } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const user = ref<User>({} as User);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const foto_sepatu = ref<any>([]);
const formRef = ref();

// const formSchema = Yup.object().shape({
//     name: Yup.string().required("Nama harus diisi"),
//     email: Yup.string()
//         .email("Email harus valid")
//         .required("Email harus diisi"),
//     password: Yup.string().min(8, "Minimal 8 karakter").nullable(),
//     passwordConfirmation: Yup.string()
//         .oneOf([Yup.ref("password")], "Konfirmasi password harus sama")
//         .nullable(),
//     phone: Yup.string().required("Nomor Telepon harus diisi"),
//     role_id: Yup.string().required("Pilih role"),
// });

function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("master/pesanan/edit", props.selected)
        .then(({ data }) => {
            // user.value = data.user;
            foto_sepatu.value = data.data.foto_sepatu
                ? ["/storage/" + data.data.foto_sepatu]
                : [];
        })
        .catch((err: any) => {
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("name", user.value.name);
    formData.append("email", user.value.email);
    formData.append("phone", user.value.phone);
    formData.append("role_id", user.value.role_id);

    if (user.value?.password) {
        formData.append("password", user.value.password);
        formData.append(
            "password_confirmation",
            user.value.passwordConfirmation
        );
    }
    if (foto_sepatu.value.length) {
        formData.append("foto_sepatu", foto_sepatu.value[0].file);
    }
    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/users/${props.selected}`
            : "/master/users/store",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Data berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err: any) => {
            formRef.value.setErrors(err.response.data.errors);
            toast.error(err.response.data.message);
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

const role = useRole();
const roles = computed(() =>
    role.data.value?.map((item: Role) => ({
        id: item.id,
        text: item.full_name,
    }))
);

onMounted(async () => {
    if (props.selected) {
        getEdit();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);
</script>

<template>
    <VForm
        class="form card mb-10"
        @submit="submit"
        :validation-schema="formSchema"
        id="form-user"
        ref="formRef"
    >
        <div class="card-header align-items-center">
            <h2 class="mb-0">Lihat Foto</h2>
            <button
                type="button"
                class="btn btn-sm btn-light-danger ms-auto"
                @click="emit('close')"
            >
                Batal
                <i class="la la-times-circle p-0"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Foto Sepatu
                        </label>
                        <!--begin::Input-->
                        <file-upload
                            :files="foto_sepatu"
                            :accepted-file-types="fileTypes"
                            required
                            v-on:updatefiles="(file) => (foto_sepatu = file)"
                        ></file-upload>
                        <!--end::Input-->
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="foto_sepatu" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
            </div>
        </div>
        <!-- <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div> -->
    </VForm>
</template>
