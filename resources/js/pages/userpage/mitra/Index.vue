<template>
  <div>
    <div class="background" id="base">
      <div class="container mt-5">
        <div class="card shadow">
          <div class="text-center mt-10">
            <h3>Form Pendaftaran Mitra</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" v-model="form.name"
                  placeholder="Masukkan Nama Lengkap" required />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" v-model="form.email" placeholder="Masukkan email"
                  required />
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">No.Telepon</label>
                <input type="tel" class="form-control" id="phone" v-model="form.phone" placeholder="Masukkan No Telepon"
                  required />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" v-model="form.password"
                  placeholder="Masukkan Password" required />
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Password Konfirmasi</label>
                <input type="password" class="form-control" id="password_confirmation" v-model="form.password_confirmation"
                  placeholder="Masukkan Password Konfirmasi" required />
              </div>

              <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import axios from "@/libs/axios";
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: ''
})

const submitForm = async () => {
  try {
    const response = await axios.post('/userpage/store', form);
    console.log(response.data);
    
    // Menggunakan SweetAlert2 untuk menampilkan alert sukses
    const {isConfirmed} = await Swal.fire({
      icon: 'sukses',
      title: 'Pendaftaran sedang ditinjau!',
      text: 'Tunggu wa masuk pada hp anda',
      confirmButtonText: 'Ok',
    });

    if (isConfirmed) {
      router.push('/userpage');
    }

    // Kamu bisa menambahkan logika untuk melakukan tindakan setelah sukses (misalnya redirect)
  } catch (error) {
    console.error(error);
    
    // Menggunakan SweetAlert2 untuk menampilkan alert kesalahan
    await Swal.fire({
      icon: 'error',
      title: 'Terjadi kesalahan!',
      text: 'Silakan coba lagi.',
      confirmButtonText: 'Ok'
    });
  }
}

</script>

<style scoped>
.container {
  max-width: 600px;
}

.background {
  background-image: url('/media/misc/bg-sepatu.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 100vh;
  width: 100vw;
}
#base {
  display : flex;
  align-items: center;
  justify-content: center;
}
.card {
  border-radius: 10px;
}

.btn-primary {
  background-color: #4a90e2;
  border-color: #4a90e2;
}

.card-header {
  background-color: #f7f7f7;
  border-bottom: none;
}

.form-control {
  border-radius: 8px;
}


</style>