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
        entity: {
            entity: 'Entity',
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
      results: {
        legend: 'User\'s List',
        name: 'Name',
        created_at: 'Creation Date',
        updated_at: 'Update At',
        email: 'Email',
        actions: 'Actions',
        organization: 'Organization',
        errors: 'Something went wrong.',
        loading: 'Loading...'
      }
    },
  }
};
