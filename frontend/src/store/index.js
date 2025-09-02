import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    user: null,
    cart: {
      items: [],
      totals: { subtotal: 0, tax: 0, total: 0 }
    },
    products: [],
    orders: [],
    loading: false
  },
  
  mutations: {
    SET_USER (state, user) {
      state.user = user
    },
    SET_CART (state, cart) {
      state.cart = cart
    },
    SET_PRODUCTS (state, products) {
      state.products = products
    },
    SET_ORDERS (state, orders) {
      state.orders = orders
    },
    SET_LOADING (state, loading) {
      state.loading = loading
    },
    LOGOUT (state) {
      state.user = null
      state.cart = { items: [], totals: { subtotal: 0, tax: 0, total: 0 } }
      state.orders = []
    }
  },
  
  actions: {
    async login ({ commit }, credentials) {
      const response = await axios.post('/api/users/login', credentials)
      if (response.data.success) {
        commit('SET_USER', response.data.user)
        await this.dispatch('loadCart')
      }
      return response.data
    },
    
    async register ({ commit }, userData) {
      const response = await axios.post('/api/users/register', userData)
      return response.data
    },
    
    logout ({ commit }) {
      commit('LOGOUT')
    },
    
    async loadCart ({ commit, state }) {
      if (!state.user) return
      
      const response = await axios.get(`/api/cart/${state.user.id}`)
      if (response.data.success) {
        commit('SET_CART', response.data.data)
      }
    },
    
    async addToCart ({ dispatch, state }, { productId, quantity }) {
      if (!state.user) {
        throw new Error('User must be logged in')
      }
      
      await axios.post(`/api/cart/${state.user.id}/add`, {
        product_id: productId,
        quantity
      })
      
      await dispatch('loadCart')
    },
    
    async updateCartItem ({ dispatch, state }, { productId, quantity }) {
      if (!state.user) {
        throw new Error('User must be logged in')
      }
      
      await axios.put(`/api/cart/${state.user.id}/update`, {
        product_id: productId,
        quantity
      })
      
      await dispatch('loadCart')
    },
    
    async removeFromCart ({ dispatch, state }, productId) {
      if (!state.user) {
        throw new Error('User must be logged in')
      }
      
      await axios.delete(`/api/cart/${state.user.id}/remove/${productId}`)
      await dispatch('loadCart')
    },
    
    async loadProducts ({ commit }) {
      commit('SET_LOADING', true)
      try {
        const response = await axios.get('/api/products')
        if (response.data.success) {
          commit('SET_PRODUCTS', response.data.data)
        }
      } finally {
        commit('SET_LOADING', false)
      }
    },
    
    async loadOrders ({ commit, state }) {
      if (!state.user) return
      
      const response = await axios.get(`/api/users/${state.user.id}/orders`)
      if (response.data.success) {
        commit('SET_ORDERS', response.data.data)
      }
    }
  },
  
  getters: {
    user: state => state.user,
    cart: state => state.cart,
    cartItemCount: state => {
      return state.cart.items.reduce((total, item) => total + parseInt(item.quantity), 0)
    },
    products: state => state.products,
    orders: state => state.orders,
    loading: state => state.loading
  }
})
