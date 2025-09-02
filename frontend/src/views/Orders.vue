<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="display-1 text-center">My Orders</h1>
      </v-col>
    </v-row>

    <v-row v-if="!user">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-account-alert</v-icon>
        <h3 class="headline grey--text mt-4">Please log in to view your orders</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/login')">
          Login
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else-if="loading">
      <v-col cols="12" class="text-center">
        <v-progress-circular indeterminate color="primary" size="64" />
      </v-col>
    </v-row>

    <v-row v-else-if="orders.length === 0">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-clipboard-list-outline</v-icon>
        <h3 class="headline grey--text mt-4">No orders yet</h3>
        <p class="subtitle-1 grey--text">
          When you place orders, they will appear here
        </p>
        <v-btn color="primary" class="mt-4" @click="$router.push('/products')">
          Start Shopping
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col cols="12">
        <v-card
          v-for="order in orders"
          :key="order.id"
          class="mb-4"
          data-testid="order-card"
        >
          <v-card-title class="d-flex justify-space-between align-center">
            <div>
              <h3>Order #{{ order.id }}</h3>
              <p class="body-2 grey--text ma-0">
                Placed on {{ formatDate(order.created_at) }}
              </p>
            </div>
            <v-chip
              :color="getStatusColor(order.status)"
              :text-color="getStatusTextColor(order.status)"
              small
            >
              {{ formatStatus(order.status) }}
            </v-chip>
          </v-card-title>

          <v-divider />

          <v-card-text>
            <v-row>
              <v-col cols="12" md="8">
                <h4 class="subtitle-1">Items:</h4>
                <p class="body-2">{{ order.items || 'Loading items...' }}</p>
                
                <h4 class="subtitle-1 mt-4">Shipping Address:</h4>
                <p class="body-2">{{ order.shipping_address }}</p>
              </v-col>
              <v-col cols="12" md="4" class="text-md-right">
                <div class="d-flex justify-space-between mb-1">
                  <span>Subtotal:</span>
                  <span>${{ calculateSubtotal(order.total_amount, order.tax_amount) }}</span>
                </div>
                <div class="d-flex justify-space-between mb-1">
                  <span>Tax:</span>
                  <span>${{ formatPrice(order.tax_amount) }}</span>
                </div>
                <div class="d-flex justify-space-between mb-3">
                  <span class="font-weight-bold">Total:</span>
                  <span class="font-weight-bold">${{ formatPrice(order.total_amount) }}</span>
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'Orders',
  computed: {
    ...mapGetters(['user', 'orders', 'loading'])
  },
  async mounted () {
    if (!this.user) {
      this.$router.push('/login')
      return
    }
    
    await this.loadOrders()
  },
  methods: {
    ...mapActions(['loadOrders']),
    
    formatPrice (price) {
      return parseFloat(price).toFixed(2)
    },
    
    calculateSubtotal (total, tax) {
      const subtotal = parseFloat(total) - parseFloat(tax)
      return this.formatPrice(subtotal)
    },
    
    formatDate (dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    
    formatStatus (status) {
      return status.charAt(0).toUpperCase() + status.slice(1)
    },
    
    getStatusColor (status) {
      const colors = {
        pending: 'orange',
        processing: 'blue',
        shipped: 'purple',
        delivered: 'green',
        cancelled: 'red'
      }
      return colors[status] || 'grey'
    },
    
    getStatusTextColor (status) {
      return status === 'pending' ? 'black' : 'white'
    }
    
  }
}
</script>
