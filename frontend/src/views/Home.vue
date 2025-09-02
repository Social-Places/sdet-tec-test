<template>
  <div>
    <!-- Hero Section -->
    <v-container fluid class="pa-0">
      <v-img
        height="400px"
        src="https://plus.unsplash.com/premium_photo-1664305035108-0cb11aeb0baf?q=80&w=1977&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
      >
        <v-container fill-height>
          <v-row align="center" justify="center">
            <v-col cols="12" md="8" class="text-center">
              <h1 class="display-2 white--text mb-4">
                Welcome to MiniMart
              </h1>
              <p class="headline white--text mb-6">
                Your one-stop shop for electronics, books, and clothing
              </p>
              <v-btn
                large
                color="primary"
                @click="$router.push('/products')"
                data-testid="shop-now-button"
              >
                Shop Now
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-img>
    </v-container>

    <!-- Featured Categories -->
    <v-container class="py-12">
      <v-row>
        <v-col cols="12" class="text-center mb-8">
          <h2 class="display-1 mb-4">Shop by Category</h2>
          <p class="subtitle-1 grey--text">
            Discover our wide range of products
          </p>
        </v-col>
      </v-row>

      <v-row>
        <v-col
          v-for="category in categories"
          :key="category.id"
          cols="12"
          md="4"
        >
          <v-card
            class="mx-auto category-card"
            max-width="400"
            @click="goToCategory(category.name)"
            hover
            data-testid="category-card"
          >
            <v-img
              height="200px"
              :src="category.image"
              :alt="category.name"
            />
            <v-card-title class="justify-center">
              {{ category.name }}
            </v-card-title>
            <v-card-text class="text-center">
              {{ category.description }}
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Featured Products -->
    <v-container class="py-12 grey lighten-5">
      <v-row>
        <v-col cols="12" class="text-center mb-8">
          <h2 class="display-1 mb-4">Featured Products</h2>
          <p class="subtitle-1 grey--text">
            Check out our most popular items
          </p>
        </v-col>
      </v-row>

      <v-row v-if="loading">
        <v-col cols="12" class="text-center">
          <v-progress-circular indeterminate color="primary" size="64" />
        </v-col>
      </v-row>

      <v-row v-else>
        <v-col
          v-for="product in featuredProducts"
          :key="product.id"
          cols="12"
          sm="6"
          md="3"
        >
          <ProductCard :product="product" @added-to-cart="showSuccessMessage" />
        </v-col>
      </v-row>

      <v-row class="mt-8">
        <v-col cols="12" class="text-center">
          <v-btn
            large
            outlined
            color="primary"
            @click="$router.push('/products')"
          >
            View All Products
          </v-btn>
        </v-col>
      </v-row>
    </v-container>

    <!-- Success Snackbar -->
    <v-snackbar v-model="snackbar" timeout="3000" color="success">
      Product added to cart!
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import ProductCard from '@/components/ProductCard.vue'

export default {
  name: 'Home',
  components: {
    ProductCard
  },
  data () {
    return {
      snackbar: false,
      categories: [
        {
          id: 1,
          name: 'Electronics',
          description: 'Latest gadgets and electronics',
          image: 'https://plus.unsplash.com/premium_photo-1681666713680-fb39c13070f3?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        },
        {
          id: 2,
          name: 'Books',
          description: 'Educational and entertainment reading',
          image: 'https://images.unsplash.com/photo-1517770413964-df8ca61194a6?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        },
        {
          id: 3,
          name: 'Clothing',
          description: 'Fashion and apparel for everyone',
          image: 'https://plus.unsplash.com/premium_photo-1679056835084-7f21e64a3402?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
        }
      ]
    }
  },
  computed: {
    ...mapGetters(['products', 'loading']),
    featuredProducts () {
      return this.products.slice(0, 4)
    }
  },
  async mounted () {
    await this.loadProducts()
  },
  methods: {
    ...mapActions(['loadProducts']),
    goToCategory (categoryName) {
      this.$router.push(`/products?category=${categoryName}`)
    },
    showSuccessMessage () {
      this.snackbar = true
    }
  }
}
</script>

<style scoped>
.category-card {
  cursor: pointer;
  transition: transform 0.2s;
}

.category-card:hover {
  transform: translateY(-4px);
}
</style>
