<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import type { User, Role } from "@/types";
import { block, unblock } from "@/libs/utils";

const user = ref({} as User);
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const tokoId = ref(null); // Variabel reaktif untuk menyimpan toko_id
const tokos = ref<any>(null);
const promos = ref<any>(null);
const route = useRoute();
const uuid = route.params.uuid;

// Fetch Toko Details
const getTokoDetail = async () => {
    try {
        const response = await axios.get(`/userpage/toko/shaw/${route.params.userId}`);
        tokos.value = response.data;
    } catch (error) {
        console.error("Error fetching shoe detail:", error);
    }
};

const getTokoId = async () => {
    try {
        const response = await axios.get(`/userpage/toko/ahay/${uuid}`);
        // console.log(response.data.data.nama_toko)
        tokoId.value = response.data.data.id;
    } catch (error) {
        console.error("Error mengambil tokoId", error)
    }
};

const layanans = computed(() =>
    tokos.value?.map((item: any) => ({
        id: item.id,
        text: item.referensi_layanan.nama_layanan,
    }))
);

const router = useRouter();

function goBack(uuid) {
    router.push({ name: 'userpage.store', params: uuid });
}

const jumlahSepatu = ref(1);
const sepatuList = ref([
    { foto_sepatu: null, brand_sepatu: "", warna_sepatu: "", layanan_id: "", toko_id: tokoId },
]);

watch(jumlahSepatu, (newVal) => {
    if (newVal < 1) {
        jumlahSepatu.value = 1;
        return;
    }

    const currentLength = sepatuList.value.length;

    if (newVal > currentLength) {
        // Tambahkan item baru jika jumlah meningkat
        const newItems = Array.from({ length: newVal - currentLength }, () => ({
            foto_sepatu: null,
            brand_sepatu: "",
            warna_sepatu: "",
            layanan_id: "",
        }));
        sepatuList.value.push(...newItems);
    } else if (newVal < currentLength) {
        // Hapus item jika jumlah berkurang
        sepatuList.value.splice(newVal);
    }
});

// function updateFoto(event: Event, index: number) {
//     const file = (event.target as HTMLInputElement).files?.[0] || null;
//     sepatuList.value[index].foto_sepatu = file;
// }

function submit() {
    block(document.getElementById("form-user"));

    const requestData = {
        ...user.value,
        inputs: sepatuList.value
    };

    console.log("Data yang dikirim:", requestData);

    axios({
        method: "post",
        url: "/pesanan/store",
        data: requestData,
    })
        .then(() => {
            toast.success("Data Berhasil Disimpan!");
            router.push("/pesanan");
        })
        .catch((error) => {
            console.error(error);
            toast.error("Terjadi kesalahan saat menyimpan data!");
        }).finally(() => unblock(document.getElementById("form-user")));
}

onMounted(() => {
    getTokoDetail();
    getTokoId();
});
</script>

<template>
    <div class="background container-fluid d-flex justify-content-center align-items-center py-5">
        <VForm class="form card p-4 shadow-lg rounded border-0 mx-3" id="form-user" ref="formRef"
            @submit.prevent="submit" style="max-width: 800px; width: 100%;">
            <!-- Card Header -->
            <div class="">
                <button class="btn btn-danger px-4 py-3 rounded-3 shadow-sm ms-8 mt-6" @click="goBack">
                    <i class="las la-angle-left" style="color: white;"></i>
                    Kembali
                </button>
            </div>
            <div class="bg-white border-0 mt-6 d-flex align-items-center" style="justify-content: center;">
                <h2 class="mb-0 fw-bold">
                    Tambah Pesanan
                </h2>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Input Jumlah Sepatu -->
                <div class="mb-10">
                    <label for="jumlah_sepatu" class="form-label fw-bold text-black">
                        Berapa sepatu yang akan dicuci?
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-light text-dark">
                            <i class="fas fa-shoe-prints"></i>
                        </span>
                        <Field class="form-control form-control-lg form-control-solid border-dark" type="number"
                            name="jumlah_sepatu" min="1" max="10" autocomplete="off"
                            placeholder="Masukkan jumlah sepatu" v-model="jumlahSepatu" />
                    </div>
                </div>

                <!-- Grup Input Dinamis -->
                <div v-for="(sepatu, index) in sepatuList" :key="index" class="border border-dark rounded p-5 mb-10">
                    <h5 class="fw-bold">Sepatu {{ index + 1 }}</h5>

                    <div class="mb-3">
                        <label class="form-label">Foto Sepatu</label>
                        <file-upload :files="sepatu.foto_sepatu" :accepted-file-types="fileTypes" required
                            v-on:updatefiles="(file) => (sepatu.foto_sepatu = file)"></file-upload>
                    </div>

                    <!-- Jenis Sepatu -->
                    <div class="mb-3">
                        <label class="form-label">Brand Sepatu</label>
                        <input type="text" class="form-control form-control-lg form-control-solid"
                            placeholder="Masukkan brand sepatu" v-model="sepatu.brand_sepatu" />
                    </div>

                    <!-- Layanan -->
                    <div class="mb-3">
                        <label class="form-label">Layanan</label>
                        <select2 placeholder="Pilih layanan" class="form-select-solid" :options="layanans"
                            v-model="sepatu.layanan_id"></select2>
                    </div>

                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                <!-- <div>
                    <h5>Total Harga: Rp {{ totalHarga }}</h5>
                </div> -->
                <button @click="submit" class="btn btn-primary px-5">
                    Simpan
                </button>
            </div>
        </VForm>
    </div>
</template>

<style scoped>
.background {
    background-color: #333;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /* height: 100vh;
    width: 100vw; */
}
</style>