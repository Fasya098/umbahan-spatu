<script setup lang="ts">
import { h, onMounted, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";
import { useAuthStore } from "@/stores/auth";
import axios from 'axios';

const auth = useAuthStore();
const mitraId = auth.user.id;
const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const tokoId = ref<number | null>(null);

const getToko = async () => {
    try {
        const response = await axios.get(`/dashboard/get/${mitraId}`);
        tokoId.value = response.data.data.id;
        if (paginateRef.value) {
            paginateRef.value.refetch();
        }
    } catch (error) {
        console.error("Terjadi kesalahan:", error);
    }
};

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const handleOrderAction = async (uuid: string, action: 'accept' | 'reject') => {
    try {
        if (action === 'accept') {
            await axios.post(`/master/pesanan/terima/${uuid}`);
        } else {
            await axios.delete(`/master/pesanan/tolak/${uuid}`);
        }
        paginateRef.value.refetch();
    } catch (error) {
        console.error(`Error ${action}ing order:`, error);
    }
};

const handleDelivery = async (uuid: string) => {
    try {
        await axios.post(`/master/pesanan/kirim/${uuid}`);
        paginateRef.value.refetch();
    } catch (error) {
        console.error("Error processing delivery:", error);
    }
};

const columns = [
    column.accessor("no", {
        header: "#",
    }),
    column.accessor("user.name", {
        header: "Customer",
    }),
    column.accessor("toko.nama_toko", {
        header: "Toko",
    }),
    column.accessor("layanan.referensi_layanan.nama_layanan", {
        header: "Layanan",
    }),
    column.accessor("brand_sepatu", {
        header: "Brand sepatu",
    }),
    column.accessor("warna_sepatu", {
        header: "Warna sepatu",
    }),
    column.accessor("tanggal_pesanan", {
        header: "Tanggal memesan",
    }),
    column.accessor("status", {
        header: "Status",
    }),
    column.accessor("total_harga", {
        header: "Total harga",
    }),
    column.accessor("foto_sepatu", {
        header: "Foto sepatu",
        cell: cell => h('img', { src: `/storage/${cell.getValue()}`, width: 100 })
    }),
    column.accessor("uuid", {
        header: "Aksi",
        cell: (info) => {
            const uuid = info.getValue();
            // Tambahkan console.log untuk debugging
            console.log('Row Data:', info.row.original);
            
            return h("div", { class: "d-flex flex-column gap-2" }, [
                // Top section (eye & trash) - always visible
                h(
                    "div",
                    { class: "d-flex gap-2" },
                    [
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-icon btn-info",
                                onClick: () => {
                                    selected.value = uuid;
                                    openForm.value = true;
                                },
                            },
                            h("i", { class: "la la-eye fs-2" })
                        ),
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-icon btn-danger",
                                onClick: () =>
                                    deleteUser(`/master/pesanan/destroy/${uuid}`),
                            },
                            h("i", { class: "la la-trash fs-2" })
                        ),
                    ]
                ),
                // Status = 1: Show accept/reject buttons
                info.row.original.status === "1" && h(
                    "div",
                    { class: "d-flex gap-2" },
                    [
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-icon btn-success",
                                onClick: () => handleOrderAction(uuid, 'accept')
                            },
                            h("i", { class: "la la-check fs-2" })
                        ),
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-icon btn-danger",
                                onClick: () => handleOrderAction(uuid, 'reject')
                            },
                            h("i", { class: "la la-close fs-2" })
                        ),
                    ]
                ),
                // Status = 2: Show delivery button
                info.row.original.status === "2" && h(
                    "div",
                    { class: "d-flex gap-2" },
                    [
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-icon btn-primary",
                                onClick: () => handleDelivery(uuid)
                            },
                            h("i", { class: "la la-truck fs-2" })
                        ),
                    ]
                ),
                // Status = 3: Show delivered badge
                info.row.original.status === "3" && h(
                    "div",
                    { class: "d-flex gap-2" },
                    [
                        h(
                            "span",
                            { class: "badge bg-success" },
                            "Delivered"
                        ),
                    ]
                ),
            ]);
        },
    })
];

const getApiUrl = () => {
    return tokoId.value ? `/pesanan?toko_id=${tokoId.value}` : '/pesanan';
};

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo(0, 0);
});

watch(tokoId, () => {
    if (paginateRef.value) {
        paginateRef.value.refetch();
    }
});

onMounted(() => {
    getToko();
});
</script>

<template>
    <Form :selected="selected" @close="openForm = false" v-if="openForm" @refresh="refresh" />

    <div class="card">
        <div class="card-header align-items-center">
            <h2 class="mb-0">List Pesanan</h2>
            <!-- <button type="button" class="btn btn-sm btn-icon btn-primary ms-auto" v-if="!openForm" @click="openForm = true">
                Tambah
                <i class="la la-plus"></i>
            </button> -->
        </div>
        <div class="card-body">
            <paginate ref="paginateRef" id="table-users" :url="getApiUrl()" :columns="columns"></paginate>
        </div>
    </div>
</template>