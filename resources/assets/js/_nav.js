export default {
  items: [
    {
      name: 'Dashboard',
      url: '/dashboard',
      icon: 'icon-speedometer'
    },
    {
      name: 'Financing',
      url: '/financing',
      icon: 'fa fa-archive',
      // rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'General Journal',
          url: '/financing/generaljournal',
          // rights: '4-13',
          icon: 'fa fa-book'
        },
        {
          name: 'Cash Disbursement',
          url: '/financing/cashdisbursement',
          // rights: '4-13',
          icon: 'fa fa-book'
        },
        {
          name: 'Accounts Payable',
          url: '/financing/accountpayables',
          // rights: '4-13',
          icon: 'fa fa-book'
        },
        {
          name: 'Accounts Receivable',
          url: '/financing/accountreceivables',
          // rights: '4-13',
          icon: 'fa fa-book'
        },
      ]
    }, // END OF FINANCING
    {
      name: 'References',
      url: '/references',
      icon: 'icon-book-open',
      // rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'Categories',
          url: '/references/categories',
          // rights: '4-13',
          icon: 'icon-folder'
        },
        {
          name: 'Departments',
          url: '/references/departments',
          // rights: '4-13',
          icon: 'icon-list'
        },
        {
          name: 'Units',
          url: '/references/units',
          // rights: '4-13',
          icon: 'fa fa-adjust'
        },
        {
          name: 'Locations',
          url: '/references/locations',
          // rights: '4-13',
          icon: 'icon-location-pin'
        },
        {
          name: 'Brands',
          url: '/references/brands',
          // rights: '4-13',
          icon: 'icon-folder'
        },
        {
          name: 'Banks',
          url: '/references/banks',
          // rights: '4-13',
          icon: 'fa fa-university'
        },
        {
          name: 'Account Classes',
          url: '/references/accountclasses',
          // rights: '4-13',
          icon: 'fa fa-bars'
        },
        {
          name: 'Chart of Accounts',
          url: '/references/accounttitles',
          // rights: '4-13',
          icon: 'fa fa-bars'
        },
      ]
    }, // END OF REFERENCES
    {
      name: 'Masterfiles',
      url: '/masterfiles',
      icon: 'fa fa-th-list',
      // rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'Products',
          url: '/masterfiles/products',
          // rights: '4-13',
          icon: 'fa fa-product-hunt'
        },
        {
          name: 'Suppliers',
          url: '/masterfiles/suppliers',
          // rights: '4-13',
          icon: 'fa fa-users'
        },
        {
          name: 'Customers',
          url: '/masterfiles/customers',
          // rights: '4-13',
          icon: 'fa fa-users'
        },
        {
          name: 'Salespersons',
          url: '/masterfiles/salespersons',
          // rights: '4-13',
          icon: 'fa fa-users'
        },
      ]
    }, // END OF MASTERFILES
    {
      name: 'Settings',
      url: '/settings',
      icon: 'fa fa-cogs',
      // rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'General',
          url: '/settings/generalconfiguration',
          // rights: '4-13',
          icon: 'fa fa-cog'
        },
      ]
    }, // END OF SETTINGS
  ],
}
