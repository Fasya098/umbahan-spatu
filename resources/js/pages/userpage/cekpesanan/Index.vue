<script setup lang="ts">
import { ref } from "vue";
import axios from "axios";

interface OrderDetail {
    kode_pesanan: string;
    nama_produk: string;
    jumlah: number;
    harga: number;
    status: string;
    tanggal_pesanan: string;
    alamat_pengiriman: string;
}

const kodePesanan = ref("");
const orderDetails = ref<OrderDetail[]>([]);
const loading = ref(false);
const errorMessage = ref("");

const cekPesanan = async () => {
    if (!kodePesanan.value) {
        errorMessage.value = "Silakan masukkan kode pesanan";
        return;
    }

    loading.value = true;
    errorMessage.value = "";

    try {
        const response = await axios.get(`/master/pesanan/cek/${kodePesanan.value}`);
        orderDetails.value = response.data; // Menyimpan array hasil dari backend
    } catch (error) {
        if (axios.isAxiosError(error)) {
            errorMessage.value = error.response?.data?.message || "Terjadi kesalahan saat mengambil data pesanan";
        } else {
            errorMessage.value = "Terjadi kesalahan yang tidak diketahui";
        }
        orderDetails.value = [];
    } finally {
        loading.value = false;
    }
};

</script>

<template>
    <div class="background">
        <div class="container">
            <div class="card shadow-sm p-4">
                <h2 class="mb-3 text-center">Cek Status Pesanan</h2>
                <p class="text-center">Masukkan kode pesanan yang telah dikirim ke email Anda.</p>

                <div class="mb-3">
                    <input v-model="kodePesanan" type="text" class="form-control" placeholder="Masukkan Kode Pesanan"
                        @keyup.enter="cekPesanan" />
                </div>

                <button class="btn btn-primary w-100" @click="cekPesanan" :disabled="loading">
                    {{ loading ? 'Memeriksa...' : 'Cek Pesanan' }}
                </button>

                <div v-if="errorMessage" class="alert alert-danger mt-3">
                    {{ errorMessage }}
                </div>

                <!-- Tabel Detail Pesanan -->
                <div v-if="orderDetails.length" class="mt-4">
                    <h4 class="mb-3">Detail Pesanan</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Pesanan</th>
                                    <th>Brand Sepatu</th>
                                    <th>Warna Sepatu</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal Pesanan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(pesanan, index) in orderDetails" :key="pesanan.kode_pesanan">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ pesanan.kode_pesanan }}</td>
                                    <td>{{ pesanan.brand_sepatu }}</td>
                                    <td>{{ pesanan.warna_sepatu }}</td>
                                    <td>Rp {{ pesanan.total_harga.toLocaleString('id-ID') }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-warning': parseInt(pesanan.status) === 1, // Penjemputan
                                            'bg-primary': parseInt(pesanan.status) === 2,    // Proses Pengerjaan
                                            'bg-success': parseInt(pesanan.status) === 3  // Pengiriman
                                        }">
                                            {{
                                                parseInt(pesanan.status) === 1 ? 'Pesanan dalam proses penjemputan' :
                                                    parseInt(pesanan.status) === 2 ? 'Pesanan sedang diproses' :
                                            parseInt(pesanan.status) === 3 ? 'Pesanan dalam proses pengiriman' :
                                            'Status tidak diketahui'
                                            }}
                                        </span>
                                    </td>

                                    <td>{{ new Date(pesanan.tanggal_pesanan).toLocaleDateString('id-ID') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    margin-bottom: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 500px;
    min-height: 100vh;
    padding: 20px;
}

.background {
    background-image: url('/media/misc/bg-sepatu.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    width: 100vw;
    display: flex;
    justify-content: center;
    align-items: center;
}

.table {
    background-color: white;
}

.badge {
    padding: 0.5em 1em;
}

.img-thumbnail {
    border-radius: 5px;
    object-fit: cover;
}
</style>