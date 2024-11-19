<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User, Role, ReferensiLayanan } from "@/types";
import ApiService from "@/core/services/ApiService";
import { useRole } from "@/services/useRole";
import { useUser } from "@/services/useUser";
import { useReferensiLayanan } from "@/services/useReferensiLayanan";
import { useAuthStore } from  '@/stores/auth'

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const data = ref<User>({} as User);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const photo = ref<any>([]);
const formRef = ref();

const { user } = useAuthStore()

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
    ApiService.get("master/layanan/edit", props.selected)
        .then(({ data }) => {
            data.value = data.data;
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
    formData.append("user_id", user.id);
    formData.append("referensi_layanan_id", data.value.referensi_layanan_id);
    formData.append("harga", data.value.harga);

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/layanan/update/${props.selected}`
            : "/master/layanan/store",
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

const userService = useUser();
const pilihUser = computed(() =>
    userService.data.value?.map((item: User) => ({
        id: item.id,
        text: item.name,
    }))
)

const referensiLayanan = useReferensiLayanan();
const referensiLayanans = computed(() =>
    referensiLayanan.data.value?.map((item: ReferensiLayanan) => ({
        id: item.id,
        text: item.nama_layanan,
    }))
)

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
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Layanan</h2>
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
                        <label class="form-label fw-bold fs-6 required">
                            Nama Layanan
                        </label>
                        <Field
                            name="referensi_layanan_id"
                            type="hidden"
                            v-model="data.referensi_layanan_id"
                        >
                            <select2
                                placeholder="Pilih user"
                                class="form-select-solid"
                                :options="referensiLayanans"
                                name="referensi_layanan_id"
                                v-model="data.referensi_layanan_id"
                            >
                            </select2>
                        </Field>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="referensi_layanan_id" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Harga
                        </label>
                        <Field
                            class="form-control form-control-lg form-control-solid"
                            type="number"
                            name="harga"
                            autocomplete="off"
                            v-model="data.harga"
                            placeholder="Masukkan Harga"
                        />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga" />
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
