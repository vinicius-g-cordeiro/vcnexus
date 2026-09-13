

// const paymentsOptions = { label: t('header.links.payments.payments'), children: [], icon: 'bi bi-cash-coin' };
// const paymentsOptionsUrls = {
//     'payments.view': { label: t('header.links.payments.list'), href: '/payments/list/', icon: 'bi bi-receipt-cutoff' },
//     'payments.new': { label: t('header.links.payments.new'), href: '/payments/new/', icon: 'bi bi-cash' },
//     'payments.wallet': { label: t('header.links.payments.wallet'), href: '/payments/wallet/', icon: 'bi bi-wallet' },
// };
// paymentsOptions.children = Object.entries(paymentsOptionsUrls)
//     .filter((permission) => authStore.hasPermission(permission))
//     .map(([, item]) => item)


// const storeOptions = { label: t('header.links.store.store'), children: [], icon: 'bi bi-shop' };
// const storeOptionsUrls = {
//     'store.view': { label: t('header.links.store.list'), href: '/store/list/', icon: 'bi bi-clipboard-data' },
//     'store.orders': { label: t('header.links.store.orders'), href: '/store/orders/', icon: 'bi bi-cart4' },
//     'store.suppliers': { label: t('header.links.store.suppliers'), href: '/store/suppliers/', icon: 'bi bi-truck' },
//     'store.deliveries': { label: t('header.links.store.deliveries'), href: '/store/deliveries/', icon: 'bi bi-box-seam' },
//     'store.inventory': { label: t('header.links.store.inventory'), href: '/store/inventory/', icon: 'bi bi-cart-check' },
// };
// storeOptions.children = Object.entries(storeOptionsUrls)
//     .filter((permission) => authStore.hasPermission(permission))
//     .map(([, item]) => item)


// const productsOptions = { label: t('header.links.products.products'), children: [], icon: 'bi bi-box-seam' };
// const productsOptionsUrls = {
//     'products.view': { label: t('header.links.products.list'), href: '/products/list/', icon: 'bi bi-list-stars' },
//     'products.new': { label: t('header.links.products.new'), href: '/products/new/', icon: 'bi bi-box2' },
//     'products.stock': { label: t('header.links.products.stock'), href: '/products/stock/', icon: 'bi bi-boxes' },
// };
// productsOptions.children = Object.entries(productsOptionsUrls)
//     .filter((permission) => authStore.hasPermission(permission))
//     .map(([, item]) => item)


export default {
  header:{
    dropdown:{
      profile: 'Profile',
      settings: 'Settings',
      logout: 'Logout',
      login: 'Login',
      register: 'Register',
      search: 'Search'
    },
    links: {
        home: 'Home',
        about: 'About',
        payments: {
          payments: 'Payments',
          list: 'List',
          new: 'New',
          wallet: 'Wallet',
        },
        store: {
          store: 'Store',
          list: 'List',
          orders: 'Orders',
          suppliers: 'Suppliers',
          deliveries: 'Deliveries',
          inventory: 'Inventory',
        },
        products: {
          products: 'Products',
          list: 'List',
          new: 'New',
          stock: 'Manage Stock',
        },
        schedule: {
            schedule: 'Schedule',
            calendar: 'Calendar',
            list: 'List',
            new: 'New',
        },
        tasks: {
            tasks: 'Tasks',
            new: 'New',
            list: 'List'
        },
        users: {
            users: 'Users',
            new: 'New',
            list: 'List',
            documents: 'Documents',
            reports: 'Reports',
        },
        tenants: {
            tenants: 'Tenant',
            new: 'New',
            list: 'List',
            reports: 'Reports',
        }
    },
    searchbar: 'Search',
  },
  errors: {
    404: {
      code: "ERROR 404",
      titlePrefix: "Page ",
      titleHighlight: "not",
      titleSuffix: " found",
      description:
        "The page you're looking for doesn't exist or may have been moved.",
      back: "Back to dashboard",
    },
  },
  users: {
    list: {
      search: {
        legend: "Users - Search",
        search: 'Search',
        order: {
          label: 'Order',
        },
        active: {
          label: 'Active',
          active: 'Active',
          deactivated: 'Deactivated' 
        },
        blocked: {
          label: 'Blocked',
          blocked: 'Blocked',
          unblocked: 'Un-blocked' 
        },
        order_by: 'Order by',
        searchbtn: 'Search',
        clear: 'Clear'
      },
      actions: {
        new: 'New',
        reports: 'Reports',
        documents: 'Documents'
      },
      results: {
        legend: 'User\'s List',
        loading: 'Loading...',
        errors: 'Something went wrong.',
        active: 'Active',
        deactivated: 'Deactivated',
        blocked: 'Blocked',
        headers : {
          userInfo: 'User',
          organization: 'Organization',
          status: 'Status',
          actions: 'Actions',
          created_at: 'Created at',
          updated_at: 'Updated at',
          blocked_at: 'Blocked at',
          last_login: 'Last Login',
        },
        actions: {
          view: 'View',
          edit: 'Edit',
          delete: 'Deactivate',
          activate: 'Activate',
          block: 'Block',
          unblock: 'Unblock',
        }
      }
    },
  },
  tenants: {
    list: {
      search: {
        legend: "Tenants - Search",
        search: 'Search',
        order: {
          label: 'Order',
        },
        active: {
          label: 'Active',
          active: 'Active',
          deactivated: 'Deactivated' 
        },
        order_by: 'Order by',
        searchbtn: 'Search',
        clear: 'Clear'
      },
      actions: {
        new: 'New',
        reports: 'Reports',
        documents: 'Documents',
        users: 'Users'
      },
      results: {
        legend: 'Tenants List',
        loading: 'Loading...',
        errors: 'Something went wrong.',
        active: 'Active',
        deactivated: 'Deactivated',
        blocked: 'Blocked',
        headers : {
          userInfo: 'Tenant',
          organization: 'Organization',
          status: 'Status',
          actions: 'Actions',
          created_at: 'Created at',
          updated_at: 'Updated at',
          blocked_at: 'Blocked at',
          last_login: 'Last Login',
        },
        actions: {
          view: 'View',
          edit: 'Edit',
          delete: 'Deactivate',
          activate: 'Activate',
          block: 'Block',
          unblock: 'Unblock',
        }
      }
    },
  }
};
