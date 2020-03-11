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
      // rights: [2,3,4,5,6,7,8,9],
      children:[
        {
          name: 'Categories',
          url: '/references/categories',
          rights: '4-13',
          icon: 'icon-folder'
        }
      ]
    },
  ],
}
