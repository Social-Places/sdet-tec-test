<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="display-1 text-center">Products</h1>
      </v-col>
    </v-row>

    <!-- Filters and Search -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-text-field
          v-model="searchQuery"
          label="Search products..."
          prepend-inner-icon="mdi-magnify"
          outlined
          dense
          hide-details="auto"
          clearable
          data-testid="search-input"
          @input="onSearchInput"
        />
      </v-col>
      <v-col cols="12" md="3">
        <v-select
          v-model="selectedCategory"
          :items="categoryOptions"
          label="Category"
          outlined
          hide-details="auto"
          dense
          clearable
          data-testid="category-filter"
          @change="filterProducts"
        />
      </v-col>
      <v-col cols="12" md="3">
        <v-select
          v-model="sortBy"
          :items="sortOptions"
          label="Sort by"
          outlined
          hide-details="auto"
          dense
          data-testid="sort-select"
          @change="sortProducts"
        />
      </v-col>
      <v-col cols="12" md="2" class="d-flex align-center">
        <v-btn
          color="primary"
          outlined
          block
          @click="clearFilters"
          data-testid="clear-filters-button"
        >
          Clear Filters
        </v-btn>
      </v-col>
    </v-row>

    <!-- Loading State -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center">
        <v-progress-circular indeterminate color="primary" size="64" />
      </v-col>
    </v-row>

    <!-- Products Grid -->
    <v-row v-else-if="filteredProducts.length > 0">
      <v-col
        v-for="product in paginatedProducts"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <ProductCard :product="product" @added-to-cart="showSuccessMessage" />
      </v-col>
    </v-row>

    <!-- No Products Found -->
    <v-row v-else>
      <v-col cols="12" class="text-center">
        <v-icon size="64" color="grey lighten-1">mdi-package-variant</v-icon>
        <h3 class="headline grey--text mt-4">No products found</h3>
        <p class="subtitle-1 grey--text">
          Try adjusting your search or filters
        </p>
      </v-col>
    </v-row>

    <!-- Pagination -->
    <v-row v-if="totalPages > 1" class="mt-8">
      <v-col cols="12" class="d-flex justify-center">
        <v-pagination
          v-model="currentPage"
          :length="totalPages"
          :total-visible="7"
          data-testid="pagination"
          @input="onPageChange"
        />
      </v-col>
    </v-row>

    <!-- Success Snackbar -->
    <v-snackbar v-model="snackbar" timeout="3000" color="success">
      Product added to cart!
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import ProductCard from '@/components/ProductCard.vue'

export default {
  name: 'Products',
  components: {
    ProductCard
  },
  data () {
    return {
      searchQuery: '',
      selectedCategory: null,
      sortBy: 'name',
      currentPage: 1,
      itemsPerPage: 12,
      snackbar: false,
      searchTimeout: null,
      categoryOptions: [
        { text: 'Electronics', value: 'Electronics' },
        { text: 'Books', value: 'Books' },
        { text: 'Clothing', value: 'Clothing' }
      ],
      sortOptions: [
        { text: 'Name (A-Z)', value: 'name' },
        { text: 'Name (Z-A)', value: 'name_desc' },
        { text: 'Price (Low to High)', value: 'price' },
        { text: 'Price (High to Low)', value: 'price_desc' }
      ]
    }
  },
  computed: {
    ...mapGetters(['products', 'loading']),
    filteredProducts () {
      let filtered = [...this.products]

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(product =>
          product.name.toLowerCase().includes(query) ||
          product.description.toLowerCase().includes(query)
        )
      }

      // Apply category filter
      if (this.selectedCategory) {
        filtered = filtered.filter(product =>
          product.category_name === this.selectedCategory
        )
      }

      return filtered
    },
    sortedProducts () {
      const products = [...this.filteredProducts]
      
      switch (this.sortBy) {
        case 'name':
          return products.sort((a, b) => a.name.localeCompare(b.name))
        case 'name_desc':
          return products.sort((a, b) => b.name.localeCompare(a.name))
        case 'price':
          return products.sort((a, b) => parseFloat(a.price) - parseFloat(b.price))
        case 'price_desc':
          return products.sort((a, b) => parseFloat(b.price) - parseFloat(a.price))
        default:
          return products
      }
    },
    paginatedProducts () {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.sortedProducts.slice(start, end)
    },
    totalPages () {
      return Math.ceil(this.sortedProducts.length / this.itemsPerPage)
    }
  },
  async mounted () {
    await this.loadProducts()
    
    // Check for category filter from URL
    const category = this.$route.query.category
    if (category) {
      this.selectedCategory = category
    }
  },
  methods: {
    ...mapActions(['loadProducts']),
    onSearchInput () {
      // Debounce search to avoid too many API calls
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.currentPage = 1 // Reset to first page when searching
      }, 300)
    },
    filterProducts () {
      this.currentPage = 1 // Reset to first page when filtering
    },
    sortProducts () {
      this.currentPage = 1 // Reset to first page when sorting
    },
    onPageChange () {
      // Scroll to top when page changes
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    clearFilters () {
      this.searchQuery = ''
      this.selectedCategory = null
      this.sortBy = 'name'
      this.currentPage = 1
    },
    showSuccessMessage () {
      this.snackbar = true
    }
  }
}
</script>
