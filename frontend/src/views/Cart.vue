<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="display-1 text-center">Shopping Cart</h1>
      </v-col>
    </v-row>

    <!-- Empty Cart State -->
    <v-row v-if="!user">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-account-alert</v-icon>
        <h3 class="headline grey--text mt-4">Please log in to view your cart</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/login')">
          Login
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else-if="cart.items.length === 0">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-cart-outline</v-icon>
        <h3 class="headline grey--text mt-4">Your cart is empty</h3>
        <p class="subtitle-1 grey--text">
          Browse our products and add some items to your cart
        </p>
        <v-btn color="primary" class="mt-4" @click="$router.push('/products')">
          Shop Now
        </v-btn>
      </v-col>
    </v-row>

    <!-- Cart Items -->
    <v-row v-else>
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title>
            Cart Items ({{ cart.items.length }})
          </v-card-title>
          
          <v-divider />
          
          <div v-for="item in cart.items" :key="item.product_id" class="cart-item">
            <v-card-text class="d-flex align-center">
              <v-img
                :src="item.image_url"
                :alt="item.name"
                width="80"
                height="80"
                class="mr-4"
              />
              
              <div class="flex-grow-1">
                <h4 class="subtitle-1 mb-1">{{ item.name }}</h4>
                <p class="body-2 grey--text mb-2">${{ formatPrice(item.price) }}</p>
                
                <div class="d-flex align-center">
                  <v-btn
                    small
                    icon
                    @click="updateQuantity(item.product_id, item.quantity - 1)"
                    :disabled="updating"
                    data-testid="decrease-quantity"
                  >
                    <v-icon>mdi-minus</v-icon>
                  </v-btn>
                  
                  <span class="mx-3 body-1" data-testid="item-quantity">
                    {{ item.quantity }}
                  </span>
                  
                  <v-btn
                    small
                    icon
                    @click="updateQuantity(item.product_id, item.quantity + 1)"
                    :disabled="updating || item.quantity >= item.stock_quantity"
                    data-testid="increase-quantity"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                  
                  <v-spacer />
                  
                  <div class="text-right">
                    <div class="subtitle-1 font-weight-bold">
                      ${{ formatPrice(item.price * item.quantity) }}
                    </div>
                    <v-btn
                      small
                      text
                      color="error"
                      @click="removeItem(item.product_id)"
                      :loading="updating"
                      data-testid="remove-item"
                    >
                      Remove
                    </v-btn>
                  </div>
                </div>
              </div>
            </v-card-text>
            <v-divider />
          </div>
        </v-card>
      </v-col>

      <!-- Order Summary -->
      <v-col cols="12" md="4">
        <v-card sticky>
          <v-card-title>Order Summary</v-card-title>
          
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span data-testid="subtotal">${{ formatPrice(cart.totals.subtotal) }}</span>
            </div>
            
            <div class="d-flex justify-space-between mb-2">
              <span>Tax (8%):</span>
              <span data-testid="tax">${{ formatPrice(cart.totals.tax) }}</span>
            </div>
            
            <v-divider class="my-3" />
            
            <div class="d-flex justify-space-between mb-4">
              <span class="font-weight-bold">Total:</span>
              <span class="font-weight-bold text-h6 primary--text" data-testid="total">
                ${{ formatPrice(cart.totals.total) }}
              </span>
            </div>

            <v-btn
              color="primary"
              large
              block
              :disabled="cart.items.length === 0"
              @click="proceedToCheckout"
              data-testid="checkout-button"
            >
              Proceed to Checkout
            </v-btn>
            
            <v-btn
              outlined
              large
              block
              class="mt-2"
              @click="$router.push('/products')"
            >
              Continue Shopping
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
  </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Cart',
  data () {
    return {
      updating: false,
      snackbar: {
        show: false,
        message: '',
        color: 'success'
      }
    }
  },
  computed: {
    ...mapGetters(['user', 'cart'])
  },
  async mounted () {
    if (this.user) {
      await this.loadCart()
    }
  },
  methods: {
    ...mapActions(['loadCart', 'updateCartItem', 'removeFromCart']),
    
    formatPrice (price) {
      return parseFloat(price).toFixed(2)
    },
    
    async updateQuantity (productId, newQuantity) {
      if (newQuantity < 1) {
        await this.removeItem(productId)
        return
      }
      
      this.updating = true
      try {
        await this.updateCartItem({ productId, quantity: newQuantity })
        await this.loadCart() // Refresh cart data
        this.showMessage('Cart updated successfully', 'success')
      } catch (error) {
        console.error('Failed to update cart:', error)
        this.showMessage('Failed to update cart', 'error')
      } finally {
        this.updating = false
      }
    },
    
    async removeItem (productId) {
      this.updating = true
      try {
        await this.removeFromCart(productId)
        await this.loadCart() // Refresh cart data
        this.showMessage('Item removed from cart', 'success')
      } catch (error) {
        console.error('Failed to remove item:', error)
        this.showMessage('Failed to remove item', 'error')
      } finally {
        this.updating = false
      }
    },
    
    proceedToCheckout () {
      this.$router.push('/checkout')
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
.cart-item:last-child .v-divider {
  display: none;
}

.v-card.sticky {
  position: sticky;
  top: 100px;
}
</style>
