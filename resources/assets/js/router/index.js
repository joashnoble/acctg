import Vue from 'vue'
import Router from 'vue-router'

// Containers
import Full from '@/containers/Full'

import Dashboard from '@/views/Dashboard'

// Views - Pages
import Page404 from '@/views/pages/Page404'
import Page500 from '@/views/pages/Page500'
import Login from '@/views/pages/Login'
import Logout from '@/views/pages/Logout'
import Register from '@/views/pages/Register'

// REFERENCES
import categories from '@/views/references/Categories'
import departments from '@/views/references/Departments'
import units from '@/views/references/Units'
import locations from '@/views/references/Locations'
import brands from '@/views/references/Brands'
import banks from '@/views/references/Banks'
import accountclasses from '@/views/references/AccountClasses'
import accounttitles from '@/views/references/AccountTitles'

// MASTERFILES
import products from '@/views/masterfiles/Products'
import suppliers from '@/views/masterfiles/Suppliers'
import customers from '@/views/masterfiles/Customers'
import salespersons from '@/views/masterfiles/Salespersons'


import generalconfiguration from '@/views/settings/GeneralConfiguration'
import generaljournal from '@/views/financing/GeneralJournals'
import cashdisbursement from '@/views/financing/CashDisbursements'
import accountreceivables from '@/views/financing/AccountReceivables'
import accountpayables from '@/views/financing/AccountPayables'


import store from '../store'
Vue.use(Router)

const router = new Router({
  mode: 'hash', // Demo is living in GitHub.io, so required!
  linkActiveClass: 'open active',
  scrollBehavior: () => ({ y: 0 }),
  routes: [
    {
      path: '/',
      redirect: 'dashboard',
      name: 'Home',
      component: Full,
      children: [
        {
          path: 'dashboard',
          name: 'Dashboard',
          component: Dashboard,
          meta: { requiresAuth: true },
        },
        {
          path: 'references',
          name: 'References',
          component: {
            render (c) { return c('router-view') }
          },
          children: [
            {
              path: 'categories',
              name: 'Categories',
              component: categories,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'departments',
              name: 'Departments',
              component: departments,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'units',
              name: 'Units',
              component: units,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'locations',
              name: 'Locations',
              component: locations,
              // meta: {requiresAuth: true, rights: '4-13'}
            },

            {
              path: 'brands',
              name: 'Brands',
              component: brands,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'banks',
              name: 'Banks',
              component: banks,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'accountclasses',
              name: 'Account Classes',
              component: accountclasses,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'accounttitles',
              name: 'Chart of Accounts',
              component: accounttitles,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
          ]
        },
        {
          path: 'masterfiles',
          name: 'Masterfiles',
          component: {
            render (c) { return c('router-view') }
          },
          children: [
            {
              path: 'products',
              name: 'Products',
              component: products,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'suppliers',
              name: 'Suppliers',
              component: suppliers,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'customers',
              name: 'Customers',
              component: customers,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'salespersons',
              name: 'Salespersons',
              component: salespersons,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
          ]
        }, // END OF MASTERFILES
        {
          path: 'financing',
          name: 'Financing',
          component: {
            render (c) { return c('router-view') }
          },
          children: [
            {
              path: 'generaljournal',
              name: 'General Journal',
              component: generaljournal,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'cashdisbursement',
              name: 'Cash Disbursement',
              component: cashdisbursement,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'accountpayables',
              name: 'Accounts Payable',
              component: accountpayables,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
            {
              path: 'accountreceivables',
              name: 'Accounts Receivables',
              component: accountreceivables,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
          ]
        }, // END OF FINANCING
        {
          path: 'settings',
          name: 'Settings',
          component: {
            render (c) { return c('router-view') }
          },
          children: [
            {
              path: 'generalconfiguration',
              name: 'General',
              component: generalconfiguration,
              // meta: {requiresAuth: true, rights: '4-13'}
            },
          ]
        }, // END OF MASTERFILES
      ]
    },
    {
      path: '/register',
      name: 'Register',
      component: Register
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/logout',
      name: 'Logout',
      component: Logout
    },
    {
      path: '*',
      name:'404', 
      component: Page404
    },
  ]
})
export default router
router.beforeEach((to, from, next) => {

  //check if user has rights on this route
  if(to.matched.some(route => route.meta.rights)){
    var right = false
    var row = store.state.rights.filter(right => 
      right.right_code == to.meta.rights
    )

    if(row.length > 0){
      right = true
    }

    if(!right){
      next({name: from.name})
      return
    }
  }

  // check if the route requires authentication and user is not logged in
  if (to.matched.some(route => route.meta.requiresAuth) && !store.state.isLoggedIn) {
    // redirect to login page
    next({ name: 'Login' })
    return
  }

// if logged in redirect to dashboard
  if(to.path === '/login' && store.state.isLoggedIn) {
      next({name: from.name})
      return
  }

next()
})
