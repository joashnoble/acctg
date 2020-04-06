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
        },
        {
          name: 'Cash Disbursement',
          url: '/financing/cashdisbursement',
          // rights: '4-13',
        },
        {
          name: 'Accounts Payable',
          url: '/financing/accountpayables',
          // rights: '4-13',
        },
        {
          name: 'Accounts Receivable',
          url: '/financing/accountreceivables',
          // rights: '4-13',
          icon: ''
        },
        {
          name: 'Petty Cash Journal',
          url: '/financing/pettycashjournals',
          // rights: '4-13',
        },
        {
          name: 'Cash Receipts',
          url: '/financing/cashreceipts',
          // rights: '4-13',
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
        },
        {
          name: 'Departments',
          url: '/references/departments',
          // rights: '4-13',
        },
        {
          name: 'Units',
          url: '/references/units',
          // rights: '4-13',
        },
        {
          name: 'Locations',
          url: '/references/locations',
          // rights: '4-13',
        },
        {
          name: 'Brands',
          url: '/references/brands',
          // rights: '4-13',
        },
        {
          name: 'Banks',
          url: '/references/banks',
          // rights: '4-13',
        },
        {
          name: 'Account Classes',
          url: '/references/accountclasses',
          // rights: '4-13',
        },
        {
          name: 'Chart of Accounts',
          url: '/references/accounttitles',
          // rights: '4-13',
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
        },
        {
          name: 'Suppliers',
          url: '/masterfiles/suppliers',
          // rights: '4-13',
        },
        {
          name: 'Customers',
          url: '/masterfiles/customers',
          // rights: '4-13',
        },
        {
          name: 'Salespersons',
          url: '/masterfiles/salespersons',
          // rights: '4-13',
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
        },
      ]
    }, // END OF SETTINGS
  ],
}
