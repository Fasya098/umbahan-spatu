<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const tokoId = ref(null); // Variabel reaktif untuk menyimpan toko_id
const tokos = ref<any>(null);
const promos = ref<any>(null);
const route = useRoute();

// Fetch Toko Details
const getTokoDetail = async () => {
    try {
        const response = await axios.get(`/userpage/toko/shaw/${route.params.userId}`);
        tokos.value = response.data;
    } catch (error) {
        console.error("Error fetching shoe detail:", error);
    }
};

// Fetch Promo by Toko
const getPromoByToko = async () => {
    try {
        const response = await axios.get(`/userpage/toko/ahay/${route.params.uuid}`);
        promos.value = response.data;
        if (response.data.length > 0) {
            tokoId.value = response.data[0].toko_id; // Simpan toko_id ke variabel reaktif
            console.log("Toko ID:", tokoId);
        } else {
            console.log("Data tidak tersedia");
        }
    } catch (error) {
        console.error("Error fetching promo:", error);
    }
};

const layanans = computed(() =>
    tokos.value?.map((item: any) => ({
        id: item.id,
        text: item.referensi_layanan.nama_layanan,
    }))
);

const prms = computed(() =>
    promos.value?.map((item: any) => ({
        id: item.id,
        text: item.nama_promo,
    }))
);

const router = useRouter();
const jumlahSepatu = ref(1);
const sepatuList = ref(
    Array.from({ length: jumlahSepatu.value }, () => ({
        foto: null,
        jenis: "",
        warna: "",
        layanan: "",
        promo: null,
    }))
);

watch(jumlahSepatu, (newVal) => {
    if (newVal > sepatuList.value.length) {
        const newItems = Array.from({ length: newVal - sepatuList.value.length }, () => ({
            foto: null,
            jenis: "",
            warna: "",
            layanan: "",
            promo: null,
        }));
        sepatuList.value.push(...newItems);
    } else {
        sepatuList.value.splice(newVal);
    }
});

// Calculate Total Price
const totalHarga = computed(() => {
    return sepatuList.value.reduce((total, sepatu) => {
        let harga = 0;

        if (sepatu.layanan) harga += 15000; // Contoh harga layanan
        if (sepatu.warna === "putih") harga += 5000;
        if (sepatu.promo) harga -= 6000; // Contoh promo

        return total + harga;
    }, 0);
});

function updateFoto(event: Event, index: number) {
    const file = (event.target as HTMLInputElement).files?.[0] || null;
    sepatuList.value[index].foto = file;
}

function goBack() {
    router.go(-1);
}

function submit() {
    const isValid = sepatuList.value.every((sepatu) => sepatu.jenis && sepatu.warna && sepatu.layanan);
    if (!isValid) {
        toast.error("Pastikan semua data sepatu terisi!");
        return;
    }

    const confirmed = confirm(`Total Harga: Rp ${totalHarga.value}. Apakah Anda yakin ingin melanjutkan?`);
    if (!confirmed) return;

    const formData = new FormData();

    // Pastikan toko_id tersedia
    if (!tokoId.value) {
        toast.error("Toko ID tidak ditemukan!");
        return;
    }

    // Append the general fields for the order
    formData.append("toko_id", tokoId.value); // Assuming toko_id is taken from the route
    formData.append("tanggal_pesanan", new Date().toISOString().split("T")[0]); // Current date as the order date
    formData.append("status", "1"); // Example status (1 = Penjemputan)
    formData.append("total_harga", totalHarga.value.toString());

    // Append each sepatu data dynamically
    sepatuList.value.forEach((sepatu, index) => {
        formData.append(`sepatu[${index}][layanan_id]`, sepatu.layanan);
        formData.append(`sepatu[${index}][promo_id]`, sepatu.promo || ""); // Optional promo
        formData.append(`sepatu[${index}][foto_sepatu]`, sepatu.foto || ""); // Optional photo
        formData.append(`sepatu[${index}][brand_sepatu]`, sepatu.jenis);
        formData.append(`sepatu[${index}][warna_sepatu]`, sepatu.warna);
    });

    // Log the FormData
    console.log("Data yang akan dikirim:");
    formData.forEach((value, key) => {
        console.log(`${key}: ${value}`);
    });

    // Sending the FormData
    axios
        .post("/master/pesanan/store", formData)
        .then(() => {
            toast.success("Data berhasil disimpan!");
            router.push("/pesanan");
        })
        .catch((error) => {
            console.error(error);
            toast.error("Terjadi kesalahan saat menyimpan data!");
        });
}


onMounted(() => {
    getTokoDetail();
    getPromoByToko();
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

                    <!-- Upload Foto -->
                    <div class="mb-3">
                        <input :id="'foto_sepatu_' + index" type="file" class="form-control"
                            @change="updateFoto($event, index)" />
                    </div>

                    <!-- Jenis Sepatu -->
                    <div class="mb-3">
                        <label class="form-label">Brand Sepatu</label>
                        <Field class="form-control form-control-lg form-control-solid" type="text" name="brand_sepatu"
                            autocomplete="off" placeholder="Masukkan Brand Sepatu" v-model="sepatu.jenis" />
                    </div>

                    <!-- Warna Sepatu -->
                    <div class="mb-3">
                        <label class="form-label">Warna Sepatu</label>
                        <select2 placeholder="Pilih Warna Sepatu" class="form-select-solid" :options="[
                            { id: 'hitam', text: 'Hitam' },
                            { id: 'putih', text: 'Putih' }
                        ]" v-model="sepatu.warna"></select2>
                    </div>

                    <!-- Layanan -->
                    <div class="mb-3">
                        <label class="form-label">Layanan</label>
                        <select2 placeholder="Pilih layanan" class="form-select-solid" :options="layanans"
                            v-model="sepatu.layanan"></select2>
                    </div>

                    <!-- Promo -->
                    <div class="mb-3">
                        <label class="form-label">Promo</label>
                        <select2 placeholder="Pilih promo" class="form-select-solid" :options="prms"
                            v-model="sepatu.promo"></select2>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                <div>
                    <h5>Total Harga: Rp {{ totalHarga }}</h5>
                </div>
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