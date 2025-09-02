<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="display-1 text-center">Profile</h1>
      </v-col>
    </v-row>

    <v-row v-if="!user">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-account-alert</v-icon>
        <h3 class="headline grey--text mt-4">Please log in to view your profile</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/login')">
          Login
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>
            <v-icon left>mdi-account</v-icon>
            Personal Information
          </v-card-title>
          
          <v-card-text>
            <v-form ref="form" v-model="valid">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="firstName"
                    label="First Name"
                    outlined
                    data-testid="first-name-input"
                    :readonly="!editing"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="lastName"
                    label="Last Name"
                    outlined
                    data-testid="last-name-input"
                    :readonly="!editing"
                  />
                </v-col>
              </v-row>
              
              <v-text-field
                v-model="email"
                label="Email"
                type="email"
                outlined
                data-testid="email-input"
                :readonly="!editing"
              />
              
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    label="Member Since"
                    :value="formatDate(user.created_at)"
                    outlined
                    readonly
                    data-testid="member-since"
                  />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn
              v-if="!editing"
              color="primary"
              @click="startEditing"
              data-testid="edit-button"
            >
              Edit Profile
            </v-btn>
            <template v-else>
              <v-btn
                text
                @click="cancelEditing"
                data-testid="cancel-button"
              >
                Cancel
              </v-btn>
              <v-btn
                color="primary"
                :loading="saving"
                @click="saveProfile"
                data-testid="save-button"
              >
                Save Changes
              </v-btn>
            </template>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="mb-4">
          <v-card-title>
            <v-icon left>mdi-chart-line</v-icon>
            Quick Stats
          </v-card-title>
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Total Orders:</span>
              <span class="font-weight-bold">{{ orders.length }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span>Cart Items:</span>
              <span class="font-weight-bold">{{ cartItemCount }}</span>
            </div>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title>
            <v-icon left>mdi-cog</v-icon>
            Account Actions
          </v-card-title>
          <v-card-text>
            <v-btn
              block
              color="primary"
              outlined
              class="mb-2"
              @click="$router.push('/orders')"
            >
              View Order History
            </v-btn>
            <v-btn
              block
              color="error"
              outlined
              @click="confirmLogout"
              data-testid="logout-button"
            >
              Logout
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Success/Error Messages -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      {{ snackbar.message }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar.show = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>

    <!-- Logout Confirmation Dialog -->
    <v-dialog v-model="logoutDialog" max-width="400">
      <v-card>
        <v-card-title>Confirm Logout</v-card-title>
        <v-card-text>
          Are you sure you want to logout?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="logoutDialog = false">Cancel</v-btn>
          <v-btn color="error" @click="handleLogout">Logout</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Profile',
  data () {
    return {
      valid: false,
      editing: false,
      saving: false,
      firstName: '',
      lastName: '',
      email: '',
      logoutDialog: false,
      snackbar: {
        show: false,
        message: '',
        color: 'success'
      }
    }
  },
  computed: {
    ...mapGetters(['user', 'orders', 'cartItemCount'])
  },
  mounted () {
    if (!this.user) {
      this.$router.push('/login')
      return
    }
    
    this.loadUserData()
  },
  methods: {
    ...mapActions(['logout', 'loadOrders']),
    
    loadUserData () {
      if (this.user) {
        this.firstName = this.user.first_name || ''
        this.lastName = this.user.last_name || ''
        this.email = this.user.email || ''
      }
    },
    
    startEditing () {
      this.editing = true
    },
    
    cancelEditing () {
      this.editing = false
      this.loadUserData() // Reset to original values
    },
    
    async saveProfile () {
      this.saving = true
      
      try {
        const response = await this.$http.put(`/api/users/${this.user.id}`, {
          first_name: this.firstName,
          last_name: this.lastName,
          email: this.email
        })
        
        if (response.data.success) {
          this.editing = false
          this.showMessage('Profile updated successfully!', 'success')
        } else {
          this.showMessage(response.data.message || 'Failed to update profile', 'error')
        }
      } catch (error) {
        console.error('Profile update error:', error)
        this.showMessage('An error occurred while updating profile', 'error')
      } finally {
        this.saving = false
      }
    },
    
    confirmLogout () {
      this.logoutDialog = true
    },
    
    handleLogout () {
      this.logout()
      this.logoutDialog = false
      this.$router.push('/')
    },
    
    formatDate (dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    
    showMessage (message, color = 'success') {
      this.snackbar = {
        show: true,
        message,
        color
      }
    }
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
