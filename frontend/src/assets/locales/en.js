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
        schedule: {
            schedule: 'Schedule',
            calendar: 'Calendar',
            list: 'List'
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
        users: 'Users'
      },

      results: {
        legend: 'Tenants List',
        loading: 'Loading...',
        errors: 'Something went wrong.',
        empty: 'No results found..',
        headers : {
          tenantInfo: 'Tenant',
          status: 'Status',
          actions: 'Actions',
          created_at: 'Created',
          updated_at: 'Updated',
          deleted_at: 'Deleted',
          deleted_by: 'Deleted By',
          last_login: 'Last Login',
        },
        actions: {
          view: 'View',
          edit: 'Edit',
          delete: 'Deactivate',
          activate: 'Activate',
          block: 'Block',
        }
      }
    },
  }
};
