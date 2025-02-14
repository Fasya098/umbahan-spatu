<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg">
              <div class="card-body p-5">
                <!-- Header Section -->
                <div class="text-center mb-4">
                  <div class="mb-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                      <i class="bi bi-envelope fs-1 text-primary"></i>
                    </div>
                  </div>
                  <h2 class="fw-bold mb-2">Email Verification</h2>
                  <p class="text-muted mb-1">We've sent a verification code to</p>
                  <p class="text-primary fw-medium">{{ email }}</p>
                </div>
  
                <div v-if="!isVerified">
                  <!-- OTP Input Section -->
                  <div class="mb-4">
                    <div class="d-flex justify-content-center gap-2">
                      <input
                        v-for="(digit, index) in otpDigits"
                        :key="index"
                        v-model="otpDigits[index]"
                        type="text"
                        maxlength="1"
                        class="form-control text-center fw-bold fs-4"
                        :class="{ 'is-invalid': error }"
                        style="width: 50px; height: 50px;"
                        @keyup="handleKeyup($event, index)"
                        @paste="handlePaste"
                      />
                    </div>
                    
                    <!-- Error Message -->
                    <div v-if="error" class="text-center text-danger small mt-2">
                      {{ error }}
                    </div>
                  </div>
  
                  <!-- Verify Button -->
                  <div class="d-grid gap-2 mb-3">
                    <button
                      @click="verifyOTP"
                      :disabled="loading"
                      class="btn btn-primary btn-lg"
                    >
                      <span v-if="loading" class="d-flex align-items-center justify-content-center gap-2">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Verifying...
                      </span>
                      <span v-else>Verify Code</span>
                    </button>
                  </div>
  
                  <!-- Resend Section -->
                  <div class="text-center">
                    <p class="text-muted small">
                      Didn't receive the code?
                      <button
                        @click="resendCode"
                        :disabled="countdown > 0"
                        class="btn btn-link btn-sm text-decoration-none p-0 align-baseline"
                      >
                        {{ countdown > 0 ? `Resend in ${countdown}s` : 'Resend Code' }}
                      </button>
                    </p>
                  </div>
                </div>
  
                <!-- Success State -->
                <div v-else class="text-center">
                  <div class="mb-4">
                    <div class="rounded-circle bg-success bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                      <i class="bi bi-check-lg fs-1 text-success"></i>
                    </div>
                  </div>
                  <h3 class="fw-bold mb-2">Verification Successful!</h3>
                  <p class="text-muted mb-4">Your email has been verified successfully.</p>
                  <button 
                    @click="emit('verified')"
                    class="btn btn-success"
                  >
                    Continue
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, onUnmounted } from 'vue'
  import axios from 'axios'
  
  const props = defineProps<{
    email: string
  }>()
  
  const emit = defineEmits<{
    (event: 'verified'): void
  }>()
  
  const otpDigits = ref<string[]>(Array(6).fill(''))
  const loading = ref(false)
  const error = ref('')
  const isVerified = ref(false)
  const countdown = ref(0)
  let countdownTimer: number | undefined
  
  const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000/api'
  
  const handleKeyup = (event: KeyboardEvent, index: number) => {
    const input = event.target as HTMLInputElement
    
    if (event.key === 'Backspace' && !input.value && index > 0) {
      otpDigits.value[index - 1] = ''
      const prevInput = document.querySelector<HTMLInputElement>(`input:nth-child(${index})`)
      prevInput?.focus()
    } else if (input.value && index < otpDigits.value.length - 1) {
      const nextInput = document.querySelector<HTMLInputElement>(`input:nth-child(${index + 2})`)
      nextInput?.focus()
    }
  }
  
  const handlePaste = (event: ClipboardEvent) => {
    event.preventDefault()
    const pastedData = event.clipboardData?.getData('text')
    if (!pastedData) return
  
    const digits = pastedData.slice(0, 6).split('')
    otpDigits.value = [...digits, ...Array(6 - digits.length).fill('')]
  }
  
  const startCountdown = () => {
    countdown.value = 60
    countdownTimer = window.setInterval(() => {
      if (countdown.value > 0) {
        countdown.value--
      } else {
        clearInterval(countdownTimer)
      }
    }, 1000)
  }
  
  const resendCode = async () => {
    try {
      loading.value = true
      error.value = ''
      
      await axios.post(`${API_BASE_URL}/auth/resend-otp`, {
        email: props.email
      })
      
      startCountdown()
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to resend verification code'
    } finally {
      loading.value = false
    }
  }
  
  const verifyOTP = async () => {
    try {
      loading.value = true
      error.value = ''
      
      const otp = otpDigits.value.join('')
      if (otp.length !== 6) {
        error.value = 'Please enter all digits'
        return
      }
  
      await axios.post(`${API_BASE_URL}/auth/verify-otp`, {
        email: props.email,
        otp
      })
  
      isVerified.value = true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Invalid verification code'
      otpDigits.value = Array(6).fill('')
    } finally {
      loading.value = false
    }
  }
  
  onMounted(() => {
    startCountdown()
  })
  
  onUnmounted(() => {
    if (countdownTimer) {
      clearInterval(countdownTimer)
    }
  })
  </script>