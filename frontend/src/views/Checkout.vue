<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="display-1 text-center">Checkout</h1>
      </v-col>
    </v-row>

    <v-row v-if="!user">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-account-alert</v-icon>
        <h3 class="headline grey--text mt-4">Please log in to checkout</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/login')">
          Login
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else-if="cart.items.length === 0">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-cart-outline</v-icon>
        <h3 class="headline grey--text mt-4">Your cart is empty</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/products')">
          Shop Now
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col cols="12" md="8">
        <v-card class="mb-4">
          <v-card-title>Shipping Information</v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid">
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="shippingAddress.firstName"
                    label="First Name"
                    required
                    outlined
                    data-testid="first-name-input"
                  />
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="shippingAddress.lastName"
                    label="Last Name"
                    required
                    outlined
                    data-testid="last-name-input"
                  />
                </v-col>
              </v-row>
              
              <v-text-field
                v-model="shippingAddress.address"
                label="Street Address"
                required
                outlined
                data-testid="address-input"
              />
              
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="shippingAddress.city"
                    label="City"
                    required
                    outlined
                    data-testid="city-input"
                  />
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="shippingAddress.state"
                    label="State"
                    required
                    outlined
                    data-testid="state-input"
                  />
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="shippingAddress.zipCode"
                    label="ZIP Code"
                    required
                    outlined
                    data-testid="zip-input"
                  />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Order Items Review -->
        <v-card>
          <v-card-title>Order Review</v-card-title>
          <v-card-text>
            <div v-for="item in cart.items" :key="item.product_id" class="d-flex align-center mb-3">
              <div class="flex-grow-1">
                <div class="subtitle-1">{{ item.name }}</div>
                <div class="body-2 grey--text">Qty: {{ item.quantity }} × ${{ formatPrice(item.price) }}</div>
              </div>
              <div class="subtitle-1 font-weight-bold">
                ${{ formatPrice(item.price * item.quantity) }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Order Summary -->
      <v-col cols="12" md="4">
        <v-card sticky>
          <v-card-title>Order Summary</v-card-title>
          
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span>${{ formatPrice(cart.totals.subtotal) }}</span>
            </div>
            
            <div class="d-flex justify-space-between mb-2">
              <span>Tax (8%):</span>
              <span>${{ formatPrice(cart.totals.tax) }}</span>
            </div>
            
            <div class="d-flex justify-space-between mb-4">
              <span>Shipping:</span>
              <span>FREE</span>
            </div>
            
            <v-divider class="my-3" />
            
            <div class="d-flex justify-space-between mb-4">
              <span class="font-weight-bold">Total:</span>
              <span class="font-weight-bold text-h6 primary--text">
                ${{ formatPrice(cart.totals.total) }}
              </span>
            </div>

            <v-btn
              color="primary"
              large
              block
              :loading="placing"
              :disabled="!isFormValid"
              @click="placeOrder"
              data-testid="place-order-button"
            >
              Place Order
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Success/Error Messages -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="5000">
      {{ snackbar.message }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar.show = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Checkout',
  data () {
    return {
      valid: false,
      placing: false,
      shippingAddress: {
        firstName: '',
        lastName: '',
        address: '',
        city: '',
        state: '',
        zipCode: ''
      },
      snackbar: {
        show: false,
        message: '',
        color: 'success'
      }
    }
  },
  computed: {
    ...mapGetters(['user', 'cart']),
    isFormValid () {
      return this.shippingAddress.firstName &&
             this.shippingAddress.lastName &&
             this.shippingAddress.address &&
             this.shippingAddress.city &&
             this.shippingAddress.state &&
             this.shippingAddress.zipCode
    },
    formattedAddress () {
      const addr = this.shippingAddress
      return `${addr.firstName} ${addr.lastName}, ${addr.address}, ${addr.city}, ${addr.state} ${addr.zipCode}`
    }
  },
  async mounted () {
    if (!this.user) {
      this.$router.push('/login')
      return
    }
    
    if (this.cart.items.length === 0) {
      this.$router.push('/cart')
    }
  },
  methods: {
    formatPrice (price) {
      return parseFloat(price).toFixed(2)
    },
    
    async placeOrder () {
      if (!this.isFormValid) return
      
      this.placing = true
      
      try {
        const response = await this.$http.post('/api/orders', {
          user_id: this.user.id,
          shipping_address: this.formattedAddress
        })
        
        if (response.data.success) {
          this.showMessage('Order placed successfully!', 'success')
          setTimeout(() => {
            this.$router.push('/orders')
          }, 2000)
        } else {
          this.showMessage(response.data.message || 'Failed to place order', 'error')
        }
      } catch (error) {
        console.error('Order placement error:', error)
        this.showMessage('An error occurred while placing your order', 'error')
      } finally {
        this.placing = false
      }
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
.v-card.sticky {
  position: sticky;
  top: 100px;
}
</style>
