<template>
  <v-container>
    <v-row v-if="loading">
      <v-col cols="12" class="text-center">
        <v-progress-circular indeterminate color="primary" size="64" />
      </v-col>
    </v-row>

    <v-row v-else-if="error">
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-alert-circle</v-icon>
        <h3 class="headline grey--text mt-4">{{ error }}</h3>
        <v-btn color="primary" class="mt-4" @click="$router.push('/products')">
          Back to Products
        </v-btn>
      </v-col>
    </v-row>

    <v-row v-else-if="product">
      <v-col cols="12">
        <!-- Breadcrumbs -->
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 mb-4">
          <template v-slot:item="{ item }">
            <v-breadcrumbs-item
              :href="item.href"
              :disabled="item.disabled"
              @click="item.click"
            >
              {{ item.text }}
            </v-breadcrumbs-item>
          </template>
        </v-breadcrumbs>
      </v-col>

      <v-col cols="12" md="6">
        <v-img
          :src="product.image_url"
          :alt="product.name"
          height="400px"
          contain
          class="grey lighten-2"
        />
      </v-col>

      <v-col cols="12" md="6">
        <div class="product-details">
          <h1 class="display-1 mb-2" data-testid="product-name">
            {{ product.name }}
          </h1>
          
          <v-chip
            small
            color="primary"
            text-color="white"
            class="mb-4"
            data-testid="product-category"
          >
            {{ product.category_name }}
          </v-chip>

          <div class="display-2 primary--text mb-4" data-testid="product-price">
            ${{ formatPrice(product.price) }}
          </div>

          <div class="body-1 mb-6" data-testid="product-description">
            {{ product.description }}
          </div>

          <!-- Stock Status -->
          <div class="mb-4">
            <v-chip
              v-if="product.stock_quantity > 10"
              small
              color="success"
              text-color="white"
              data-testid="stock-status"
            >
              In Stock ({{ product.stock_quantity }} available)
            </v-chip>
            <v-chip
              v-else-if="product.stock_quantity > 0"
              small
              color="warning"
              text-color="white"
              data-testid="stock-status"
            >
              Limited Stock ({{ product.stock_quantity }} left)
            </v-chip>
            <v-chip
              v-else
              small
              color="error"
              text-color="white"
              data-testid="stock-status"
            >
              Out of Stock
            </v-chip>
          </div>

          <!-- Add to Cart Section -->
          <v-card outlined class="pa-4">
            <div class="d-flex align-center mb-4">
              <v-text-field
                v-model.number="quantity"
                type="number"
                min="1"
                :max="product.stock_quantity"
                label="Quantity"
                dense
                hide-details="auto"
                outlined
                style="max-width: 120px"
                class="mr-4"
                data-testid="quantity-input"
              />
              
              <v-btn
                large
                color="primary"
                :disabled="product.stock_quantity === 0 || !user"
                :loading="addingToCart"
                @click="handleAddToCart"
                data-testid="add-to-cart-button"
              >
                <v-icon left>mdi-cart-plus</v-icon>
                Add to Cart
              </v-btn>
            </div>

            <div v-if="!user" class="caption grey--text">
              <router-link to="/login">Login</router-link> to add items to cart
            </div>
            
            <div class="subtitle-2 mb-2">
              Total: <span class="primary--text font-weight-bold">
                ${{ formatPrice(product.price * quantity) }}
              </span>
            </div>
          </v-card>

          <!-- Product Specifications -->
          <v-expansion-panels class="mt-6" multiple>
            <v-expansion-panel>
              <v-expansion-panel-header>
                <template v-slot:default="{ open }">
                  <v-row no-gutters>
                    <v-col cols="4" class="d-flex justify-start">
                      <v-icon class="mr-2">mdi-information</v-icon>
                      Product Details
                    </v-col>
                  </v-row>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <div class="body-2">
                  <div class="mb-2">
                    <strong>Category:</strong> {{ product.category_name }}
                  </div>
                  <div class="mb-2">
                    <strong>Product ID:</strong> {{ product.id }}
                  </div>
                  <div class="mb-2">
                    <strong>Availability:</strong> 
                    {{ product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                  </div>
                </div>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel>
              <v-expansion-panel-header>
                <template v-slot:default="{ open }">
                  <v-row no-gutters>
                    <v-col cols="4" class="d-flex justify-start">
                      <v-icon class="mr-2">mdi-truck</v-icon>
                      Shipping Info
                    </v-col>
                  </v-row>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <div class="body-2">
                  <div class="mb-2">
                    <strong>Free shipping</strong> on orders over $50
                  </div>
                  <div class="mb-2">
                    <strong>Estimated delivery:</strong> 3-5 business days
                  </div>
                  <div class="mb-2">
                    <strong>Return policy:</strong> 30 days
                  </div>
                </div>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
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
  name: 'ProductDetail',
  props: {
    id: {
      type: [String, Number],
      required: true
    }
  },
  data () {
    return {
      product: null,
      loading: true,
      error: null,
      quantity: 1,
      addingToCart: false,
      snackbar: {
        show: false,
        message: '',
        color: 'success'
      }
    }
  },
  computed: {
    ...mapGetters(['user']),
    breadcrumbs () {
      return [
        {
          text: 'Home',
          disabled: false,
          href: '#',
          click: () => this.$router.push('/')
        },
        {
          text: 'Products',
          disabled: false,
          href: '#',
          click: () => this.$router.push('/products')
        },
        {
          text: this.product?.name || 'Loading...',
          disabled: true
        }
      ]
    }
  },
  async mounted () {
    await this.loadProduct()
  },
  watch: {
    id: {
      handler: 'loadProduct',
      immediate: false
    }
  },
  methods: {
    ...mapActions(['addToCart']),
    
    async loadProduct () {
      this.loading = true
      this.error = null
      
      try {
        const response = await this.$http.get(`/api/products/${this.id}`)
        
        if (response.data.success) {
          this.product = response.data.data
          // Reset quantity when product changes
          this.quantity = 1
        } else {
          this.error = 'Product not found'
        }
      } catch (error) {
        console.error('Failed to load product:', error)
        this.error = 'Failed to load product details'
      } finally {
        this.loading = false
      }
    },
    
    formatPrice (price) {
      return parseFloat(price).toFixed(2)
    },
    
    async handleAddToCart () {
      if (!this.user) {
        this.$router.push('/login')
        return
      }
      
      if (this.quantity > this.product.stock_quantity) {
        this.showMessage('Not enough stock available', 'error')
        return
      }
      
      this.addingToCart = true
      
      try {
        await this.addToCart({
          productId: this.product.id,
          quantity: this.quantity
        })
        
        this.showMessage(`${this.product.name} added to cart!`, 'success')
      } catch (error) {
        console.error('Failed to add to cart:', error)
        this.showMessage('Failed to add item to cart', 'error')
      } finally {
        this.addingToCart = false
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
.product-details {
  height: 100%;
}

.v-expansion-panel-content >>> .v-expansion-panel-content__wrap {
  padding: 16px 24px 16px 24px;
}
</style>
