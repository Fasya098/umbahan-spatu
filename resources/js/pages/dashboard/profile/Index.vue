<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User, Role } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const { user } = useAuthStore()

const users = ref<User>({} as User);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const foto_toko = ref<any>([]);
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
    ApiService.get(`master/toko/edit/${user.id}`)
        .then(({ data }) => {
            users.value = data.data;
            foto_toko.value = data.data.foto_toko ? ["/storage/" + data.data.foto_toko] : [];
        })
        .finally(() => {
            unblock(document.getElementById("form-user"));
        });
}

function submit() {
    const formData = new FormData();
    formData.append("nama_toko", users.value.nama_toko);
    formData.append("foto_toko", users.value.foto_toko);
    formData.append("deskripsi", users.value.deskripsi);
    formData.append("alamat", users.value.alamat);
    formData.append("nomor_telepon", users.value.nomor_telepon);

    // if (user.value?.password) {
    //     formData.append("password", user.value.password);
    //     formData.append(
    //         "password_confirmation",
    //         user.value.passwordConfirmation
    //     );
    // }
    if (foto_toko.value.length) {
        formData.append("foto_toko", foto_toko.value[0].file);
    }

    // Tambahkan user_id yang diambil dari /auth/me
    if (user.id) {
        formData.append("user_id", user.id);
    }
    // if (props.selected) {
    //     formData.append("_method", "PUT");
    // }

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/toko/${props.selected}`
            : "/master/toko/store",
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
            getEdit();
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

onMounted(() => {
    getEdit();
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
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">
        <div class="card-header align-items-center">
            <h2 class="mb-0">Profile Details</h2>
            <!-- <button
                type="button"
                class="btn btn-sm btn-light-danger ms-auto"
                @click="emit('close')"
            >
                Batal
                <i class="la la-times-circle p-0"></i>
            </button> -->
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Nama Toko
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nama_toko"
                            autocomplete="off" v-model="users.nama_toko" placeholder="Masukkan Nama" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama_toko" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Deskripsi
                        </label>
                        <Field as="textarea" rows="4" class="form-control form-control-lg form-control-solid"
                            name="deskripsi" autocomplete="off" v-model="users.deskripsi"
                            placeholder="Masukkan deskripsi" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="deskripsi" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            alamat
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="alamat"
                            autocomplete="off" v-model="users.alamat" placeholder="Masukkan alamat" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="alamat" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Nomor Telepon
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nomor_telepon"
                            autocomplete="off" v-model="users.nomor_telepon" placeholder="No Telepon" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nomor_telepon" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6">
                            Foto Toko
                        </label>
                        <!--begin::Input-->
                        <file-upload :files="foto_toko" :accepted-file-types="fileTypes" required
                            v-on:updatefiles="(file) => (foto_toko = file)"></file-upload>
                        <!--end::Input-->
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="foto_toko" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
            </div>
        </div>
        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary btn-sm ms-auto">
                Simpan
            </button>
        </div>
    </VForm>
</template>
