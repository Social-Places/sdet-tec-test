<template>
  <v-card class="mx-auto" max-width="344" hover @click="viewProduct">
    <v-img
      :src="product.image_url"
      height="200px"
      :alt="product.name"
    />

    <v-card-title>
      <router-link 
        :to="`/products/${product.id}`" 
        class="text-decoration-none primary--text"
        data-testid="product-name-link"
      >
        {{ product.name }}
      </router-link>
    </v-card-title>

    <v-card-subtitle class="pb-0">
      {{ product.category_name }}
    </v-card-subtitle>

    <v-card-text class="text--primary">
      <div class="text-h6 mt-2">${{ formatPrice(product.price) }}</div>
    </v-card-text>

    <v-card-actions @click.stop>
      <v-text-field
        v-model.number="quantity"
        type="number"
        min="1"
        :max="product.stock_quantity"
        label="Quantity"
        dense
        hide-details="auto"
        style="max-width: 100px"
        data-testid="quantity-input"
        @click.stop
      />
      
      <v-spacer />
      
      <v-btn
        :disabled="product.stock_quantity === 0 || loading"
        color="primary"
        size="small"
        @click.stop="handleAddToCart"
        :loading="loading"
        data-testid="add-to-cart-button"
      >
        Buy
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      quantity: 1,
      loading: false
    }
  },
  methods: {
    ...mapActions(['addToCart']),
    
    async handleAddToCart () {
      if (!this.$store.getters.user) {
        this.$router.push('/login')
        return
      }
      
      this.loading = true
      try {
        await this.addToCart({
          productId: this.product.id,
          quantity: this.quantity
        })
        this.$emit('added-to-cart')
      } catch (error) {
        console.error('Failed to add to cart:', error)
      } finally {
        this.loading = false
      }
    },

    viewProduct () {
      this.$router.push(`/products/${this.product.id}`)
    },

    formatPrice (price) {
      return parseFloat(price)
    }
  }
}
</script>
