<template>
  <div class="shoe-cleaning-website">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <router-link to="/" class="nav-item">Home</router-link>
        <router-link to="/maps" class="nav-item">Maps</router-link>
      </div>
      <div style="justify-content: flex-end;">
        <Button @click="navigateToMitra" style="justify-content: flex-end">
          Bergabung dengan kami
        </Button>
      </div>
    </nav>

    <!-- Content -->
    <main class="content">
      <div class="card-container">
        <!-- Cek apakah data sudah ada -->
        <div v-if="shoes.length" v-for="shoe in shoes" :key="shoe.id" class="shoe-card">
          <!-- Gabungkan base URL dengan path foto_toko -->
          <img :src="getImageUrl(shoe.foto_toko)" alt="Sepatu" class="shoe-image">
          <div class="shoe-info">
            <h3>{{ shoe.nama_toko }}</h3>
            <p>Jarak: 5,1km</p>
          </div>
        </div>
        <!-- Tampilkan pesan jika data belum ada -->
        <p v-else>Loading shoes data...</p>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="company-info">
          <img src="/media/logo-spatu-nobackground.png" alt="Logo Perusahaan" class="company-logo">
          <h2 class="text-white">Cuci Sepatu Bersih</h2>
          <p>Jl. Contoh No. 123, Kota Anda</p>
          <p>Telp: (021) 1234-5678</p>
          <p>Email: info@cucisepatubersih.com</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToMitra = () => {
  router.push('/userpage/mitra');
};

// State untuk menampung data sepatu
const shoes = ref([]);

// Fungsi untuk mendapatkan data sepatu dari API
const getShoesData = async () => {
  try {
    const response = await axios.get('/userpage/toko/get');
    console.log(response.data); // Cek data di sini
    shoes.value = response.data;
  } catch (error) {
    console.error('Error fetching shoes data:', error);
  }
};

// Fungsi untuk menggabungkan URL base dan path gambar
const getImageUrl = (path: string) => {
  return path ? `/storage/${path}` : '/placeholder-shoe.jpg';
};

// Panggil API saat komponen di-mount
onMounted(() => {
  getShoesData();
});
</script>

<style scoped>
.shoe-cleaning-website {
  font-family: Arial, sans-serif;
}

.navbar {
  background-color: #333;
  padding: 2rem;
}

.navbar-container {
  display: flex;
  justify-content: center;
}

.nav-item {
  color: white;
  text-decoration: none;
  margin: 0 1rem;
}

.content {
  padding: 2rem;
}

.card-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  /* Fleksibel dengan min-width untuk laptop */
  gap: 1.5rem;
  /* Jarak antar card */
}

.shoe-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 1.5rem;
  max-width: 220px;
  /* Max width agar berbentuk persegi panjang */
  max-height: 320px;
  /* Max height agar berbentuk persegi panjang ke bawah */
}

.shoe-image {
  width: 100%;
  height: 250px;
  /* Sesuaikan tinggi gambar */
  object-fit: cover;
}

.shoe-info {
  padding: 1rem;
  /* Padding tetap sesuai */
}

.shoe-info h3 {
  color: #333;
  /* Warna teks yang jelas */
  font-size: 1.25rem;
}

.shoe-info p {
  color: #555;
  /* Warna untuk teks harga */
  font-size: 1rem;
}

.footer {
  background-color: #333;
  color: white;
  padding: 2rem;
}

.footer-content {
  display: flex;
  justify-content: center;
}

.company-info {
  text-align: center;
}

.company-logo {
  width: 100px;
  height: auto;
  margin-bottom: 1rem;
}
</style>
