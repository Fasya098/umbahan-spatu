<template>
  <div class="shoe-cleaning-website">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="nav">
        <div class="navbar-container">
          <router-link to="/" class="nav-item">Home</router-link>
          <router-link to="/maps" class="nav-item">Maps</router-link>
        </div>
        <div class="search-join-container">
          <div class="search-location-wrapper">
            <input type="text" v-model="searchQuery" @input="filterStores" placeholder="Cari nama toko..."
              class="search-input" />
            <Button v-if="!userLocation" class="btn btn-location btn-sm" @click="getUserLocation">
              <span><i style="color: red" class="fa-solid fa-location-dot"></i></span>
              Gunakan Lokasi Saya
            </Button>
          </div>
          <Button class="btn btn-info btn-sm" @click="navigateToMitra">
            Bergabung dengan kami
          </Button>
        </div>
      </div>
    </nav>

    <div v-if="userLocation" class="location-info">
      <p>Menampilkan toko terdekat dari lokasi Anda</p>
    </div>

    <!-- Content -->
    <main class="content">
      <div class="card-container">
        <div v-if="filteredTokos.length" class="cards-wrapper">
          <div v-for="toko in filteredTokos" :key="toko.uuid" class="shoe-card">
            <img :src="getImageUrl(toko.foto_toko)" alt="Sepatu" class="shoe-image">
            <div class="shoe-info">
              <h3>{{ toko.nama_toko }}</h3>
              <p>Alamat: {{ toko.alamat }}</p>
              <div class="card-footer">
                <p v-if="userLocation">Jarak: {{ toko.jarak.toFixed(2) }} km</p>
                <button class="btn btn-info btn-sm" @click="navigateToStore(toko.uuid)">Lihat Toko</button>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="loading-text">{{ tokos.length === 0 ? 'Loading data...' : 'Tidak ada toko ditemukan' }}</p>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="company-info">
          <img src="/media/logo-spatu-nobackground.png" alt="Logo Perusahaan" class="company-logo">
          <h2 class="text-white">Cuci Sepatu Bersih</h2>
          <p>Telp: (021) 1234-5678</p>
          <p>Email: info@cucisepatubersih.com</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const tokos = ref([]);
const searchQuery = ref('');
const userLocation = ref<{ lat: number; lng: number } | null>(null);

const filteredTokos = computed(() => {
  if (!searchQuery.value) return tokos.value;
  const query = searchQuery.value.toLowerCase();
  return tokos.value.filter(toko =>
    toko.nama_toko.toLowerCase().includes(query) ||
    toko.alamat.toLowerCase().includes(query)
  );
});

const getUserLocation = () => {
  if (!navigator.geolocation) {
    alert("Geolocation tidak didukung oleh browser ini.");
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      userLocation.value = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      calculateDistances();
    },
    (error) => {
      alert("Gagal mendapatkan lokasi: " + error.message);
    }
  );
};

const calculateDistance = (lat1: number, lng1: number, lat2: number, lng2: number): number => {
  const R = 6371; // Radius bumi dalam km
  const dLat = (lat2 - lat1) * (Math.PI / 180);
  const dLng = (lng2 - lng1) * (Math.PI / 180);

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
    Math.sin(dLng / 2) * Math.sin(dLng / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
};

const calculateDistances = () => {
  if (!userLocation.value) return;

  tokos.value.forEach(toko => {
    if (toko.latitude && toko.longitude) {
      toko.jarak = calculateDistance(userLocation.value.lat, userLocation.value.lng, toko.latitude, toko.longitude);
    } else {
      toko.jarak = 0;
    }
  });

  tokos.value.sort((a, b) => a.jarak - b.jarak);
};

const navigateToMitra = () => {
  router.push('/userpage/mitra');
};

const getShoesData = async () => {
  try {
    const response = await axios.get('/userpage/toko/get');
    console.log(response.data);
    tokos.value = response.data;
  } catch (error) {
    console.error('Error fetching shoes data:', error);
  }
};

const getImageUrl = (path: string) => {
  return path ? `/storage/${path}` : '/placeholder-shoe.jpg';
};

function navigateToStore(uuid) {
  router.push({
    name: 'userpage.store',
    params: { uuid }
  });
};

onMounted(() => {
  getShoesData();
});
</script>

<style scoped>
.nav {
  display: flex;
  flex-direction: row;
  width: 100%;
  justify-content: space-between;
  /* background-color: red; */
}

.shoe-cleaning-website {
  font-family: Arial, sans-serif;
}

.navbar {
  background-color: #333;
  padding: 2rem;
  width: 100%;
}

.navbar-container {
  display: flex;
  flex-direction: row;
  justify-content: center;
}

.nav-item {
  color: white;
  text-decoration: none;
  margin: 0 1rem;
}

.search-location-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.search-join-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: space-between;
    margin: 0 auto;
  }

.search-input {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.95rem;
  width: 200px;
  background-color: white;
}

.search-input:focus {
  outline: none;
  border-color: #17a2b8;
  box-shadow: 0 0 0 2px rgba(23, 162, 184, 0.2);
}

.btn-location {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: none;
  background-color: #4CAF50;
  color: white;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.95rem;
}

.btn-location:hover {
  background-color: #45a049;
  transform: translateY(-1px);
}

.location-info {
  /* background-color: #e8f5e9; */
  color: #2e7d32;
  text-align: center;
  /* padding: 0.5rem; */
  /* margin-bottom: 1rem; */
}

.content {
  padding: 2rem;
  min-height: 50vh;
}

.card-container {
  max-width: 1450px;
}

.cards-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.shoe-card {
  flex: 0 1 189px;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.shoe-card:hover {
  transform: translateY(-5px);
}

.shoe-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.shoe-info {
  padding: 1.25rem;
}

.shoe-info h3 {
  color: #333;
  font-size: 1.25rem;
  margin: 0 0 0.5rem 0;
}

.shoe-info p {
  color: #666;
  font-size: 0.95rem;
  margin: 0.5rem 0;
}

.card-footer {
  margin-top: 1rem;
}

.btn.btn-info {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  border: none;
  background-color: #17a2b8;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn.btn-info:hover {
  background-color: #138496;
}

.loading-text {
  text-align: center;
  font-size: 1.1rem;
  color: #666;
  padding: 2rem;
}

.footer {
  background-color: #333;
  color: white;
  padding: 2rem;
  margin-top: 3rem;
}

.footer-content {
  display: flex;
  justify-content: center;
}

.company-info {
  text-align: center;
}

.company-logo {
  width: 85px;
  height: auto;
  margin-bottom: 2rem;
}

@media (max-width: 768px) {
  .shoe-card {
    flex: 0 1 100%;
    max-width: 400px;
  }

  .card-container {
    padding: 0 0.5rem;
  }

  .cards-wrapper {
    gap: 1rem;
  }

  .search-location-wrapper {
    flex-direction: column;
    width: 100%;
  }

  .search-join-container {
    flex-direction: column;
    gap: 1rem;
  }

  .btn-location {
    width: 100%;
    justify-content: center;
  }

  .search-input {
    width: 100%;
  }
}
</style>