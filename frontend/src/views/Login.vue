<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="elevation-8">
          <v-card-title class="justify-center">
            <h2 class="display-1">Login</h2>
          </v-card-title>

          <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="handleLogin">
              <v-text-field
                v-model="email"
                :rules="emailRules"
                label="Email"
                type="email"
                prepend-inner-icon="mdi-email"
                outlined
                required
                data-testid="email-input"
                @keyup.enter="handleLogin"
              />

              <v-text-field
                v-model="password"
                :rules="passwordRules"
                :type="showPassword ? 'text' : 'password'"
                label="Password"
                prepend-inner-icon="mdi-lock"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                outlined
                required
                data-testid="password-input"
                @click:append="showPassword = !showPassword"
                @keyup.enter="handleLogin"
              />

              <v-alert
                v-if="error"
                type="error"
                dense
                class="mb-4"
                data-testid="error-alert"
              >
                {{ error }}
              </v-alert>

              <v-btn
                type="submit"
                color="primary"
                large
                block
                :loading="loading"
                :disabled="!valid"
                data-testid="login-button"
                @click="handleLogin"
              >
                Login
              </v-btn>
            </v-form>

            <div class="text-center mt-4">
              <p class="subtitle-2">
                Don't have an account?
                <router-link 
                  to="/register" 
                  class="primary--text text-decoration-none"
                  data-testid="register-link"
                >
                  Sign up here
                </router-link>
              </p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Login',
  data () {
    return {
      valid: false,
      email: '',
      password: '',
      showPassword: false,
      loading: false,
      error: null,
      emailRules: [
        v => !!v || 'Email is required',
        v => /.+@.+/.test(v) || 'E-mail must be valid'
      ],
      passwordRules: [
        v => !!v || 'Password is required',
        v => v.length >= 6 || 'Password must be at least 6 characters'
      ]
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  methods: {
    ...mapActions(['login']),
    
    async handleLogin () {
      if (!this.$refs.form.validate()) {
        return
      }

      this.loading = true
      this.error = null

      try {
        const result = await this.login({
          email: this.email,
          password: this.password
        })

        if (result.success) {
          if (this.$route.path !== '/') {
            this.$router.push('/')
          }
        } else {
          this.error = result.message || 'Login failed'
        }
      } catch (error) {
        console.error('Login error:', error)
        this.error = 'An unexpected error occurred'
      } finally {
        this.loading = false
      }
    },

    fillTestCredentials () {
      this.email = 'test@example.com'
      this.password = 'password'
    }
  }
}
</script>

<style scoped>
.v-card {
  padding: 20px;
}
</style>
