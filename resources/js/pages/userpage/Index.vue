<template>
  <div class="shoe-cleaning-website">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <router-link to="/" class="nav-item">Home</router-link>
        <router-link to="/maps" class="nav-item">Maps</router-link>
      </div>
    </nav>

    <!-- Content -->
    <main class="content">
      <div class="card-container">
        <div v-for="i in 12" :key="i" class="shoe-card">
          <img :src="`/placeholder-shoe-${i}.jpg`" alt="Sepatu" class="shoe-image">
          <div class="shoe-info">
            <h3>Sepatu {{ i }}</h3>
            <p>Rp {{ (Math.random() * 100000 + 50000).toFixed(0) }}</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <div class="company-info">
          <img src="/logo.png" alt="Logo Perusahaan" class="company-logo">
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
// Tambahkan logika komponen di sini jika diperlukan
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
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.shoe-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
}

.shoe-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.shoe-info {
  padding: 1rem;
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