<template>
  <v-app>
    <v-navigation-drawer
      v-model="drawer"
      :clipped="$vuetify.breakpoint.lgAndUp"
      app
    >
      <v-list dense>
        <v-list-item link to="/">
          <v-list-item-action>
            <v-icon>mdi-home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Home</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-item link to="/products">
          <v-list-item-action>
            <v-icon>mdi-shopping</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Products</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-item v-if="user" link to="/orders">
          <v-list-item-action>
            <v-icon>mdi-clipboard-list</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>My Orders</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      app
      color="primary"
      dark
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title class="headline text-uppercase">
        <span>Mini</span>
        <span class="font-weight-light">Mart</span>
      </v-toolbar-title>
      <v-spacer />

      <v-btn icon @click="$router.push('/cart')" data-testid="cart-button">
        <v-badge :content="cartItemCount" :value="cartItemCount > 0" color="red">
          <v-icon>mdi-cart</v-icon>
        </v-badge>
      </v-btn>

      <v-menu v-if="user" offset-y>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
            data-testid="user-menu-button"
          >
            <v-icon>mdi-account</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="$router.push('/profile')">
            <v-list-item-title>Profile</v-list-item-title>
          </v-list-item>
          <v-list-item @click="logout">
            <v-list-item-title>Logout</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-btn v-else text @click="$router.push('/login')" data-testid="login-button">
        Login
      </v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>

    <v-footer app>
      <span>&copy; {{ new Date().getFullYear() }} MiniMart SDET Test</span>
    </v-footer>
  </v-app>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  name: 'App',
  data: () => ({
    drawer: false
  }),
  computed: {
    ...mapGetters(['user', 'cartItemCount'])
  },
  methods: {
    ...mapActions(['logout'])
  }
}
</script>
