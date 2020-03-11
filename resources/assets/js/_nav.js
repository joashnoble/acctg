export default {
  items: [
    {
      name: 'Dashboard',
      url: '/dashboard',
      icon: 'icon-speedometer'
    },
    {
      name: 'References',
      url: '/references',
      icon: 'icon-book-open',
      rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'Departments',
          url: '/references/departments',
          rights: '2-5',
          icon: 'icon-list'
        },
        {
          name: 'Charges',
          url: '/references/charges',
          rights: '3-9',
          icon: 'icon-energy'
        },
        {
          name: 'Categories',
          url: '/references/categories',
          rights: '4-13',
          icon: 'icon-folder'
        },
        {
          name: 'Locations',
          url: '/references/locations',
          rights: '5-17',
          icon: 'icon-location-pin'
        },
        {
          name: 'Contract Types',
          url: '/references/contracttypes',
          rights: '6-21',
          icon: 'icon-docs'
        }
        ,
        {
          name: 'Check Types',
          url: '/references/checktypes',
          rights: '7-25',
          icon: 'icon-credit-card'
        }
        ,
        {
          name: 'Nature Of Business',
          url: '/references/natureofbusiness',
          rights: '8-29',
          icon: 'icon-basket-loaded'
        },
        {
          name: 'Billing Period',
          url: '/references/billingperiods',
          rights: '9-33',
          icon: 'icon-calendar'
        }


      ]
    },
    {
      name: 'Transactions',
      url: '/transactions',
      icon: 'icon-note',
      rights: [1,13,14,15,16],
      children:[
        {
          name: 'Tenants',
          url: '/transactions/tenants',
          rights: '1-1',
          icon: 'icon-user-follow'
        },
        {
          name: 'Contracts',
          url: '/transactions/contracts',
          rights: '13-46',
          icon: 'icon-envelope-letter'
        },
        {
          name: 'Billing',
          url: '/transactions/billing',
          rights: '14-50',
          icon: 'icon-briefcase'
        },
        {
          name: 'Payment',
          url: '/transactions/payment',
          rights: '15-55',
          icon: 'icon-wallet'
        },
        {
          name: 'Adjustment',
          url: '/transactions/adjustment',
          rights: '16-59',
          icon: 'icon-cursor-move'
        },
        {
          name: 'Charge Slip',
          url: '/transactions/chargeslip',
          rights: '20-73',
          icon: 'icon-cursor-move'
        }
      ]
    },
    {
      name: 'Accounting',
      url: '/accounting',
      icon: 'icon-calculator',
      rights: [18,19],
      children:[
        {
          name: 'AR Billing',
          url: '/accounting/ar_billing',
          rights: '18-64',
          icon: 'icon-briefcase'
        },
        {
          name: 'AR Payment',
          url: '/accounting/ar_payment',
          rights: '19-66',
          icon: 'icon-wallet'
        },
      ]
    },
    {
      name: 'Reports',
      url: '/reports',
      icon: 'icon-doc',
      rights: [17],
      children:[
        {
          name: 'Tenant Per Sqm Rate',
          url: '/reports/tenant_per_sqm_rate',
          icon: 'icon-layers'
        },
        {
          name: 'Tenants Master List',
          url: '/reports/tenants_master_list',
          icon: 'icon-layers'
        },
        {
          name: 'Contracts Master List',
          url: '/reports/contracts_master_list',
          icon: 'icon-layers'
        },
        {
          name: 'Billing Forecast',
          url: '/reports/billing_forecast',
          icon: 'icon-layers'
        },
        {
          name: 'Rental Rates and Charges',
          url: '/reports/rental_and_charges',
          icon: 'icon-layers'
        },
        {
          name: 'Collection Report',
          url: '/reports/collection_report',
          icon: 'icon-layers'
        },
      ]
    },
    {
      name: 'Accounts',
      url: '/accounts',
      icon: 'icon-wrench',
      rights: [10,11,12],
      children:[
        {
          name: 'Users',
          url: '/accounts/users',
          rights: '10-37',
          icon: 'icon-user'
        },
        {
          name: 'User Group',
          url: '/accounts/user_groups',
          rights: '11-41',
          icon: 'icon-people'
        },
        {
          name: 'Company Settings',
          url: '/accounts/company_settings',
          rights: '12-45',
          icon: 'icon-settings'
        }
      ]
    },
    // {
    //   name: 'Components',
    //   url: '/components/buttons',
    //   icon: 'icon-puzzle',
    //   children: [
    //     {
    //       name: 'Buttons',
    //       url: '/components/buttons',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Social Buttons',
    //       url: '/components/social-buttons',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Cards',
    //       url: '/components/cards',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Forms',
    //       url: '/components/forms',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Modals',
    //       url: '/components/modals',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Switches',
    //       url: '/components/switches',
    //       icon: 'icon-puzzle'
    //     },
    //     {
    //       name: 'Tables',
    //       url: '/components/tables',
    //       icon: 'icon-puzzle'
    //     }
    //   ]
    // },
    // {
    //   name: 'Icons',
    //   url: '/icons',
    //   icon: 'icon-star',
    //   children: [
    //     {
    //       name: 'Font Awesome',
    //       url: '/icons/font-awesome',
    //       icon: 'icon-star'
    //     },
    //     {
    //       name: 'Simple Line Icons',
    //       url: '/icons/simple-line-icons',
    //       icon: 'icon-star'
    //     }
    //   ]
    // },
    // {
    //   name: 'Widgets',
    //   url: '/widgets',
    //   icon: 'icon-calculator',
    //   badge: {
    //     variant: 'danger',
    //     text: 'NEW'
    //   }
    // },
    // {
    //   name: 'Charts',
    //   url: '/charts',
    //   icon: 'icon-pie-chart'
    // },
    // {
    //   divider: true
    // },
    // {
    //   title: true,
    //   name: 'Extras'
    // },
    // {
    //   name: 'Pages',
    //   url: '/pages',
    //   icon: 'icon-star',
    //   children: [
    //     {
    //       name: 'Login',
    //       url: '/pages/login',
    //       icon: 'icon-star'
    //     },
    //     {
    //       name: 'Register',
    //       url: '/pages/register',
    //       icon: 'icon-star'
    //     },
    //     {
    //       name: 'Error 404',
    //       url: '/pages/404',
    //       icon: 'icon-star'
    //     },
    //     {
    //       name: 'Error 500',
    //       url: '/pages/500',
    //       icon: 'icon-star'
    //     }
    //   ]
    // }
  ],
}
