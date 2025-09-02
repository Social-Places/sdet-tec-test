<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="elevation-8">
          <v-card-title class="justify-center">
            <h2 class="display-1">Create Account</h2>
          </v-card-title>

          <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="handleRegister">
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="firstName"
                    :rules="nameRules"
                    label="First Name"
                    prepend-inner-icon="mdi-account"
                    outlined
                    required
                    data-testid="first-name-input"
                  />
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="lastName"
                    :rules="nameRules"
                    label="Last Name"
                    outlined
                    required
                    data-testid="last-name-input"
                  />
                </v-col>
              </v-row>

              <v-text-field
                v-model="email"
                :rules="emailRules"
                label="Email"
                type="email"
                prepend-inner-icon="mdi-email"
                outlined
                required
                data-testid="email-input"
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
              />

              <v-text-field
                v-model="confirmPassword"
                :rules="confirmPasswordRules"
                :type="showConfirmPassword ? 'text' : 'password'"
                label="Confirm Password"
                prepend-inner-icon="mdi-lock-check"
                :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                outlined
                required
                data-testid="confirm-password-input"
                @click:append="showConfirmPassword = !showConfirmPassword"
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

              <v-alert
                v-if="success"
                type="success"
                dense
                class="mb-4"
                data-testid="success-alert"
              >
                {{ success }}
              </v-alert>

              <v-btn
                type="submit"
                color="primary"
                large
                block
                :loading="loading"
                :disabled="!valid"
                data-testid="register-button"
                @click="handleRegister"
              >
                Create Account
              </v-btn>
            </v-form>

            <div class="text-center mt-4">
              <p class="subtitle-2">
                Already have an account?
                <router-link 
                  to="/login" 
                  class="primary--text text-decoration-none"
                  data-testid="login-link"
                >
                  Sign in here
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
import { mapActions } from 'vuex'

export default {
  name: 'Register',
  data () {
    return {
      valid: false,
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmPassword: '',
      showPassword: false,
      showConfirmPassword: false,
      loading: false,
      error: null,
      success: null,
      nameRules: [
        v => !!v || 'Name is required',
        v => v.length >= 2 || 'Name must be at least 2 characters'
      ],
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
    confirmPasswordRules () {
      return [
        v => !!v || 'Please confirm your password',
        v => v === this.password || 'Passwords do not match'
      ]
    }
  },
  methods: {
    ...mapActions(['register']),
    
    async handleRegister () {
      if (!this.$refs.form.validate()) {
        return
      }

      this.loading = true
      this.error = null
      this.success = null

      try {
        const result = await this.register({
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email,
          password: this.password
        })

        if (result.success) {
          this.success = 'Account created successfully! You can now log in.'
          setTimeout(() => {
            this.$router.push('/login')
          }, 2000)
        } else {
          this.error = result.message || 'Registration failed'
        }
      } catch (error) {
        console.error('Registration error:', error)
        this.error = 'An unexpected error occurred'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.v-card {
  padding: 20px;
}
</style>
