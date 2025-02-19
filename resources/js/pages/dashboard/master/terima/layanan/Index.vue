<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";
import { currency } from "@/libs/utils";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const handleTerima = async (requestNamaLayanan) => {
    try {
        const response = await axios.post('/master/referensi/layanan/terima', {
            request_nama_layanan: requestNamaLayanan,
        });
        // Menangani response sukses
        console.log(response.data);
        toast.success('Layanan Diterima!');
        refresh();
    } catch (error) {
        // Menangani error
        console.error(error);
        toast.error('Error menerima layanan');
    }
};

const handleTolak = async (requestNamaLayanan) => {
    try {
        const response = await axios.post('/master/referensi/layanan/tolak', {
            request_nama_layanan: requestNamaLayanan,
        });
        // Menangani response sukses
        console.log(response.data);
        toast.success('Layanan Ditolak!');
        refresh();
    } catch (error) {
        // Menangani error
        console.error(error);
        toast.error('Error menolak Layanan');
    }
};

const columns = [
    column.accessor("no", {
        header: "#",
    }),
    column.accessor("request_nama_layanan", {
        header: "Nama Layanan",
    }),
    column.accessor("id", {
        header: "Aksi",
        cell: (cell) => {
            return h("div", { class: "d-flex gap-2" }, [ // Pastikan untuk mengembalikan elemen
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-success",
                        onClick: () => handleTerima(cell.row.original.request_nama_layanan),
                    },
                    h("i", { class: "la la-check fs-2" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-danger",
                        onClick: () => handleTolak(cell.row.original.request_nama_layanan), // Jika perlu mengirim user_id
                    },
                    h("i", { class: "la la-times fs-2" })
                ),
            ]);
        },
    }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});
</script>

<template>
    <Form :selected="selected" @close="openForm = false" v-if="openForm" @refresh="refresh" />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">Terima Layanan</h2>
            <button type="button" class="btn btn-sm btn-primary ms-auto" v-if="!openForm" @click="openForm = true">
                Tambah
                <i class="la la-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <paginate ref="paginateRef" id="table-users" url="/master/request/layanan" :columns="columns"></paginate>
        </div>
    </div>
</template>
