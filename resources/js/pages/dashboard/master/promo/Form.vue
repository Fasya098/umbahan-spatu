<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch, computed } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { User, Toko } from "@/types";
import { useToko } from "@/services/useToko";
import ApiService from "@/core/services/ApiService";
import { useAuthStore } from '@/stores/auth';
import { useRoute } from 'vue-router';
import DatePicker from '@/components/DatePicker.vue';

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);
const data = ref<User>({} as User);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const toko = ref<any>([]);
const formRef = ref();
const { user } = useAuthStore()
console.log("Toko ID:", user.toko?.id);
const route = useRoute();
const formSchema = Yup.object().shape({
    nama_promo: Yup.string().required("Nama promo harus diisi"),
    harga: Yup.string().required("Harga harus diisi"),
});

const getToko = () => {
    axios.get('/master/promo/get/${route.params.uuid}')
        .then(response => {
            toko.value = response.data;
        })
        .catch(error => {
            console.error(error);
        });
}

function getEdit() {
    block(document.getElementById("form-user"));
    ApiService.get("master/promo/edit", props.selected)
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
    formData.append("toko_id", user.toko?.id);
    formData.append("nama_promo", data.value.nama_promo);
    formData.append("harga", data.value.harga);
    formData.append("tanggal_mulai", data.value.tanggal_mulai);
    formData.append("tanggal_berakhir", data.value.tanggal_berakhir);

    if (props.selected) {
        formData.append("_method", "PUT");
    }

    block(document.getElementById("form-user"));
    axios({
        method: "post",
        url: props.selected
            ? `/master/promo/update/${props.selected}`
            : "/master/promo/store",
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

onMounted(async () => {
    // console.log(useAuthStore());
    if (props.selected) {
        getEdit();
        getToko();
        useToko();
    }
});

watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
            getToko();
        }
    }
);
</script>

<template>
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" id="form-user" ref="formRef">
        <div class="card-header align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Promo</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
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
                            Nama Promo
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="nama_promo"
                            autocomplete="off" v-model="data.nama_promo" placeholder="Masukkan Promo" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="nama_promo" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Potongan Harga
                        </label>
                        <Field class="form-control form-control-lg form-control-solid" type="number" name="harga"
                            autocomplete="off" v-model="data.harga" placeholder="Masukkan Harga" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="harga" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tanggal Mulai
                        </label>
                        <DatePicker v-model="data.tanggal_mulai" :value-type="'format'" format="YYYY-MM-DD"
                            class="form-control form-control-lg form-control-solid"
                            placeholder="Masukkan Tanggal Mulai" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tanggal_mulai" />
                            </div>
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <div class="col-md-6">
                    <!--begin::Input group-->
                    <div class="fv-row mb-7">
                        <label class="form-label fw-bold fs-6 required">
                            Tanggal Berakhir 
                        </label>
                        <DatePicker v-model="data.tanggal_berakhir" :value-type="'format'" format="YYYY-MM-DD"
                            class="form-control form-control-lg form-control-solid"
                            placeholder="Masukkan Tanggal Berakhir" />
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <ErrorMessage name="tanggal_berakhir" />
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