<template>
  <div class="container my-4">
    <header class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Dashboard Admin - Laundry Sepatu</h1>
      <!-- <button @click="logout" class="btn btn-danger">Logout</button> -->
    </header>

    <section class="row mb-4">
      <div class="col-md-4">
        <div class="card text-center bg-primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Pesanan</h5>
            <p class="card-text display-4">{{ totalOrders }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center bg-warning text-dark">
          <div class="card-body">
            <h5 class="card-title">Pesanan Diproses</h5>
            <p class="card-text display-4">{{ processingOrders }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Pesanan Selesai</h5>
            <p class="card-text display-4">{{ completedOrders }}</p>
          </div>
        </div>
      </div>
    </section>

    <section>
      <h2 class="h4 mb-3">Daftar Pesanan</h2>
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Nama Customer</th>
            <th>Jumlah Sepatu</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(order, index) in orders" :key="order.id">
            <td>{{ index + 1 }}</td>
            <td>{{ order.customerName }}</td>
            <td>{{ order.shoeCount }}</td>
            <td>{{ order.status }}</td>
            <td>
              <button @click="updateOrderStatus(order.id, 'diproses')" class="btn btn-primary btn-sm">Proses</button>
              <button @click="updateOrderStatus(order.id, 'selesai')" class="btn btn-success btn-sm">Selesaikan</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// State untuk data
const totalOrders = ref(0);
const processingOrders = ref(0);
const completedOrders = ref(0);
const orders = ref([]);

// Fungsi fetch data dari backend
const fetchDashboardData = async () => {
  try {
    // Simulasi fetch data
    const response = await fetch('/api/admin/dashboard'); // Ganti dengan endpoint Anda
    const data = await response.json();
    totalOrders.value = data.totalOrders;
    processingOrders.value = data.processingOrders;
    completedOrders.value = data.completedOrders;
    orders.value = data.orders;
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
  }
};

// Fungsi update status pesanan
const updateOrderStatus = async (orderId, status) => {
  try {
    await fetch(`/api/admin/orders/${orderId}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ status }),
    });
    fetchDashboardData(); // Refresh data setelah update
  } catch (error) {
    console.error('Error updating order status:', error);
  }
};

// Fungsi logout
const logout = () => {
  // Logika logout
  console.log('Logout');
};

// Fetch data saat komponen dimuat
onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.card {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.table {
  border: 1px solid #dee2e6;
}
</style>
